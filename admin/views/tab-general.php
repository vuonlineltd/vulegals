<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>Company details</h2>
<p class="description">Used as merge fields in the banner text, Cookie Policy and Gravity Forms consent text: <code>{company}</code> <code>{contact_email}</code> <code>{address}</code> <code>{company_number}</code> <code>{ico_number}</code> <code>{privacy_url}</code> <code>{cookie_url}</code> <code>{updated}</code> <code>{version}</code>.</p>
<table class="form-table" role="presentation">
	<tr><th><label for="company_name">Legal entity name</label></th><td><input type="text" class="regular-text" id="company_name" name="<?php echo esc_attr( $n( 'company_name' ) ); ?>" value="<?php echo esc_attr( $s['company_name'] ); ?>" placeholder="e.g. Vu Online Limited T/A Vu Digital"></td></tr>
	<tr><th><label for="company_address">Registered address</label></th><td><textarea class="regular-text" rows="3" id="company_address" name="<?php echo esc_attr( $n( 'company_address' ) ); ?>"><?php echo esc_textarea( $s['company_address'] ); ?></textarea></td></tr>
	<tr><th><label for="company_number">Company number</label></th><td><input type="text" class="regular-text" id="company_number" name="<?php echo esc_attr( $n( 'company_number' ) ); ?>" value="<?php echo esc_attr( $s['company_number'] ); ?>"></td></tr>
	<tr><th><label for="ico_number">ICO registration</label></th><td><input type="text" class="regular-text" id="ico_number" name="<?php echo esc_attr( $n( 'ico_number' ) ); ?>" value="<?php echo esc_attr( $s['ico_number'] ); ?>"></td></tr>
	<tr><th><label for="contact_email">Privacy contact email</label></th><td><input class="regular-text" type="email" id="contact_email" name="<?php echo esc_attr( $n( 'contact_email' ) ); ?>" value="<?php echo esc_attr( $s['contact_email'] ); ?>"></td></tr>
</table>

<h2>Policy pages</h2>
<table class="form-table" role="presentation">
	<tr><th><label for="privacy_page">Privacy Policy page</label></th><td><?php wp_dropdown_pages( array( 'name' => $n( 'privacy_page' ), 'id' => 'privacy_page', 'selected' => (int) $s['privacy_page'], 'show_option_none' => '— Select —', 'option_none_value' => 0 ) ); ?></td></tr>
	<tr><th><label for="cookie_page">Cookie Policy page</label></th><td>
		<?php wp_dropdown_pages( array( 'name' => $n( 'cookie_page' ), 'id' => 'cookie_page', 'selected' => (int) $s['cookie_page'], 'show_option_none' => '— Select —', 'option_none_value' => 0 ) ); ?>
		<p><label><input type="checkbox" name="<?php echo esc_attr( $n( 'policy_inline_panel' ) ); ?>" value="1" <?php checked( $s['policy_inline_panel'] ); ?>> Include a "Change your cookie settings" panel (switches + save) in the generated policy</label> <span class="description">Also available anywhere as <code>[vu_cookie_settings]</code>.</span></p>
		<p class="description">The page should contain the <strong>Cookie Policy</strong> block or the <code>[vu_cookie_policy]</code> shortcode (Oxygen: drop the shortcode in a Shortcode element). Or <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_create_page' ), 'vul_create_page' ) ); ?>">create one automatically</a>.</p>
	</td></tr>
</table>

<h2>Consent</h2>
<table class="form-table" role="presentation">
	<tr><th><label for="policy_version">Policy version</label></th><td>
		<input type="text" class="small-text" id="policy_version" name="<?php echo esc_attr( $n( 'policy_version' ) ); ?>" value="<?php echo esc_attr( $s['policy_version'] ); ?>">
		<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_bump_version' ), 'vul_bump_version' ) ); ?>">Bump version &amp; re-ask everyone</a>
		<p class="description">Stored in the consent cookie. Change it after adding new cookies or categories and every visitor is asked again.</p>
	</td></tr>
	<tr><th><label for="policy_updated">Policy last updated</label></th><td><input type="date" id="policy_updated" name="<?php echo esc_attr( $n( 'policy_updated' ) ); ?>" value="<?php echo esc_attr( $s['policy_updated'] ); ?>"></td></tr>
	<tr><th><label for="cookie_expiry_days">Remember choice for</label></th><td><input class="small-text" type="number" min="1" max="365" id="cookie_expiry_days" name="<?php echo esc_attr( $n( 'cookie_expiry_days' ) ); ?>" value="<?php echo esc_attr( $s['cookie_expiry_days'] ); ?>"> days <span class="description">(ICO suggests re-asking at least every 6 months for optional cookies)</span></td></tr>
	<tr><th>Consent log</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'log_consent' ) ); ?>" value="1" <?php checked( $s['log_consent'] ); ?>> Record each consent decision (anonymous ID, hashed IP, choices, policy version) as proof of consent</label>
		<p><label for="log_retention_days">Keep for</label> <input class="small-text" type="number" min="0" id="log_retention_days" name="<?php echo esc_attr( $n( 'log_retention_days' ) ); ?>" value="<?php echo esc_attr( $s['log_retention_days'] ); ?>"> days (0 = forever)</p>
	</td></tr>
</table>
