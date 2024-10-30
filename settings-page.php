<?php
global $wp_rewrite;

if ( $_GET['updated'] == 'true' || $_GET['settings-updated'] == 'true' ) {
	$success = DFHotlink::activate();

	if ( !$success ) {
		?><div class="error"><p>There was a problem writing .htaccess. See <a href="#df_output">below</a> for manual instructions.</p></div><?php
	}
}

if ( ! got_mod_rewrite() ) {
	?><div class="error"><p>mod_rewrite is not available.</p></div><?php
} ?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php echo $GLOBALS['title']; ?></h2>

<p>Prevent remote websites from linking directly to your content.</p>

<form method="post" action="options.php">
	<?php settings_fields( 'df_hotlink' ); ?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				Protect these extensions

			</th>
			<td nowrap>
<?php

$selected_extensions = DFHotlink::selected_extensions();
$common_extensions = DFHotlink::common_extensions();

echo '<table>';
echo '<tr valign="top"><td>';
$col_height = ceil( count( $common_extensions ) / 4 );
$c = 0;
foreach ( $common_extensions as $ext ) {
	if ( false !== $key = array_search($ext, $selected_extensions) ) {
		$checked = ' checked';
		unset( $selected_extensions[ $key ] );
	} else {
		$checked = '';
	}

	if ( $c !== 0 && $c % $col_height === 0 && $c != count( $common_extensions ) ) {
		echo '</td><td>';
	}
	$c++;
	echo '<input type="checkbox" id="df_ext_' . esc_attr($ext) . '" name="df_common_extensions[]" value="' . esc_attr($ext) . '"'
		. $checked
		. ' /> '
		. '<label for="df_ext_' . esc_attr($ext). '">' . esc_html($ext) . "</label><br />\n";
}

// Whatever wasn't on the list is added to the custom list.
$custom_extensions = implode( "\n", $selected_extensions );
?>
						</td>
						<td>
							<h3 style="margin-top: 0;">Custom Extensions</h3>
							<span class="description">One per line, no periods</span><br />
							<textarea name="hotlink_extensions" rows="4" cols="4"><?php echo esc_html($custom_extensions); ?></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Allow direct linking</th>
			<td>
				<input type="checkbox" name="hotlink_allowdirectlink" value="1" <?php checked( get_option('hotlink_allowdirectlink'), 'checked'); ?> />
				<span class="description">Enable to support links from outside of a browser. This includes links in e-mail, streaming MP3 apps, manually typed addresses, and bookmarks.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Allowed Domains<br /><span class="description">One per line</span></th>
			<td><?php echo DFHotlink::clean_url(); ?> (the current website is always included)<br />
				<textarea name="hotlink_domains" rows="10" cols="30"><?php echo esc_html( implode("\n", get_option('hotlink_domains') ) ); ?></textarea><br />
				<span class="description">Links from all other websites will see an error page.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Use content placeholders</th>
			<td>
				<input type="checkbox" name="hotlink_allowdirectlink" value="1" <?php checked( get_option('hotlink_usecontentplaceholders'), 'checked'); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Content placeholder folder</th>
			<td>
				<input type="text" name="hotlink_contentplaceholderstorage" value="<?php esc_attr( get_option('hotlink_contentplaceholderstorage') ); ?>" />
				<span class="description">Default is <code>wp-content/hotlink-placeholders</code>.</span>
			</td>
		</tr>
	</table>

<?php if ( ( $_GET['updated'] == 'true' || $_GET['settings-updated'] == 'true') && ! $success  ) { ?>
	<h3 id="df_output">Output</h3>
	<p>.htaccess is not writeable or mod_rewrite is not detected. Please add the following into your .htaccess file.</p>
	<p><textarea rows="9" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_html( DFHotlink::hotlink_rules_text() ); ?></textarea></p>
<?php } ?>

	<p class="submit">
		<input type="submit" value="Save Changes" class="button-primary" name="Submit">
	</p>
</form>

</div>
