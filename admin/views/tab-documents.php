<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $n = array( 'VUL_Admin', 'n' ); ?>
<p class="description vul-lede">Generates a Website Terms of Use, Privacy Policy and Accessibility Statement from the company details under General plus the fields below. Each clause can be overridden further down if a solicitor supplies wording. Vu Digital are not lawyers: these documents are a sound starting point for a typical UK brochure or lead-generation site, and regulated sectors should have them reviewed.</p>

<h2>Entity</h2>
<table class="form-table" role="presentation">
	<tr><th>Entity type</th><td>
		<select name="<?php echo esc_attr( $n( 'entity_type' ) ); ?>">
			<?php foreach ( array( 'limited' => 'Limited company', 'llp' => 'LLP', 'sole' => 'Sole trader', 'partnership' => 'Partnership', 'charity' => 'Registered charity' ) as $k => $l ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $s['entity_type'], $k ); ?>><?php echo esc_html( $l ); ?></option>
			<?php endforeach; ?>
		</select>
		<span class="description">Uses the legal name, number and address from General.</span>
	</td></tr>
	<tr><th>Jurisdiction</th><td>
		<select name="<?php echo esc_attr( $n( 'jurisdiction' ) ); ?>">
			<?php foreach ( array( 'england-wales' => 'England and Wales', 'scotland' => 'Scotland', 'northern-ireland' => 'Northern Ireland' ) as $k => $l ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $s['jurisdiction'], $k ); ?>><?php echo esc_html( $l ); ?></option>
			<?php endforeach; ?>
		</select>
	</td></tr>
	<tr><th><label for="company_phone">Telephone</label></th><td><input type="text" class="regular-text" id="company_phone" name="<?php echo esc_attr( $n( 'company_phone' ) ); ?>" value="<?php echo esc_attr( $s['company_phone'] ); ?>"></td></tr>
	<tr><th><label for="vat_number">VAT number</label></th><td><input type="text" class="regular-text" id="vat_number" name="<?php echo esc_attr( $n( 'vat_number' ) ); ?>" value="<?php echo esc_attr( $s['vat_number'] ); ?>"> <span class="description">Required on the site if VAT registered (E-Commerce Regulations 2002)</span></td></tr>
	<tr><th><label for="regulator">Regulator</label></th><td><input type="text" class="regular-text" id="regulator" name="<?php echo esc_attr( $n( 'regulator' ) ); ?>" value="<?php echo esc_attr( $s['regulator'] ); ?>" placeholder="e.g. the Solicitors Regulation Authority (SRA number 123456)"></td></tr>
	<tr><th><label for="professional_body">Professional body</label></th><td><input type="text" class="regular-text" id="professional_body" name="<?php echo esc_attr( $n( 'professional_body' ) ); ?>" value="<?php echo esc_attr( $s['professional_body'] ); ?>" placeholder="e.g. the General Chiropractic Council"></td></tr>
	<tr><th><label for="contact_page">Contact page</label></th><td><?php wp_dropdown_pages( array( 'name' => $n( 'contact_page' ), 'id' => 'contact_page', 'selected' => (int) $s['contact_page'], 'show_option_none' => '— None —', 'option_none_value' => 0 ) ); ?></td></tr>
</table>

