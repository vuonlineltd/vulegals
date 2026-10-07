<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>Style</h2>
<p class="description">The banner is styled entirely from CSS custom properties, so it can match any site. Pick colours here, or choose <em>Theme</em> and define the variables in your own stylesheet.</p>
<table class="form-table" role="presentation">
	<tr><th>Mode</th><td>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'style_mode' ) ); ?>" value="custom" <?php checked( $s['style_mode'], 'custom' ); ?>> Set colours below</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'style_mode' ) ); ?>" value="theme" <?php checked( $s['style_mode'], 'theme' ); ?>> Theme defines <code>--vul-*</code> variables (nothing emitted from here)</label>
	</td></tr>
	<?php
	$colours = array(
		'colour_bg'        => 'Background',
		'colour_fg'        => 'Text',
		'colour_muted'     => 'Muted text',
		'colour_accent'    => 'Accent (primary button, switches)',
		'colour_accent_fg' => 'Accent text',
		'colour_border'    => 'Border / divider',
	);
	foreach ( $colours as $k => $label ) : ?>
		<tr class="vul-if-custom"><th><label for="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></label></th><td><input type="text" class="vul-colour" id="<?php echo esc_attr( $k ); ?>" name="<?php echo esc_attr( $n( $k ) ); ?>" value="<?php echo esc_attr( $s[ $k ] ); ?>"></td></tr>
	<?php endforeach; ?>
	<tr class="vul-if-custom"><th><label for="radius">Corner radius</label></th><td><input type="text" class="small-text" id="radius" name="<?php echo esc_attr( $n( 'radius' ) ); ?>" value="<?php echo esc_attr( $s['radius'] ); ?>"> <span class="description">e.g. 12px, 0, 1rem</span></td></tr>
	<tr class="vul-if-custom"><th><label for="font">Font family</label></th><td><input type="text" class="regular-text" id="font" name="<?php echo esc_attr( $n( 'font' ) ); ?>" value="<?php echo esc_attr( $s['font'] ); ?>"> <span class="description"><code>inherit</code> uses the site's body font</span></td></tr>
	<tr><th><label for="custom_css">Extra CSS</label></th><td><textarea class="large-text code" rows="8" id="custom_css" name="<?php echo esc_attr( $n( 'custom_css' ) ); ?>" spellcheck="false"><?php echo esc_textarea( $s['custom_css'] ); ?></textarea>
	<p class="description">Appended after the variables. Available variables:</p>
	<pre class="vul-pre">:root {
  --vul-bg; --vul-fg; --vul-muted; --vul-accent; --vul-accent-fg; --vul-border;
  --vul-radius; --vul-btn-radius; --vul-font; --vul-text-size; --vul-shadow;
  --vul-switch-on; --vul-switch-off; --vul-overlay; --vul-z; --vul-gap; --vul-max;
}
/* Map to your theme tokens, e.g. */
:root { --vul-accent: var(--color-primary); --vul-radius: var(--radius-lg); }</pre>
	</td></tr>
</table>

<h2>Preview</h2>
<div class="vul-preview" id="vul-preview">
	<div class="vul-preview__card">
		<p class="vul-preview__title"><?php echo esc_html( $s['text_title'] ); ?></p>
		<p class="vul-preview__body"><?php echo esc_html( wp_strip_all_tags( str_replace( '{cookie_link}', $s['text_cookie_link'], $s['text_body'] ) ) ); ?></p>
		<div class="vul-preview__actions">
			<span class="vul-preview__btn vul-preview__btn--primary"><?php echo esc_html( $s['text_accept'] ); ?></span>
			<span class="vul-preview__btn vul-preview__btn--secondary"><?php echo esc_html( $s['text_reject'] ); ?></span>
			<span class="vul-preview__btn vul-preview__btn--link"><?php echo esc_html( $s['text_manage'] ); ?></span>
		</div>
	</div>
</div>
