<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap vul-admin">
	<h1>Consent log</h1>
	<p><?php echo esc_html( number_format_i18n( $count ) ); ?> records. Last 30 days:
		<?php foreach ( $stats as $src => $n ) : ?><span class="vul-pill"><?php echo esc_html( str_replace( '_', ' ', $src ) ); ?>: <?php echo esc_html( $n ); ?></span> <?php endforeach; ?>
	</p>
	<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_export_log' ), 'vul_export_log' ) ); ?>">Export CSV</a></p>
	<table class="widefat striped vul-table">
		<thead><tr><th>When (UTC)</th><th>Consent ID</th><th>Version</th><th>Source</th><th>Choices</th><th>Page</th></tr></thead>
		<tbody>
		<?php if ( ! $rows ) : ?><tr><td colspan="6">Nothing logged yet.</td></tr><?php endif; ?>
		<?php foreach ( $rows as $r ) : $c = json_decode( $r->categories, true ) ?: array(); ?>
			<tr>
				<td><?php echo esc_html( $r->created ); ?></td>
				<td><code><?php echo esc_html( substr( $r->consent_id, 0, 8 ) ); ?></code></td>
				<td><?php echo esc_html( $r->policy_version ); ?></td>
				<td><?php echo esc_html( str_replace( '_', ' ', $r->source ) ); ?></td>
				<td><?php foreach ( $c as $k => $v ) : ?><span class="vul-pill <?php echo $v ? 'vul-pill--on' : 'vul-pill--off'; ?>"><?php echo esc_html( $k ); ?></span> <?php endforeach; ?></td>
				<td><?php echo esc_html( str_replace( home_url(), '', $r->url ) ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
