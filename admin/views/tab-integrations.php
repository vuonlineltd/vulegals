<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>Google Tag Manager &amp; Consent Mode v2</h2>
<table class="form-table" role="presentation">
	<tr><th>Consent Mode</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'consent_mode' ) ); ?>" value="1" <?php checked( $s['consent_mode'] ); ?>> Emit <code>gtag('consent','default', …)</code> in the head and <code>update</code> on every decision</label>
		<p class="description">Maps: analytics → <code>analytics_storage</code>; marketing → <code>ad_storage</code>, <code>ad_user_data</code>, <code>ad_personalization</code>; functional → <code>functionality_storage</code>, <code>personalization_storage</code>. Also pushes a <code>vul_consent</code> dataLayer event you can use as a GTM trigger.</p>
	</td></tr>
	<tr><th><label for="consent_mode_wait">wait_for_update</label></th><td><input class="small-text" type="number" id="consent_mode_wait" name="<?php echo esc_attr( $n( 'consent_mode_wait' ) ); ?>" value="<?php echo esc_attr( $s['consent_mode_wait'] ); ?>"> ms</td></tr>
	<tr><th>URL passthrough</th><td><label><input type="checkbox" name="<?php echo esc_attr( $n( 'url_passthrough' ) ); ?>" value="1" <?php checked( $s['url_passthrough'] ); ?>> Enable <code>url_passthrough</code> (passes ad click IDs through URLs when cookies are denied)</label></td></tr>
	<tr><th><label for="gtm_id">GTM container</label></th><td>
		<input type="text" class="regular-text" id="gtm_id" name="<?php echo esc_attr( $n( 'gtm_id' ) ); ?>" value="<?php echo esc_attr( $s['gtm_id'] ); ?>" placeholder="GTM-XXXXXXX"><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'gtm_load' ) ); ?>" value="1" <?php checked( $s['gtm_load'] ); ?>> Load GTM from here (immediately after the consent defaults, which is where Google wants it)</label>
		<p class="description">If GTM is already loaded by the theme or another plugin, leave this unticked. The consent defaults still go out first because they print at <code>wp_head</code> priority 0.</p>
	</td></tr>
</table>

<h2>Google Site Kit</h2>
<table class="form-table" role="presentation">
	<tr><th>Site Kit tags</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'sitekit_takeover' ) ); ?>" value="1" <?php checked( $s['sitekit_takeover'] ); ?>> Block Site Kit's own GA4 / Ads / GTM tags and its Consent Mode snippet so they can't race this plugin</label>
		<p class="description">Keep Site Kit for the dashboard reports; let Vu Legals load the tag (enter the GTM ID above, or register a gtag snippet under Scripts). <?php echo defined( 'GOOGLESITEKIT_VERSION' ) ? 'Site Kit is active on this site.' : 'Site Kit is not active on this site.'; ?></p>
	</td></tr>
</table>

<h2>WP Consent API</h2>
<p>If the <a href="https://wordpress.org/plugins/wp-consent-api/" target="_blank" rel="noopener">WP Consent API</a> plugin is installed, Vu Legals registers as the consent management plugin and publishes each decision to it (<code>functional</code>, <code>preferences</code>, <code>statistics</code>, <code>marketing</code>), so WooCommerce, Complianz-aware plugins and anything else that calls <code>wp_has_consent()</code> respects the banner. Nothing to configure.</p>

<h2>Gravity Forms</h2>
<table class="form-table" role="presentation">
	<tr><th>Consent field</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'gf_enable' ) ); ?>" value="1" <?php checked( $s['gf_enable'] ); ?>> Fill empty Consent field labels with the text below</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'gf_override' ) ); ?>" value="1" <?php checked( $s['gf_override'] ); ?>> Always override, even if the field has its own label</label>
		<?php if ( ! class_exists( 'GFForms' ) ) : ?><p class="description">Gravity Forms is not active on this site.</p><?php endif; ?>
	</td></tr>
	<tr><th><label for="gf_text">Consent text</label></th><td>
		<textarea class="large-text" rows="3" id="gf_text" name="<?php echo esc_attr( $n( 'gf_text' ) ); ?>"><?php echo esc_textarea( $s['gf_text'] ); ?></textarea>
		<p class="description">Merge fields work here. Also available in any GF field as <code>{vu_legals:privacy_url}</code>, <code>{vu_legals:company}</code>, <code>{vu_legals:contact_email}</code>.</p>
	</td></tr>
</table>
