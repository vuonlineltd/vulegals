<?php
/**
 * Category switches. Included by the modal and by the inline settings panel.
 * Expects $vul_prefix (string) for unique input IDs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vul_cats   = vul_categories();
$vul_prefix = isset( $vul_prefix ) ? $vul_prefix : 'vul-cat';
?>
			<div class="vul-cats">
				<?php foreach ( $vul_cats as $slug => $cat ) : ?>
					<?php $locked = 'necessary' === $slug; ?>
					<div class="vul-cat">
						<div class="vul-cat__row">
							<label class="vul-cat__label" for="<?php echo esc_attr( $vul_prefix . '-' . $slug ); ?>"><?php echo esc_html( $cat['label'] ); ?></label>
							<?php if ( $locked ) : ?>
								<span class="vul-cat__always"><?php echo esc_html( vul_get( 'text_always_on' ) ); ?></span>
								<input type="checkbox" id="<?php echo esc_attr( $vul_prefix . '-' . $slug ); ?>" class="vul-switch" checked disabled aria-hidden="true" tabindex="-1">
							<?php else : ?>
								<input type="checkbox" id="<?php echo esc_attr( $vul_prefix . '-' . $slug ); ?>" class="vul-switch" role="switch" data-vul-cat="<?php echo esc_attr( $slug ); ?>">
							<?php endif; ?>
						</div>
						<p class="vul-cat__desc"><?php echo esc_html( $cat['description'] ); ?></p>
						<?php $vul_cookies = VUL_Registry::cookies( $slug ); ?>
						<?php if ( $vul_cookies ) : ?>
							<details class="vul-cat__details">
								<summary><?php echo esc_html( count( $vul_cookies ) ); ?> <?php echo 1 === count( $vul_cookies ) ? 'cookie' : 'cookies'; ?></summary>
								<ul>
									<?php foreach ( $vul_cookies as $c ) : ?>
										<li><code><?php echo esc_html( $c['name'] ); ?></code><?php if ( $c['provider'] ) : ?> <span class="vul-muted">· <?php echo esc_html( $c['provider'] ); ?></span><?php endif; ?><?php if ( $c['duration'] ) : ?> <span class="vul-muted">· <?php echo esc_html( $c['duration'] ); ?></span><?php endif; ?></li>
									<?php endforeach; ?>
								</ul>
							</details>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

