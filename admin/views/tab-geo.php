<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>Where to show the banner</h2>
<table class="form-table" role="presentation">
	<tr><th>Mode</th><td>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'geo_mode' ) ); ?>" value="all" <?php checked( $s['geo_mode'], 'all' ); ?>> Everyone, everywhere (simplest, safest)</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'geo_mode' ) ); ?>" value="eu_uk" <?php checked( $s['geo_mode'], 'eu_uk' ); ?>> UK, EU, EEA and Switzerland only</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'geo_mode' ) ); ?>" value="custom" <?php checked( $s['geo_mode'], 'custom' ); ?>> Custom list of countries</label>
	</td></tr>
	<tr><th><label for="geo_countries">Countries</label></th><td><textarea class="large-text code" rows="2" id="geo_countries" name="<?php echo esc_attr( $n( 'geo_countries' ) ); ?>" placeholder="GB, IE, FR, DE"><?php echo esc_textarea( $s['geo_countries'] ); ?></textarea><p class="description">ISO 3166-1 alpha-2 codes, comma or space separated. Used with "Custom" only.</p></td></tr>
	<tr><th>Outside those regions</th><td>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'geo_outside' ) ); ?>" value="implied" <?php checked( $s['geo_outside'], 'implied' ); ?>> No banner, everything runs (implied consent)</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'geo_outside' ) ); ?>" value="hide" <?php checked( $s['geo_outside'], 'hide' ); ?>> No banner, optional cookies stay blocked (strict)</label>
	</td></tr>
	<tr><th>Detection</th><td>
		<p>Country comes from the CDN or host header when present: <code>CF-IPCountry</code> (Cloudflare), <code>X-Country-Code</code> (WP Engine GeoTarget), CloudFront, Vercel. No third-party lookups are made.</p>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'geo_timezone_fallback' ) ); ?>" value="1" <?php checked( $s['geo_timezone_fallback'] ); ?>> If no header is available, use the browser's timezone (Europe/*) as a fallback</label>
		<p class="description">Without the fallback and without a header, the banner is shown to everyone. Detected now for you: <code><?php echo esc_html( VUL_Geo::country() ?: 'no header' ); ?></code>. <?php if ( VUL_Geo::page_cache_active() ) : ?><strong>A page cache is active on this site, so the header is ignored and the decision is made in the browser (timezone fallback) or the banner is shown to everyone.</strong><?php else : ?>If a page cache is added later the plugin switches to the browser-side decision automatically.<?php endif; ?></p>
		<p class="description">Developers: <code>add_filter( 'vul_country', fn() => 'GB' );</code></p>
	</td></tr>
</table>
