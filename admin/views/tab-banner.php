<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>Layout</h2>
<table class="form-table" role="presentation">
	<tr><th>Position</th><td>
		<fieldset class="vul-layouts">
			<?php foreach ( array( 'box-left' => 'Card, bottom left', 'box-right' => 'Card, bottom right', 'bar' => 'Full-width bar', 'modal' => 'Centred modal' ) as $k => $l ) : ?>
				<label class="vul-layout-opt"><input type="radio" name="<?php echo esc_attr( $n( 'layout' ) ); ?>" value="<?php echo esc_attr( $k ); ?>" <?php checked( $s['layout'], $k ); ?>> <span class="vul-layout-thumb vul-layout-thumb--<?php echo esc_attr( $k ); ?>"><i></i></span> <?php echo esc_html( $l ); ?></label>
			<?php endforeach; ?>
		</fieldset>
	</td></tr>
	<tr><th>Options</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'overlay' ) ); ?>" value="1" <?php checked( $s['overlay'] ); ?>> Dim the page behind the banner (always on for the modal layout)</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'show_reject' ) ); ?>" value="1" <?php checked( $s['show_reject'] ); ?>> Show a "Reject" button on the first layer <span class="description">(UK ICO expects rejecting to be as easy as accepting: leave this on)</span></label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'show_manage' ) ); ?>" value="1" <?php checked( $s['show_manage'] ); ?>> Show a "Manage preferences" link on the first layer</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'show_icon' ) ); ?>" value="1" <?php checked( $s['show_icon'] ); ?>> Show the cookie icon</label>
	</td></tr>
</table>

<h2>Wording</h2>
<table class="form-table" role="presentation">
	<tr><th><label for="text_title">Banner title</label></th><td><input type="text" class="regular-text" id="text_title" name="<?php echo esc_attr( $n( 'text_title' ) ); ?>" value="<?php echo esc_attr( $s['text_title'] ); ?>"></td></tr>
	<tr><th><label for="text_body">Banner text</label></th><td><textarea class="large-text" rows="3" id="text_body" name="<?php echo esc_attr( $n( 'text_body' ) ); ?>"><?php echo esc_textarea( $s['text_body'] ); ?></textarea><p class="description"><code>{cookie_link}</code> becomes a link to the Cookie Policy page. Merge fields such as <code>{company}</code> also work.</p></td></tr>
	<tr><th><label for="text_cookie_link">Cookie link text</label></th><td><input type="text" class="regular-text" id="text_cookie_link" name="<?php echo esc_attr( $n( 'text_cookie_link' ) ); ?>" value="<?php echo esc_attr( $s['text_cookie_link'] ); ?>"></td></tr>
	<tr><th><label for="text_accept">Accept button</label></th><td><input type="text" class="regular-text" id="text_accept" name="<?php echo esc_attr( $n( 'text_accept' ) ); ?>" value="<?php echo esc_attr( $s['text_accept'] ); ?>"></td></tr>
	<tr><th><label for="text_reject">Reject button</label></th><td><input type="text" class="regular-text" id="text_reject" name="<?php echo esc_attr( $n( 'text_reject' ) ); ?>" value="<?php echo esc_attr( $s['text_reject'] ); ?>"></td></tr>
	<tr><th><label for="text_manage">Manage link</label></th><td><input type="text" class="regular-text" id="text_manage" name="<?php echo esc_attr( $n( 'text_manage' ) ); ?>" value="<?php echo esc_attr( $s['text_manage'] ); ?>"></td></tr>
	<tr><th><label for="text_prefs_title">Preferences title</label></th><td><input type="text" class="regular-text" id="text_prefs_title" name="<?php echo esc_attr( $n( 'text_prefs_title' ) ); ?>" value="<?php echo esc_attr( $s['text_prefs_title'] ); ?>"></td></tr>
	<tr><th><label for="text_prefs_body">Preferences intro</label></th><td><textarea class="large-text" rows="2" id="text_prefs_body" name="<?php echo esc_attr( $n( 'text_prefs_body' ) ); ?>"><?php echo esc_textarea( $s['text_prefs_body'] ); ?></textarea></td></tr>
	<tr><th><label for="text_save">Save button</label></th><td><input type="text" class="regular-text" id="text_save" name="<?php echo esc_attr( $n( 'text_save' ) ); ?>" value="<?php echo esc_attr( $s['text_save'] ); ?>"></td></tr>
	<tr><th><label for="text_always_on">"Always on" label</label></th><td><input type="text" class="regular-text" id="text_always_on" name="<?php echo esc_attr( $n( 'text_always_on' ) ); ?>" value="<?php echo esc_attr( $s['text_always_on'] ); ?>"></td></tr>
	<tr><th><label for="text_close">Close label</label></th><td><input type="text" class="regular-text" id="text_close" name="<?php echo esc_attr( $n( 'text_close' ) ); ?>" value="<?php echo esc_attr( $s['text_close'] ); ?>"></td></tr>
	<tr><th><label for="text_embed_blocked">Blocked embed message</label></th><td><textarea class="large-text" rows="2" id="text_embed_blocked" name="<?php echo esc_attr( $n( 'text_embed_blocked' ) ); ?>"><?php echo esc_textarea( $s['text_embed_blocked'] ); ?></textarea><p class="description"><code>{category}</code> is replaced with the category name.</p></td></tr>
	<tr><th><label for="text_embed_button">Blocked embed button</label></th><td><input type="text" class="regular-text" id="text_embed_button" name="<?php echo esc_attr( $n( 'text_embed_button' ) ); ?>" value="<?php echo esc_attr( $s['text_embed_button'] ); ?>"></td></tr>
</table>

<h2>Re-open link</h2>
<p>Put a "Cookie preferences" link in the footer with any of these:</p>
<ul class="vul-code-list">
	<li>Shortcode: <code>[vu_consent_link text="Cookie preferences"]</code></li>
	<li>Any element with the attribute: <code>&lt;a href="#" data-vul-open&gt;Cookie preferences&lt;/a&gt;</code></li>
	<li>PHP: <code>echo vul_preferences_link( 'Cookie preferences' );</code></li>
	<li>JS: <code>vul.open()</code></li>
</ul>
