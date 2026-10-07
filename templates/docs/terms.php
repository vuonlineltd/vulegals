<?php
/**
 * Website Terms of Use. Plain-English clauses, ordered. Tokens: {company} {site_url} {site_host}
 * {entity_description} {vat_number} {regulator} {professional_body} {contact_email} {phone} {contact_url}
 * {privacy_url} {cookie_url} {jurisdiction} {advice_area} {doc_version} {doc_updated}.
 * Conditionals: {if:flag}…{/if} / {ifnot:flag}…{/if}. Filter: vul_document_sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	'intro' => array(
		'heading'  => '',
		'numbered' => false,
		'body'     => '<p>These terms set out how you may use this website, {site_url} ("the site"). By using the site you agree to them. If you do not agree, please do not use the site.</p>
<p>Our <a href="{privacy_url}">Privacy Policy</a>{if:cookies_page} and <a href="{cookie_url}">Cookie Policy</a>{/if} also apply to your use of the site and form part of these terms.</p>
<p>Version {doc_version}, last updated {doc_updated}.</p>',
	),

	'about' => array(
		'heading' => 'About us',
		'body'    => '<p>The site is operated by {entity_description}.{if:vat} Our VAT number is {vat_number}.{/if}</p>
{if:regulator}<p>We are regulated by {regulator}.</p>{/if}
{if:body}<p>We are a member of {professional_body}.</p>{/if}
<p>You can contact us by email at <a href="mailto:{contact_email}">{contact_email}</a>{if:phone}, by telephone on {phone}{/if}{if:contact_page}, or through our <a href="{contact_url}">contact page</a>{/if}.</p>',
	),

	'access' => array(
		'heading' => 'Using the site',
		'body'    => '<p>Access to the site is free of charge. You are responsible for the equipment and internet connection you need to use it.</p>
<p>The site is provided "as is" and "as available". We may change, suspend or withdraw all or any part of it at any time without notice, and we are not liable if it is unavailable for any period.</p>
{if:accounts}<p>Some areas of the site require an account. You must give accurate information when you register, keep your login details confidential, and tell us straight away if you think your account has been used without your permission. You are responsible for everything done through your account. We may suspend or close an account that breaches these terms.</p>{/if}',
	),

	'ip' => array(
		'heading' => 'Intellectual property',
		'body'    => '<p>Unless stated otherwise, all content on the site (text, images, graphics, logos, video, audio, code and layout) belongs to us or our licensors and is protected by copyright, trade mark and other intellectual property laws.</p>
<p>You may view the site in a browser, print or download extracts for your own personal, non-commercial reference, and share links to pages. You must always acknowledge us (or the identified licensor) as the source.</p>
<p>You may not otherwise copy, reproduce, republish, distribute, sell, adapt or commercially exploit any content without our written permission. Nothing in these terms limits the acts permitted by law, such as fair dealing for research, private study, criticism, review or news reporting.</p>',
	),

	'linking' => array(
		'heading' => 'Linking to the site',
		'body'    => '<p>You are welcome to link to {if:deeplinks}any page of {/if}{ifnot:deeplinks}the home page of {/if}the site, provided you do so fairly and lawfully, do not suggest any association with or endorsement by us that does not exist, do not use our logos or trade marks without permission, and do not damage our reputation or take unfair advantage of it.{ifnot:deeplinks} Linking to other pages needs our written permission.{/if}</p>
<p>You must not frame or embed the site on another website without our written permission, and you must not link to the site from any website that contains material that is unlawful, obscene, hateful, defamatory, discriminatory, threatening, deceptive or that infringes anyone\'s rights.</p>',
	),

	'thirdparty' => array(
		'heading' => 'Links to other websites',
		'body'    => '<p>The site may contain links to other websites. Unless we say otherwise, those sites are not under our control and we are not responsible for their content or for any loss or damage that may arise from your use of them. A link does not mean we endorse the site or the people behind it.</p>',
	),

	'forms' => array(
		'heading' => 'Contacting us through the site',
		'body'    => '{if:forms}<p>You may use the forms on the site to contact us. When you do, you must not send anything that is unlawful, obscene, offensive, threatening, defamatory, discriminatory, deceptive, or that infringes anyone else\'s rights, including their privacy or intellectual property. You must not impersonate anyone or misrepresent who you are.</p>
<p>We may monitor messages sent through the site. Personal information you send us is handled in line with our <a href="{privacy_url}">Privacy Policy</a>.</p>{/if}
{if:ugc}<p>Where the site lets you post content (such as comments or reviews), you keep ownership of it but grant us a non-exclusive, royalty-free, worldwide licence to use, reproduce and display it on the site and in connection with our business. You confirm that you have the right to post it and that it complies with these terms. We may edit or remove any content at our discretion, and we are not responsible for content posted by other users.</p>{/if}',
	),

	'disclaimer' => array(
		'heading' => 'Information on the site',
		'body'    => '<p>The content on the site is provided for general information only. It is not advice and you should not rely on it as such.{if:advice} You should always take professional advice before acting on anything relating to {advice_area}.{/if}</p>
<p>We try to keep the site accurate and up to date, but we make no promises that the content is complete, accurate or current, that the site will meet your needs, or that it will work with all software and devices.</p>
{ifnot:ecommerce}<p>Nothing on the site is a contractual offer. No goods or services are sold through the site; descriptions and any prices shown are for information only and may change. Please contact us to check availability.</p>{/if}
{if:ecommerce}<p>Purchases made through the site are governed by our separate terms of sale, which are shown before you place an order.</p>{/if}',
	),

	'liability' => array(
		'heading' => 'Our liability',
		'body'    => '<p>To the fullest extent permitted by law, we accept no liability for any loss or damage, whether foreseeable or not, arising from your use of (or inability to use) the site or your reliance on any content on it, whether in contract, tort (including negligence), breach of statutory duty or otherwise.</p>
<p>If you are a business user, we accept no liability for loss of profit, sales, business, revenue, goodwill, reputation or anticipated savings, business interruption, or any indirect or consequential loss.</p>
<p>We take reasonable care to keep the site free from viruses and other harmful material, but we are not liable for any loss or damage caused by a virus, denial-of-service attack or other harmful material that affects your equipment or data through your use of the site. Nor are we liable for the site being unavailable for reasons outside our control, such as hosting, network or power failures.</p>
<p>Nothing in these terms excludes or limits our liability for death or personal injury caused by our negligence, for fraud, or for anything else that cannot be excluded or limited by law. If you are a consumer, nothing in these terms affects your statutory rights.</p>',
	),

	'security' => array(
		'heading' => 'Security and acceptable use',
		'body'    => '<p>You must use the site lawfully. You must not: introduce viruses or other harmful code; try to gain unauthorised access to the site, the server it is hosted on, or any connected system; attack the site (for example by denial of service); scrape, copy or harvest data from the site by automated means; or use the site for anything fraudulent, harmful or unlawful.</p>
<p>Breaching this section may be a criminal offence under the Computer Misuse Act 1990. We will report breaches to the relevant authorities and cooperate with them, including by disclosing your identity, and your right to use the site will end immediately.</p>
<p>If you breach these terms we may, at our discretion, suspend or end your access to the site, issue a warning, take legal action (including to recover our costs) and disclose information to law enforcement where required or appropriate.</p>',
	),

	'privacy' => array(
		'heading' => 'Privacy and data protection',
		'body'    => '<p>We handle personal data in accordance with the UK General Data Protection Regulation and the Data Protection Act 2018. Full details of what we collect, why, on what legal basis, how long we keep it and your rights are in our <a href="{privacy_url}">Privacy Policy</a>{if:cookies_page}, and our use of cookies is explained in our <a href="{cookie_url}">Cookie Policy</a>{/if}.</p>',
	),

	'comms' => array(
		'heading' => 'Communications from us',
		'body'    => '{if:newsletter}<p>If we hold your contact details we may send you important service notices by email, for example about changes to these terms. We will only send you marketing emails with your consent, and every marketing email includes an unsubscribe link. You can opt out at any time; it may take a few working days for a change to take effect.</p>{/if}
{ifnot:newsletter}<p>If we hold your contact details we may send you important service notices by email, for example about changes to these terms. We will not send you marketing emails without your consent.</p>{/if}',
	),

	'changes' => array(
		'heading' => 'Changes to these terms',
		'body'    => '<p>We may change these terms at any time. The version and date at the top of this page show when they were last updated, and changes take effect the next time you use the site. Please check this page from time to time. If there is any conflict between a current and a previous version, the current version applies.</p>',
	),

	'law' => array(
		'heading' => 'Governing law',
		'body'    => '<p>These terms, and any dispute or claim arising from them or from your use of the site, are governed by the law of {jurisdiction}, and the courts of {jurisdiction} have jurisdiction. If you are a consumer living elsewhere in the United Kingdom, you may also bring proceedings in your home nation, and you will benefit from any mandatory provisions of the law where you live.</p>',
	),

);