<h2>What the site does</h2>
<p class="description">These switch clauses on and off.</p>
<table class="form-table" role="presentation">
	<tr><th>Features</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'site_forms' ) ); ?>" value="1" <?php checked( $s['site_forms'] ); ?>> Contact / enquiry forms</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'site_newsletter' ) ); ?>" value="1" <?php checked( $s['site_newsletter'] ); ?>> Email newsletter or marketing emails</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'site_accounts' ) ); ?>" value="1" <?php checked( $s['site_accounts'] ); ?>> User accounts / login</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'site_ugc' ) ); ?>" value="1" <?php checked( $s['site_ugc'] ); ?>> User-generated content (comments, reviews, uploads)</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'site_ecommerce' ) ); ?>" value="1" <?php checked( $s['site_ecommerce'] ); ?>> Sells goods or services online<?php echo class_exists( 'WooCommerce' ) ? ' <em>(WooCommerce detected: treated as on)</em>' : ''; ?></label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'site_deeplinks' ) ); ?>" value="1" <?php checked( $s['site_deeplinks'] ); ?>> Allow others to link to any page (untick to permit home-page links only)</label>
	</td></tr>
	<tr><th><label for="site_advice_area">Advice disclaimer</label></th><td><input type="text" class="regular-text" id="site_advice_area" name="<?php echo esc_attr( $n( 'site_advice_area' ) ); ?>" value="<?php echo esc_attr( $s['site_advice_area'] ); ?>" placeholder="e.g. your health, your legal position, your finances"><p class="description">If set, the Terms tell visitors to take professional advice before acting on anything relating to this.</p></td></tr>
</table>

<h2>Privacy Policy: personal data</h2>
<p class="description">One row per type of data. This builds both the "what we collect" and the "how long we keep it" tables.</p>
<table class="widefat vul-repeater" id="vul-data-rows">
	<thead><tr><th>Category</th><th>Examples</th><th>How collected / source</th><th>Purpose</th><th>Lawful basis</th><th>Retention</th><th></th></tr></thead>
	<tbody>
	<?php
	$rows  = is_array( $s['privacy_data'] ) ? $s['privacy_data'] : array();
	$rows[] = array(); // blank row for adding
	$bases = array( 'interests' => 'Legitimate interests', 'consent' => 'Consent', 'contract' => 'Contract', 'legal' => 'Legal obligation', 'vital' => 'Vital interests', 'public' => 'Public task' );
	foreach ( $rows as $i => $r ) :
		$f = fn( $k ) => $n( "privacy_data][$i][$k" );
		?>
		<tr class="<?php echo empty( $r['category'] ) ? 'vul-repeater__blank' : ''; ?>">
			<td><input type="text" name="<?php echo esc_attr( $f( 'category' ) ); ?>" value="<?php echo esc_attr( $r['category'] ?? '' ); ?>" placeholder="Contact details"></td>
			<td><input type="text" name="<?php echo esc_attr( $f( 'examples' ) ); ?>" value="<?php echo esc_attr( $r['examples'] ?? '' ); ?>" placeholder="name, email"></td>
			<td><input type="text" name="<?php echo esc_attr( $f( 'source' ) ); ?>" value="<?php echo esc_attr( $r['source'] ?? '' ); ?>" placeholder="Entered in our forms"></td>
			<td><input type="text" name="<?php echo esc_attr( $f( 'purpose' ) ); ?>" value="<?php echo esc_attr( $r['purpose'] ?? '' ); ?>" placeholder="To respond to you"></td>
			<td><select name="<?php echo esc_attr( $f( 'basis' ) ); ?>"><?php foreach ( $bases as $k => $l ) : ?><option value="<?php echo esc_attr( $k ); ?>" <?php selected( $r['basis'] ?? 'interests', $k ); ?>><?php echo esc_html( $l ); ?></option><?php endforeach; ?></select></td>
			<td><input type="text" name="<?php echo esc_attr( $f( 'retention' ) ); ?>" value="<?php echo esc_attr( $r['retention'] ?? '' ); ?>" placeholder="2 years"></td>
			<td><button type="button" class="button-link vul-repeater__remove" aria-label="Remove row">&times;</button></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<p><button type="button" class="button" id="vul-add-row">Add row</button> <span class="description">Rows with an empty category are ignored.</span></p>

