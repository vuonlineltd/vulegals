<?php
/**
 * Inline settings panel: lives on the Cookie Policy page (or anywhere via [vu_cookie_settings]).
 * Same switches as the modal; JS keeps both in sync and saves from whichever was used.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="vul vul-inline" data-vul-panel="inline" data-vul-inline>
	<div class="vul-inline__status" data-vul-status aria-live="polite"><?php echo esc_html( vul_get( 'text_inline_nochoice' ) ); ?></div>

	<?php $vul_prefix = 'vul-inline-cat'; include VUL_DIR . 'templates/switches.php'; ?>

	<div class="vul-actions">
		<button type="button" class="vul-btn vul-btn--primary" data-vul-save><?php echo esc_html( vul_get( 'text_save' ) ); ?></button>
		<button type="button" class="vul-btn vul-btn--secondary" data-vul-accept-all><?php echo esc_html( vul_get( 'text_accept' ) ); ?></button>
		<button type="button" class="vul-btn vul-btn--secondary" data-vul-reject-all><?php echo esc_html( vul_get( 'text_reject' ) ); ?></button>
	</div>
	<noscript><p class="vul-muted"><?php echo esc_html( vul_get( 'text_inline_noscript' ) ); ?></p></noscript>
</div>
