/* Vu Legals — consent runtime. No dependencies. */
(function () {
	'use strict';

	var cfg = window.VUL_CONFIG || {};
	var root = document.querySelector('[data-vul-root]');
	var banner = root && root.querySelector('[data-vul-banner]');
	var modal = root && root.querySelector('[data-vul-modal]');
	var overlay = root && root.querySelector('[data-vul-overlay]');
	var listeners = [];
	var state = null;
	var lastFocus = null;

	/* ---------- Cookie helpers ---------- */

	function readCookie() {
		var m = document.cookie.match(new RegExp('(?:^|; )' + cfg.cookie + '=([^;]*)'));
		if (!m) return null;
		try {
			var d = JSON.parse(decodeURIComponent(m[1]));
			if (!d || String(d.v) !== String(cfg.version) || !d.c) return null;
			return d;
		} catch (e) {
			return null;
		}
	}

	function writeCookie(data) {
		var days = cfg.expiry || 180;
		var exp = new Date(Date.now() + days * 864e5).toUTCString();
		var c = cfg.cookie + '=' + encodeURIComponent(JSON.stringify(data)) + '; expires=' + exp + '; path=/; SameSite=Lax';
		if (cfg.domain) c += '; domain=' + cfg.domain;
		if (location.protocol === 'https:') c += '; Secure';
		document.cookie = c;
	}

	function uuid() {
		if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (ch) {
			var r = Math.random() * 16 | 0;
			return (ch === 'x' ? r : (r & 0x3) | 0x8).toString(16);
		});
	}

	/* ---------- Consent state ---------- */

	function allCats(value) {
		var out = {};
		Object.keys(cfg.categories || {}).forEach(function (k) {
			out[k] = cfg.categories[k].locked ? true : !!value;
		});
		return out;
	}

	function fromSwitches(scope) {
		var out = allCats(false);
		(scope || modal || document).querySelectorAll('[data-vul-cat]').forEach(function (el) {
			out[el.getAttribute('data-vul-cat')] = el.checked;
		});
		return out;
	}

	function syncSwitches(cats) {
		document.querySelectorAll('[data-vul-cat]').forEach(function (el) {
			var k = el.getAttribute('data-vul-cat');
			el.checked = cats ? !!cats[k] : !!(cfg.categories[k] && cfg.categories[k]['default']);
			el.setAttribute('aria-checked', el.checked ? 'true' : 'false');
		});
	}

	function updateStatus(saved) {
		document.querySelectorAll('[data-vul-status]').forEach(function (el) {
			if (!state) { el.textContent = cfg.text.nochoice; el.classList.remove('is-saved'); return; }
			var on = Object.keys(state.c).filter(function (k) { return state.c[k]; }).map(function (k) { return cfg.categories[k] ? cfg.categories[k].label : k; });
			var date = state.t ? new Date(state.t * 1000).toLocaleDateString() : '';
			el.textContent = (saved ? cfg.text.saved + ' ' : '') + cfg.text.current.replace('{choices}', on.join(', ')).replace('{date}', date);
			el.classList.toggle('is-saved', !!saved); el.classList.remove('is-unsaved');
		});
	}

	function has(cat) {
		if (cat === 'necessary') return true;
		return !!(state && state.c && state.c[cat]);
	}

	/* ---------- Activation ---------- */

	function activateScripts(cat) {
		document.querySelectorAll('script[data-vul-consent="' + cat + '"]').forEach(function (old) {
			if (old.getAttribute('data-vul-done')) return;
			old.setAttribute('data-vul-done', '1');
			var s = document.createElement('script');
			Array.prototype.forEach.call(old.attributes, function (a) {
				if (/^(type|data-vul-consent|data-vul-src|data-vul-type|data-vul-done)$/.test(a.name)) return;
				s.setAttribute(a.name, a.value);
			});
			var type = old.getAttribute('data-vul-type');
			if (type) s.type = type;
			var src = old.getAttribute('data-vul-src');
			if (src) {
				s.src = src;
			} else {
				s.text = old.text || old.textContent;
			}
			old.parentNode.insertBefore(s, old.nextSibling);
		});
		document.querySelectorAll('[data-vul-embed="' + cat + '"]').forEach(function (wrap) {
			var f = wrap.querySelector('[data-vul-src]');
			if (f) {
				f.setAttribute('src', f.getAttribute('data-vul-src'));
				f.removeAttribute('data-vul-src');
			}
			wrap.classList.add('vul-embed--loaded');
		});
	}

	function consentModeUpdate(cats) {
		if (!cfg.consentMode) return;
		var g = window.gtag || function () { (window.dataLayer = window.dataLayer || []).push(arguments); };
		var m = has_(cats, 'marketing') ? 'granted' : 'denied';
		g('consent', 'update', {
			ad_storage: m,
			ad_user_data: m,
			ad_personalization: m,
			analytics_storage: has_(cats, 'analytics') ? 'granted' : 'denied',
			functionality_storage: has_(cats, 'functional') ? 'granted' : 'denied',
			personalization_storage: has_(cats, 'functional') ? 'granted' : 'denied',
			security_storage: 'granted'
		});
	}

	function has_(cats, k) {
		return k === 'necessary' || !!(cats && cats[k]);
	}

	/* WP Consent API bridge (wp-consent-api plugin): publish our state in its vocabulary. */
	function wpConsentApi(cats) {
		if (typeof window.wp_set_consent !== 'function') return;
		try {
			window.wp_set_consent('functional', 'allow');
			window.wp_set_consent('preferences', has_(cats, 'functional') ? 'allow' : 'deny');
			window.wp_set_consent('statistics', has_(cats, 'analytics') ? 'allow' : 'deny');
			window.wp_set_consent('statistics-anonymous', has_(cats, 'analytics') ? 'allow' : 'deny');
			window.wp_set_consent('marketing', has_(cats, 'marketing') ? 'allow' : 'deny');
		} catch (e) { /* noop */ }
	}

	function apply(cats, source) {
		Object.keys(cats).forEach(function (k) {
			if (cats[k]) activateScripts(k);
		});
		wpConsentApi(cats);
		if (source !== 'default') consentModeUpdate(cats); // defaults already emitted in <head>
		(window.dataLayer = window.dataLayer || []).push({ event: 'vul_consent', vul_consent: cats, vul_source: source || 'load' });
		var detail = { categories: cats, source: source || 'load' };
		document.dispatchEvent(new CustomEvent('vul:consent', { detail: detail }));
		listeners.forEach(function (fn) { try { fn(detail); } catch (e) { /* noop */ } });
		document.documentElement.setAttribute('data-vul-consent', Object.keys(cats).filter(function (k) { return cats[k]; }).join(' '));
	}

	function log(data, source) {
		if (!cfg.log || !cfg.restUrl || !navigator.sendBeacon) {
			if (cfg.log && cfg.restUrl && window.fetch) {
				fetch(cfg.restUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: data.id, v: data.v, c: data.c, source: source }), keepalive: true }).catch(function () {});
			}
			return;
		}
		var blob = new Blob([JSON.stringify({ id: data.id, v: data.v, c: data.c, source: source })], { type: 'application/json' });
		navigator.sendBeacon(cfg.restUrl, blob);
	}

	/* Delete registered first-party cookies for a category the visitor has withdrawn.
	   Third-party cookies (e.g. .youtube.com) can't be touched from here; the reload stops them being refreshed. */
	function purgeCookies(cat) {
		var names = (cfg.cookieNames && cfg.cookieNames[cat]) || [];
		if (!names.length) return;
		var patterns = names.map(function (n) { return new RegExp('^' + n.replace(/[.+?^${}()|[\]\\]/g, '\\$&').replace(/\*/g, '.*') + '$'); });
		var host = location.hostname, parts = host.split('.'), domains = [''];
		for (var i = 0; i < parts.length - 1; i++) domains.push(parts.slice(i).join('.'));
		document.cookie.split(';').forEach(function (pair) {
			var name = pair.split('=')[0].trim();
			if (!name || name === cfg.cookie) return;
			if (!patterns.some(function (re) { return re.test(name); })) return;
			domains.forEach(function (d) {
				['/', location.pathname].forEach(function (p) {
					document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=' + p + (d ? '; domain=' + d : '');
				});
			});
		});
	}

	function save(cats, source) {
		var prev = state;
		var revoked = false;
		if (prev && prev.c) {
			Object.keys(cats).forEach(function (k) { if (prev.c[k] && !cats[k]) { revoked = true; purgeCookies(k); } });
		}
		state = { v: String(cfg.version), t: Math.floor(Date.now() / 1000), id: (prev && prev.id) || uuid(), c: cats };
		writeCookie(state);
		log(state, source);
		hideAll();
		syncSwitches(cats);
		updateStatus(true);
		if (revoked) {
			// Scripts can't be unloaded; reload so the denied tags never run on this page.
			window.location.reload();
			return;
		}
		apply(cats, source);
	}

	/* ---------- UI ---------- */

	function show(el) { if (el) el.hidden = false; }
	function hide(el) { if (el) el.hidden = true; }

	function showBanner() {
		if (!root || !banner) return;
		show(root);
		show(banner);
		if (cfg.overlay || cfg.layout === 'modal') show(overlay);
	}

	function openModal() {
		if (!root || !modal) return;
		lastFocus = document.activeElement;
		syncSwitches(state && state.c);
		show(root);
		hide(banner);
		show(overlay);
		show(modal);
		document.documentElement.classList.add('vul-lock');
		var first = modal.querySelector('[data-vul-close]');
		if (first) first.focus();
	}

	function closeModal() {
		hide(modal);
		document.documentElement.classList.remove('vul-lock');
		if (!state) {
			showBanner();
		} else {
			hide(overlay);
			hide(root);
		}
		if (lastFocus && lastFocus.focus) lastFocus.focus();
	}

	function hideAll() {
		hide(modal);
		hide(banner);
		hide(overlay);
		hide(root);
		document.documentElement.classList.remove('vul-lock');
	}

	function trapFocus(e) {
		if (!modal || modal.hidden || e.key !== 'Tab') return;
		var f = modal.querySelectorAll('button, [href], input:not([disabled]), summary, [tabindex]:not([tabindex="-1"])');
		if (!f.length) return;
		var first = f[0], last = f[f.length - 1];
		if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
		else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
	}

	function bind() {
		document.addEventListener('click', function (e) {
			var t = e.target.closest('[data-vul-accept-all],[data-vul-reject-all],[data-vul-save],[data-vul-open],[data-vul-close],[data-vul-accept],[data-vul-overlay]');
			if (!t) return;
			if (t.hasAttribute('data-vul-accept-all')) { e.preventDefault(); save(allCats(true), 'accept_all'); }
			else if (t.hasAttribute('data-vul-reject-all')) { e.preventDefault(); save(allCats(false), 'reject_all'); }
			else if (t.hasAttribute('data-vul-save')) { e.preventDefault(); save(fromSwitches(t.closest('[data-vul-panel]')), 'preferences'); }
			else if (t.hasAttribute('data-vul-open')) { e.preventDefault(); openModal(); }
			else if (t.hasAttribute('data-vul-close')) { e.preventDefault(); closeModal(); }
			else if (t.hasAttribute('data-vul-accept')) {
				e.preventDefault();
				var cats = state && state.c ? Object.assign({}, state.c) : allCats(false);
				cats[t.getAttribute('data-vul-accept')] = true;
				save(cats, 'embed');
			} else if (t.hasAttribute('data-vul-overlay') && modal && !modal.hidden) { closeModal(); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
			trapFocus(e);
		});
		document.querySelectorAll('[data-vul-cat]').forEach(function (el) {
			el.addEventListener('change', function () {
				el.setAttribute('aria-checked', el.checked ? 'true' : 'false');
				var panel = el.closest('[data-vul-panel]');
				var status = panel && panel.querySelector('[data-vul-status]');
				if (status) { status.textContent = cfg.text.unsaved; status.classList.remove('is-saved'); status.classList.add('is-unsaved'); }
			});
		});
	}

	/* ---------- Geo (client fallback) ---------- */

	function inRequiredRegion() {
		try {
			var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
			return /^Europe\//.test(tz) || tz === 'Atlantic/Reykjavik' || tz === 'Atlantic/Canary' || tz === 'Atlantic/Madeira' || tz === 'Atlantic/Faroe';
		} catch (e) {
			return true; // Unknown: be safe and ask.
		}
	}

	/* ---------- Boot ---------- */

	function boot() {
		bind();
		state = readCookie();
		syncSwitches(state && state.c);
		updateStatus(false);

		if (state) {
			apply(state.c, 'stored');
			return;
		}

		var geo = cfg.geo;
		if (geo === 'client') geo = inRequiredRegion() ? 'show' : cfg.geoOutside;

		if (geo === 'implied') {
			apply(allCats(true), 'implied');
			return;
		}
		if (geo === 'hide') {
			apply(allCats(false), 'hidden');
			return;
		}
		apply(allCats(false), 'default');
		showBanner();
	}

	window.vul = {
		open: openModal,
		accept: function () { save(allCats(true), 'api'); },
		reject: function () { save(allCats(false), 'api'); },
		set: function (cats) { save(Object.assign(allCats(false), cats), 'api'); },
		state: function () { return state ? Object.assign({}, state.c) : null; },
		has: has,
		on: function (fn) { listeners.push(fn); if (state) fn({ categories: state.c, source: 'late' }); },
		reset: function () { document.cookie = cfg.cookie + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/'; location.reload(); }
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