<table class="form-table" role="presentation">
	<tr><th><label for="privacy_retention_default">Default retention</label></th><td><input type="text" class="large-text" id="privacy_retention_default" name="<?php echo esc_attr( $n( 'privacy_retention_default' ) ); ?>" value="<?php echo esc_attr( $s['privacy_retention_default'] ); ?>"></td></tr>
	<tr><th>Data location</th><td>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'privacy_transfers' ) ); ?>" value="uk" <?php checked( $s['privacy_transfers'], 'uk' ); ?>> UK only</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'privacy_transfers' ) ); ?>" value="uk_eea" <?php checked( $s['privacy_transfers'], 'uk_eea' ); ?>> UK and EEA</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'privacy_transfers' ) ); ?>" value="international" <?php checked( $s['privacy_transfers'], 'international' ); ?>> Includes outside UK/EEA (most sites: US-hosted analytics, email or CRM)</label>
	</td></tr>
	<tr><th><label for="privacy_transfer_safeguards">Transfer safeguards</label></th><td><input type="text" class="large-text" id="privacy_transfer_safeguards" name="<?php echo esc_attr( $n( 'privacy_transfer_safeguards' ) ); ?>" value="<?php echo esc_attr( $s['privacy_transfer_safeguards'] ); ?>"></td></tr>
	<tr><th><label for="privacy_processors">Service providers</label></th><td>
		<textarea class="large-text code" rows="5" id="privacy_processors" name="<?php echo esc_attr( $n( 'privacy_processors' ) ); ?>" placeholder="Name | what they do for us | where&#10;WP Engine | website hosting | UK / USA"><?php echo esc_textarea( $s['privacy_processors'] ); ?></textarea>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'privacy_processors_auto' ) ); ?>" value="1" <?php checked( $s['privacy_processors_auto'] ); ?>> Also list providers from the cookie registry automatically</label>
	</td></tr>
	<tr><th>Marketing</th><td>
		<label for="privacy_marketing_channels">Channels</label> <input type="text" id="privacy_marketing_channels" name="<?php echo esc_attr( $n( 'privacy_marketing_channels' ) ); ?>" value="<?php echo esc_attr( $s['privacy_marketing_channels'] ); ?>" placeholder="email"> &nbsp;
		<label for="privacy_marketing_optout">Opt-out</label> <input type="text" class="regular-text" id="privacy_marketing_optout" name="<?php echo esc_attr( $n( 'privacy_marketing_optout' ) ); ?>" value="<?php echo esc_attr( $s['privacy_marketing_optout'] ); ?>">
		<p class="description">Only used when "Email newsletter" is ticked above.</p>
	</td></tr>
	<tr><th>Other</th><td>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'privacy_automated' ) ); ?>" value="1" <?php checked( $s['privacy_automated'] ); ?>> We use automated decision-making or profiling</label><br>
		<label><input type="checkbox" name="<?php echo esc_attr( $n( 'privacy_children' ) ); ?>" value="1" <?php checked( $s['privacy_children'] ); ?>> Include a "not directed at children" clause</label>
	</td></tr>
</table>

<h2>Accessibility Statement</h2>
<table class="form-table" role="presentation">
	<tr><th><label for="a11y_wcag_level">Standard</label></th><td><input type="text" class="regular-text" id="a11y_wcag_level" name="<?php echo esc_attr( $n( 'a11y_wcag_level' ) ); ?>" value="<?php echo esc_attr( $s['a11y_wcag_level'] ); ?>"></td></tr>
	<tr><th>Status</th><td>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'a11y_status' ) ); ?>" value="full" <?php checked( $s['a11y_status'], 'full' ); ?>> Fully compliant</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'a11y_status' ) ); ?>" value="partial" <?php checked( $s['a11y_status'], 'partial' ); ?>> Partially compliant (honest default for most sites)</label><br>
		<label><input type="radio" name="<?php echo esc_attr( $n( 'a11y_status' ) ); ?>" value="non" <?php checked( $s['a11y_status'], 'non' ); ?>> Not compliant</label>
	</td></tr>
	<tr><th><label for="a11y_known_issues">Known issues</label></th><td><textarea class="large-text" rows="4" id="a11y_known_issues" name="<?php echo esc_attr( $n( 'a11y_known_issues' ) ); ?>" placeholder="One per line, e.g. Some older PDFs are not tagged for screen readers"><?php echo esc_textarea( $s['a11y_known_issues'] ); ?></textarea></td></tr>
	<tr><th><label for="a11y_tested_date">Last tested</label></th><td><input type="date" id="a11y_tested_date" name="<?php echo esc_attr( $n( 'a11y_tested_date' ) ); ?>" value="<?php echo esc_attr( $s['a11y_tested_date'] ); ?>"></td></tr>
	<tr><th><label for="a11y_tested_how">Tested by</label></th><td><input type="text" class="large-text" id="a11y_tested_how" name="<?php echo esc_attr( $n( 'a11y_tested_how' ) ); ?>" value="<?php echo esc_attr( $s['a11y_tested_how'] ); ?>"></td></tr>
