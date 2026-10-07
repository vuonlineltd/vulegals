<?php
/**
 * Privacy Policy. Tokens: {company} {entity_description} {contact_email} {phone} {address} {ico_number}
 * {data_table} {retention_table} {processors_list} {retention_default} {transfer_safeguards}
 * {marketing_channels} {marketing_optout} {cookie_url} {terms_url} {doc_version} {doc_updated}.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	'intro' => array(
		'heading'  => '',
		'numbered' => false,
		'body'     => '<p>{company} respects your privacy and is committed to protecting your personal data. This policy explains what we collect when you use {site_url} ("the site") or otherwise deal with us, why we collect it, how long we keep it, who we share it with and what your rights are.</p>
<p>Version {doc_version}, last updated {doc_updated}.</p>',
	),

	'controller' => array(
		'heading' => 'Who we are',
		'body'    => '<p>The data controller is {entity_description}.{if:ico} We are registered with the Information Commissioner\'s Office (ICO) under registration number {ico_number}.{/if}</p>
<p>Questions about this policy or about your personal data should be sent to <a href="mailto:{contact_email}">{contact_email}</a>{if:phone}, or by telephone on {phone}{/if}{if:address}, or by post to {address}{/if}.</p>',
	),

	'scope' => array(
		'heading' => 'What this policy covers',
		'body'    => '<p>This policy applies to your use of the site and to personal data you give us by email, telephone, in person or through forms on the site. It does not cover other websites we link to; please read their own privacy policies.</p>
<p>"Personal data" means any information that identifies you or could identify you, directly or indirectly. It does not include data that has been anonymised.</p>',
	),

	'rights' => array(
		'heading' => 'Your rights',
		'body'    => '<p>Under the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018 you have the right to:</p>
<ul>
<li><strong>be informed</strong> about how we use your personal data, which is the purpose of this policy;</li>
<li><strong>access</strong> the personal data we hold about you (a "subject access request");</li>
<li><strong>have it corrected</strong> if it is inaccurate or incomplete;</li>
<li><strong>have it erased</strong> in certain circumstances;</li>
<li><strong>restrict</strong> how we process it in certain circumstances;</li>
<li><strong>object</strong> to processing based on our legitimate interests, and to direct marketing at any time;</li>
<li><strong>withdraw consent</strong> at any time where consent is our legal basis;</li>
<li><strong>data portability</strong>: to receive data you gave us in a structured, machine-readable format where we process it by automated means on the basis of consent or a contract;</li>
<li>not be subject to decisions based solely on <strong>automated processing</strong> that have legal or similarly significant effects on you{ifnot:automated} (we do not make decisions of this kind){/if}.</li>
</ul>
<p>To exercise any of these rights, contact us using the details above. We will respond within one month, and there is normally no charge. We may need to confirm your identity first.</p>
<p>If you are unhappy with how we handle your personal data, please tell us first so we can try to put it right. You also have the right to complain to the Information Commissioner\'s Office at <a href="https://ico.org.uk" rel="noopener" target="_blank">ico.org.uk</a> or on 0303 123 1113.</p>',
	),

	'data' => array(
		'heading' => 'What we collect, how, and why',
		'body'    => '<p>We must have a lawful basis for every use of personal data. The table below sets out what we collect, where it comes from, what we use it for and the legal basis we rely on. Where we rely on legitimate interests, our interest is running and improving our business and the site, responding to enquiries and keeping our systems secure, and we have balanced this against your rights.</p>
{data_table}
{if:newsletter}<p><strong>Marketing.</strong> With your consent, or where the law otherwise allows (for example where you are an existing customer and have not opted out), we may send you information about our products, services and news by {marketing_channels}. You can opt out at any time {marketing_optout}. We will never sell your details to third parties for their marketing.</p>{/if}
{if:children}<p>The site is not directed at children under 13 and we do not knowingly collect personal data from them. If you believe a child has given us personal data, please contact us and we will delete it.</p>{/if}
{if:automated}<p><strong>Automated decision-making.</strong> We use automated processing for the purposes described above. You can ask for a person to review any decision made this way that affects you.</p>{/if}
<p>We only use your personal data for the purposes it was collected for, unless we reasonably consider another purpose compatible with the original one. If we need to use it for an unrelated purpose we will tell you and explain the legal basis.</p>',
	),

	'retention' => array(
		'heading' => 'How long we keep it',
		'body'    => '<p>We keep personal data no longer than we need it for the purpose it was collected, unless the law requires us to keep it longer (for example, accounting records must be kept for six years). Our normal retention periods are:</p>
{retention_table}
<p>Where no fixed period is listed: {retention_default}.</p>',
	),

	'sharing' => array(
		'heading' => 'Who we share it with',
		'body'    => '<p>We do not sell your personal data. We share it only with service providers who process it on our behalf under contracts that require them to protect it and use it only on our instructions, with professional advisers, and where the law requires or permits (for example with regulators, or to protect our legal rights). Our current providers are:</p>
{processors_list}
<p>If our business is sold or merged, personal data may be transferred to the new owner, who must use it in accordance with this policy.</p>',
	),

	'transfers' => array(
		'heading' => 'Where your data is stored',
		'body'    => '{ifnot:transfers}<p>We store and process your personal data within the United Kingdom, where it is protected by UK data protection law.</p>{/if}
{if:transfers}{ifnot:transfers_int}<p>We store and process your personal data within the United Kingdom and the European Economic Area (EEA). The EEA has been found by the UK to provide an adequate level of protection, so your data remains protected to UK standards.</p>{/if}{/if}
{if:transfers_int}<p>Some of our service providers store or process data outside the United Kingdom and the EEA, including in the United States. Where this happens we make sure your data is protected by one of the safeguards recognised under UK law: {transfer_safeguards}. You can ask us for details of the safeguard that applies to a particular transfer.</p>{/if}
<p>We protect your personal data with appropriate technical and organisational measures, including encrypted connections (HTTPS), access limited to people who need it and who are bound by confidentiality, secure and regularly updated systems, and procedures for handling any data breach, including notifying you and the ICO where we are required to.</p>',
	),

	'cookies' => array(
		'heading' => 'Cookies',
		'body'    => '<p>The site uses cookies and similar technologies. Strictly necessary cookies are always on; optional cookies (such as analytics{if:marketing} and marketing{/if}) run only if you allow them, and you can change your choice at any time.{if:cookies_page} Full details, including a list of every cookie, are in our <a href="{cookie_url}">Cookie Policy</a>.{/if}</p>',
	),

	'changes' => array(
		'heading' => 'Changes to this policy',
		'body'    => '<p>We may update this policy from time to time. The version and date at the top show when it last changed. Significant changes will be highlighted on the site{if:newsletter} or, where appropriate, sent to you by email{/if}.</p>',
	),

);
