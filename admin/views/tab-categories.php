<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>Consent categories</h2>
<p class="description">Labels and descriptions appear in the preferences panel and in the Cookie Policy. Disable a category you don't use and it disappears everywhere. "Pre-ticked" is off by default for a reason: under PECR/UK GDPR optional categories must be opt-in.</p>
<?php foreach ( $s['categories'] as $slug => $cat ) : $locked = 'necessary' === $slug; ?>
	<div class="vul-cat-card">
		<h3><?php echo esc_html( ucfirst( $slug ) ); ?> <code><?php echo esc_html( $slug ); ?></code></h3>
		<table class="form-table" role="presentation">
			<tr><th>Status</th><td>
				<?php if ( $locked ) : ?>
					<em>Always enabled and always on.</em>
				<?php else : ?>
					<label><input type="checkbox" name="<?php echo esc_attr( $n( "categories][$slug][enabled" ) ); ?>" value="1" <?php checked( $cat['enabled'] ); ?>> Enabled</label> &nbsp;
					<label><input type="checkbox" name="<?php echo esc_attr( $n( "categories][$slug][default_on" ) ); ?>" value="1" <?php checked( $cat['default_on'] ); ?>> Pre-ticked in preferences panel (not recommended)</label>
				<?php endif; ?>
			</td></tr>
			<tr><th><label>Label</label></th><td><input type="text" class="regular-text" name="<?php echo esc_attr( $n( "categories][$slug][label" ) ); ?>" value="<?php echo esc_attr( $cat['label'] ); ?>"></td></tr>
			<tr><th><label>Description</label></th><td><textarea class="large-text" rows="2" name="<?php echo esc_attr( $n( "categories][$slug][description" ) ); ?>"><?php echo esc_textarea( $cat['description'] ); ?></textarea></td></tr>
		</table>
	</div>
<?php endforeach; ?>