</table>

<h2>Documents</h2>
<p class="description">Each document is a block (<strong>Legal Document</strong>) or shortcode <code>[vu_document type="terms"]</code>. Expand a clause to see the generated text; type in the override box to replace it for this site only. Merge fields and <code>{if:flag}</code> conditionals work inside overrides too.</p>

<?php foreach ( VUL_Documents::types() as $type => $info ) : if ( 'cookies' === $type ) { continue; } ?>
	<?php
	$meta    = VUL_Documents::meta( $type );
	$page_id = (int) $s[ $info['page_key'] ];
	$ov      = VUL_Documents::overrides( $type );
	?>
	<div class="vul-doc-card">
		<div class="vul-doc-card__head">
			<h3><?php echo esc_html( $info['label'] ); ?></h3>
			<div class="vul-doc-card__meta">
				<label>Version <input type="text" class="small-text" name="<?php echo esc_attr( $n( "docs_meta][$type][version" ) ); ?>" value="<?php echo esc_attr( $meta['version'] ); ?>"></label>
				<label>Updated <input type="date" name="<?php echo esc_attr( $n( "docs_meta][$type][updated" ) ); ?>" value="<?php echo esc_attr( $meta['updated'] ); ?>"></label>
				<?php if ( $page_id && get_post( $page_id ) ) : ?>
					<a class="button button-small" href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" target="_blank">View page</a>
					<a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>">Edit page</a>
				<?php else : ?>
					<a class="button button-small button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=vul_create_doc_page&type=' . $type ), 'vul_create_doc_page' ) ); ?>">Create page</a>
				<?php endif; ?>
			</div>
		</div>
		<p class="description">Page: <?php wp_dropdown_pages( array( 'name' => $n( $info['page_key'] ), 'selected' => $page_id, 'show_option_none' => '— Not set —', 'option_none_value' => 0 ) ); ?></p>
		<?php foreach ( VUL_Documents::sections( $type ) as $key => $sec ) : ?>
			<?php $generated = VUL_Documents::compile( $sec['body'], $type ); ?>
			<details class="vul-clause <?php echo ! empty( $ov[ $key ] ) ? 'vul-clause--overridden' : ''; ?>">
				<summary><?php echo esc_html( $sec['heading'] ?: 'Introduction' ); ?><?php echo ! empty( $ov[ $key ] ) ? ' <span class="vul-pill vul-pill--on">overridden</span>' : ''; ?><?php echo '' === trim( wp_strip_all_tags( $generated ) ) ? ' <span class="vul-pill">hidden by current settings</span>' : ''; ?></summary>
				<div class="vul-clause__body">
					<div class="vul-clause__generated"><?php echo wp_kses_post( $generated ); ?></div>
					<label class="vul-clause__label">Override (leave empty to use the generated text)</label>
					<textarea class="large-text" rows="4" name="<?php echo esc_attr( $n( "docs_overrides][$type][$key" ) ); ?>"><?php echo esc_textarea( $ov[ $key ] ?? '' ); ?></textarea>
				</div>
			</details>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
