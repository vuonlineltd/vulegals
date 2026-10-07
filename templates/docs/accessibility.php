<?php
/**
 * Accessibility Statement. Follows the structure of the UK government model statement,
 * written for a private-sector site. Tokens: {company} {site_url} {wcag_level} {a11y_tested_date}
 * {a11y_tested_how} {a11y_known_issues} {contact_email} {phone} {doc_version} {doc_updated}.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	'intro' => array(
		'heading'  => '',
		'numbered' => false,
		'body'     => '<p>This statement applies to {site_url}, which is run by {company}. We want as many people as possible to be able to use this website, and we are committed to making it accessible in line with {wcag_level}.</p>
<p>Version {doc_version}, last updated {doc_updated}.</p>',
	),

	'what' => array(
		'heading' => 'What we have done',
		'body'    => '<p>On this website you should be able to: navigate using only a keyboard; zoom in up to 400% without content overlapping or disappearing; use a screen reader to read the content and operate the controls; see focus clearly on every interactive element; understand every image through its text alternative; and read text with sufficient colour contrast. Videos and animations do not auto-play with sound, and moving content can be paused.</p>
<p>The cookie banner and preferences panel are keyboard accessible, announce themselves to screen readers, and can be closed with the Escape key.</p>
<p><a href="https://mcmw.abilitynet.org.uk/" rel="noopener" target="_blank">AbilityNet</a> has advice on making your device easier to use if you have a disability.</p>',
	),

	'status' => array(
		'heading' => 'How accessible this website is',
		'body'    => '{if:a11y_full}<p>We believe this website is fully compliant with {wcag_level}.</p>{/if}
{if:a11y_partial}<p>This website is partially compliant with {wcag_level}. We know some parts are not yet fully accessible:</p>{if:a11y_issues}{a11y_known_issues}{/if}{ifnot:a11y_issues}<p>We are reviewing the site and will list any known issues here.</p>{/if}{/if}
{if:a11y_non}<p>This website is not yet compliant with {wcag_level}. The main issues are:</p>{if:a11y_issues}{a11y_known_issues}{/if}{ifnot:a11y_issues}<p>We are reviewing the site and will list the issues here.</p>{/if}{/if}
<p>Some content may be provided by third parties (for example embedded video players, maps or forms) and we do not always control how accessible it is. Where we know of a problem we work with the provider or offer an alternative.</p>',
	),

	'feedback' => array(
		'heading' => 'Feedback and contact',
		'body'    => '<p>If you cannot access any part of this website, or need information in a different format such as accessible PDF, large print or audio, please email <a href="mailto:{contact_email}">{contact_email}</a>{if:phone} or call {phone}{/if}. Tell us what you need and we will get back to you within five working days.</p>
<p>If you find a problem not listed on this page, or think we are not meeting the requirements, please let us know using the same details. We are always working to improve the site and every report helps.</p>',
	),

	'enforcement' => array(
		'heading' => 'Enforcement',
		'body'    => '<p>The Equality Act 2010 (and the Disability Discrimination Act 1995 in Northern Ireland) requires service providers to make reasonable adjustments for disabled people. If you are not happy with how we respond to your complaint, you can contact the <a href="https://www.equalityadvisoryservice.com/" rel="noopener" target="_blank">Equality Advisory and Support Service (EASS)</a>{if:ni}, or the <a href="https://www.equalityni.org/" rel="noopener" target="_blank">Equality Commission for Northern Ireland</a>{/if}.</p>',
	),

	'technical' => array(
		'heading' => 'Technical information',
		'body'    => '<p>{company} is committed to making this website accessible. This website has been built to conform with the Web Content Accessibility Guidelines version 2.2 at level AA, published by the W3C.</p>
{if:a11y_tested}<p>This website was last tested on {a11y_tested_date}. Testing was carried out by {a11y_tested_how}.</p>{/if}
{ifnot:a11y_tested}<p>Testing is carried out by {a11y_tested_how}.</p>{/if}
<p>We review accessibility whenever we make significant changes to the website and at least once a year. This statement was prepared on {doc_updated}.</p>',
	),

);
