<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap vul-admin">
	<h1>Scan &amp; presets</h1>
	<p>Fetches your homepage and reports which known third-party services it loads. Add their cookies to the registry with one click, then fill in anything custom under <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . VUL_Registry::COOKIE ) ); ?>">Cookies</a>.</p>
	<form method="post">
		<?php wp_nonce_field( 'vul_scan' ); ?>
		<p><button class="button button-primary" name="vul_scan" value="1">Scan homepage</button></p>
	</form>

	<?php if ( $result ) : ?>
		<?php if ( ! empty( $result['error'] ) ) : ?>
			<div class="notice notice-error"><p>Could not fetch the homepage: <?php echo esc_html( $result['error'] ); ?></p></div>
		<?php else : ?>
			<h2>Detected</h2>
			<?php if ( $result['providers'] ) : ?>
				<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_add_preset&provider=all&providers=' . implode( ',', $result['providers'] ) ), 'vul_add_preset' ) ); ?>">Add cookies for everything detected</a></p>
			<?php endif; ?>
			<table class="widefat striped vul-table">
				<thead><tr><th>Service</th><th>Category</th><th>Cookies</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $result['providers'] as $slug ) : $p = $providers[ $slug ]; ?>
					<tr>
						<td><strong><?php echo esc_html( $p['name'] ); ?></strong></td>
						<td><?php echo esc_html( $p['category'] ); ?></td>
						<td><?php echo esc_html( implode( ', ', array_column( $p['cookies'], 'name' ) ) ?: '—' ); ?></td>
						<td><?php if ( $p['cookies'] ) : ?><?php if ( in_array( $p['name'], $registered, true ) ) : ?><span class="vul-ok">In registry</span><?php else : ?><a class="button button-small" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_add_preset&provider=' . $slug ), 'vul_add_preset' ) ); ?>">Add</a><?php endif; ?><?php endif; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<h2>External scripts on the page</h2>
			<?php if ( $result['scripts'] ) : ?>
				<ul class="vul-code-list"><?php foreach ( $result['scripts'] as $src ) : ?><li><code><?php echo esc_html( $src ); ?></code> <?php $m = VUL_Providers::match( $src ); echo $m ? '<span class="vul-ok">' . esc_html( $providers[ $m ]['name'] ) . '</span>' : '<span class="vul-unknown">unknown: add a pattern under Script gating if it sets cookies</span>'; ?></li><?php endforeach; ?></ul>
			<?php else : ?><p>None.</p><?php endif; ?>
			<h2>Iframes</h2>
			<?php if ( $result['iframes'] ) : ?>
				<ul class="vul-code-list"><?php foreach ( $result['iframes'] as $src ) : ?><li><code><?php echo esc_html( $src ); ?></code></li><?php endforeach; ?></ul>
			<?php else : ?><p>None.</p><?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>

	<h2>All presets</h2>
	<table class="widefat striped vul-table">
		<thead><tr><th>Service</th><th>Category</th><th>Cookies</th><th></th></tr></thead>
		<tbody>
		<?php foreach ( $providers as $slug => $p ) : if ( ! $p['cookies'] ) { continue; } ?>
			<tr>
				<td><strong><?php echo esc_html( $p['name'] ); ?></strong></td>
				<td><?php echo esc_html( $p['category'] ); ?></td>
				<td><?php echo esc_html( implode( ', ', array_column( $p['cookies'], 'name' ) ) ); ?></td>
				<td><?php if ( in_array( $p['name'], $registered, true ) ) : ?><span class="vul-ok">In registry</span><?php else : ?><a class="button button-small" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_add_preset&provider=' . $slug ), 'vul_add_preset' ) ); ?>">Add</a><?php endif; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description">Add your own presets with the <code>vul_providers</code> filter.</p>
</div>
