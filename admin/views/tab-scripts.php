<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<h2>How scripts are held back</h2>
<p>Anything gated is output as <code>&lt;script type="text/plain" data-vul-consent="analytics"&gt;</code> and only executed once that category is granted. Four ways in, from least to most effort:</p>
<ol class="vul-steps">
	<li><strong>Google Tag Manager + Consent Mode</strong> (Integrations tab). GTM loads immediately, tags respect the consent signals. Nothing else to do.</li>
	<li><strong>Automatic detection.</strong> Known trackers (GA, Meta Pixel, Hotjar, Clarity, HubSpot, LinkedIn, YouTube, Vimeo, Maps…) are recognised by URL or inline code wherever they appear in the page, including Oxygen code blocks and snippet plugins.</li>
	<li><strong>Registered scripts.</strong> Paste snippets under <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . VUL_Registry::SCRIPT ) ); ?>">Vu Legals → Scripts</a> with a category.</li>
	<li><strong>Manual mapping</strong> below, for enqueued handles or URL fragments the detector doesn't know.</li>
</ol>
<table class="form-table" role="presentation">
	<tr><th>Automatic detection</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'auto_block' ) ); ?>" value="1" <?php checked( $s['auto_block'] ); ?>> Scan the final page output and gate known trackers automatically</label>
		<p class="description">Uses an output buffer on front-end requests. Turn off if it conflicts with another plugin doing the same.</p>
	</td></tr>
	<tr><th>Embeds</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'block_embeds' ) ); ?>" value="1" <?php checked( $s['block_embeds'] ); ?>> Replace YouTube, Vimeo and Google Maps iframes in content with a placeholder until consent</label>
		<p class="description">Detected embeds use the provider's own category (functional by default).</p>
	</td></tr>
	<tr><th><label for="handle_map">Enqueued script handles</label></th><td>
		<textarea class="large-text code" rows="5" id="handle_map" name="<?php echo esc_attr( $n( 'handle_map' ) ); ?>" spellcheck="false" placeholder="handle:category&#10;my-analytics:analytics&#10;chat-widget:functional"><?php echo esc_textarea( $s['handle_map'] ); ?></textarea>
		<p class="description">One per line, <code>handle:category</code>. Handles are what plugins pass to <code>wp_enqueue_script()</code>.</p>
	</td></tr>
	<tr><th><label for="block_patterns">URL / code patterns</label></th><td>
		<textarea class="large-text code" rows="5" id="block_patterns" name="<?php echo esc_attr( $n( 'block_patterns' ) ); ?>" spellcheck="false" placeholder="cdn.example.com/tracker.js:marketing&#10;crisp.chat:functional"><?php echo esc_textarea( $s['block_patterns'] ); ?></textarea>
		<p class="description">One per line, <code>fragment:category</code>. Matched case-insensitively against script <code>src</code>, iframe <code>src</code> and inline script contents.</p>
	</td></tr>
</table>
<h2>In templates</h2>
<pre class="vul-pre">&lt;?php echo vul_gate( '&lt;script src="https://example.com/widget.js"&gt;&lt;/script&gt;', 'marketing' ); ?&gt;
&lt;?php if ( vul_has_consent( 'analytics' ) ) { … } ?&gt;

// JS
document.addEventListener('vul:consent', e =&gt; console.log(e.detail.categories));
vul.has('marketing'); vul.open(); vul.on(cb);</pre>
