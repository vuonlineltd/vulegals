<?php
/**
 * Banner + preferences modal markup. Hidden until JS decides it is needed.
 * Override by copying to yourtheme/vu-legals/banner.php (filter: vul_banner_template).
 *
 * Available: $layout, $body (HTML), $cookie_url
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vul_cats = vul_categories();
?>
<div id="vul" class="vul vul--<?php echo esc_attr( $layout ); ?>" hidden data-vul-root>

	<div class="vul-banner" role="dialog" aria-modal="false" aria-labelledby="vul-banner-title" aria-describedby="vul-banner-body" hidden data-vul-banner>
		<div class="vul-banner__inner">
			<?php if ( vul_get( 'show_icon' ) ) : ?>
				<svg class="vul-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10c0-.7-.1-1.4-.2-2a3 3 0 0 1-3.3-3.3A3 3 0 0 1 15 3.8 10 10 0 0 0 12 2Z" stroke="currentColor" stroke-width="1.6"/><circle cx="8.5" cy="10.5" r="1.3" fill="currentColor"/><circle cx="13" cy="15.5" r="1.3" fill="currentColor"/><circle cx="8" cy="15" r="1" fill="currentColor"/><circle cx="14.5" cy="9.5" r="1" fill="currentColor"/></svg>
			<?php endif; ?>
			<div class="vul-banner__text">
				<p class="vul-title" id="vul-banner-title"><?php echo esc_html( vul_get( 'text_title' ) ); ?></p>
				<p class="vul-body" id="vul-banner-body"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput -- kses'd above. ?></p>
			</div>
			<div class="vul-actions">
				<button type="button" class="vul-btn vul-btn--primary" data-vul-accept-all><?php echo esc_html( vul_get( 'text_accept' ) ); ?></button>
				<?php if ( vul_get( 'show_reject' ) ) : ?>
					<button type="button" class="vul-btn vul-btn--secondary" data-vul-reject-all><?php echo esc_html( vul_get( 'text_reject' ) ); ?></button>
				<?php endif; ?>
				<?php if ( vul_get( 'show_manage' ) ) : ?>
					<button type="button" class="vul-btn vul-btn--link" data-vul-open><?php echo esc_html( vul_get( 'text_manage' ) ); ?></button>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="vul-overlay" hidden data-vul-overlay></div>

	<div class="vul-modal" role="dialog" aria-modal="true" aria-labelledby="vul-modal-title" hidden data-vul-modal>
		<div class="vul-modal__inner" data-vul-panel="modal">
			<div class="vul-modal__head">
				<p class="vul-title" id="vul-modal-title"><?php echo esc_html( vul_get( 'text_prefs_title' ) ); ?></p>
				<button type="button" class="vul-close" data-vul-close aria-label="<?php echo esc_attr( vul_get( 'text_close' ) ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
				</button>
			</div>
			<p class="vul-body"><?php echo wp_kses_post( vul_merge( vul_get( 'text_prefs_body' ) ) ); ?></p>

			<?php $vul_prefix = 'vul-modal-cat'; include VUL_DIR . 'templates/switches.php'; ?>

			<div class="vul-actions vul-modal__actions">
				<button type="button" class="vul-btn vul-btn--primary" data-vul-save><?php echo esc_html( vul_get( 'text_save' ) ); ?></button>
				<button type="button" class="vul-btn vul-btn--secondary" data-vul-accept-all><?php echo esc_html( vul_get( 'text_accept' ) ); ?></button>
				<?php if ( vul_get( 'show_reject' ) ) : ?>
					<button type="button" class="vul-btn vul-btn--link" data-vul-reject-all><?php echo esc_html( vul_get( 'text_reject' ) ); ?></button>
				<?php endif; ?>
			</div>
			<?php if ( $cookie_url ) : ?>
				<p class="vul-modal__foot"><a href="<?php echo esc_url( $cookie_url ); ?>"><?php echo esc_html( ucfirst( vul_get( 'text_cookie_link' ) ) ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
</div>
