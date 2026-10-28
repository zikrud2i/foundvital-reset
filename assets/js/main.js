/* FoundVital Reset – site behaviour: nav, reveal, consent-gated tracking, Calendly, form hints. */
(function () {
	'use strict';
	var FVR = window.FVR || {};
	var doc = document;

	function safe(fn) { try { return fn(); } catch (e) { return null; } }
	function getCookie(name) {
		var m = doc.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.$?*|{}()[\]\\/+^]/g, '\\$&') + '=([^;]*)'));
		return m ? decodeURIComponent(m[1]) : null;
	}
	function delCookie(name) { doc.cookie = name + '=; Max-Age=0; path=/; SameSite=Lax'; }

	/* ---------- Analytics (dataLayer always; vendor scripts only after consent) ---------- */
	window.dataLayer = window.dataLayer || [];
	var consent = safe(function () { return localStorage.getItem('fvr_consent'); });

	function track(name, params) {
		var payload = Object.assign({ event: name }, params || {});
		window.dataLayer.push(payload);
		if (consent === 'granted') {
			if (FVR.ga4 && typeof window.gtag === 'function') { window.gtag('event', name, params || {}); }
			if (FVR.pixel && typeof window.fbq === 'function') { window.fbq('trackCustom', name, params || {}); }
		}
	}
	window.fvrTrack = track;

	function loadTracking() {
		if (loadTracking.done || !FVR.tracking) { return; }
		loadTracking.done = true;
		function add(src) { var s = doc.createElement('script'); s.async = true; s.src = src; doc.head.appendChild(s); }
		if (FVR.gtm) {
			window.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
			add('https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(FVR.gtm));
		} else if (FVR.ga4) {
			window.gtag = function () { window.dataLayer.push(arguments); };
			window.gtag('js', new Date());
			window.gtag('config', FVR.ga4, { anonymize_ip: true });
			add('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(FVR.ga4));
		}
		if (FVR.pixel) {
			/* Standard Meta Pixel bootstrap */
			(function (f, b, e, v, n, t, s) {
				if (f.fbq) { return; }
				n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
				if (!f._fbq) { f._fbq = n; }
				n.push = n; n.loaded = true; n.version = '2.0'; n.queue = [];
				t = b.createElement(e); t.async = true; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
			})(window, doc, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
			window.fbq('init', FVR.pixel);
			window.fbq('track', 'PageView');
		}
	}

	var banner = doc.getElementById('consent-banner');
	function setConsent(v) {
		consent = v;
		safe(function () { localStorage.setItem('fvr_consent', v); });
		if (banner) { banner.hidden = true; }
		if (v === 'granted') { loadTracking(); }
	}
	if (FVR.tracking) {
		if (consent === 'granted') { loadTracking(); }
		else if (!consent && banner) { banner.hidden = false; }
		doc.addEventListener('click', function (e) {
			var b = e.target.closest('[data-consent]');
			if (b) { setConsent(b.getAttribute('data-consent')); }
			if (e.target.closest('[data-consent-open]') && banner) { banner.hidden = false; }
		});
	}

	/* ---------- Campaign attribution (session only, appended to Calendly links) ---------- */
	var utm = {};
	safe(function () {
		var keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
		var q = new URLSearchParams(location.search);
		keys.forEach(function (k) { if (q.get(k)) { sessionStorage.setItem('fvr_' + k, q.get(k).slice(0, 100)); } });
		keys.forEach(function (k) { var v = sessionStorage.getItem('fvr_' + k); if (v) { utm[k] = v; } });
	});
	if (Object.keys(utm).length) {
		doc.querySelectorAll('a[href*="calendly.com"]').forEach(function (a) {
			safe(function () { var u = new URL(a.href); Object.keys(utm).forEach(function (k) { u.searchParams.set(k, utm[k]); }); a.href = u.toString(); });
		});
	}

	/* ---------- Click tracking via data-track ---------- */
	doc.addEventListener('click', function (e) {
		var a = e.target.closest('[data-track]');
		if (a) { track(a.getAttribute('data-track'), { link_url: a.getAttribute('href') || '' }); }
	});

	/* ---------- Verified-success events (thank-you pages / quiz result) ---------- */
	var succ = doc.querySelector('[data-success]');
	if (succ) {
		var ck = 'fvr_ok_' + succ.getAttribute('data-success-cookie');
		if (getCookie(ck)) { track(succ.getAttribute('data-success')); delCookie(ck); }
	}
	var res = doc.querySelector('[data-quiz-result]');
	if (res && getCookie('fvr_ok_quiz')) {
		track('quiz_complete', { quiz_result: res.getAttribute('data-quiz-result') });
		delCookie('fvr_ok_quiz');
	}

	/* ---------- Mobile nav ---------- */
	var toggle = doc.querySelector('.nav-toggle'), nav = doc.getElementById('primary-nav');
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', String(!open));
			nav.classList.toggle('is-open', !open);
		});
		doc.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) { toggle.click(); toggle.focus(); }
		});
		nav.addEventListener('click', function (e) { if (e.target.closest('a') && nav.classList.contains('is-open')) { toggle.click(); } });
	}

	/* ---------- Scroll reveal (skipped for reduced motion) ---------- */
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var items = doc.querySelectorAll('.reveal');
	if (reduce || !('IntersectionObserver' in window)) {
		items.forEach(function (el) { el.classList.add('is-visible'); });
	} else {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); } });
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		items.forEach(function (el) { io.observe(el); });
	}

	/* ---------- Lead-form client hints (server still validates everything) ---------- */
	doc.querySelectorAll('form[data-form-type]').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			var name = form.querySelector('input[name=first_name]'), email = form.querySelector('input[type=email]'), co = form.querySelector('input[name=consent]');
			var nameErr = name ? doc.getElementById(name.getAttribute('aria-describedby')) : null, err = doc.getElementById(email.getAttribute('aria-describedby'));
			var nameMsg = name && !name.value.trim() ? 'Please enter your first name.' : '', msg = '';
			if (!email.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { msg = 'Please enter a valid email address.'; }
			else if (co && !co.checked) { msg = 'Please tick the consent box so we can email you.'; }
			if (nameErr) { nameErr.textContent = nameMsg; nameErr.hidden = !nameMsg; name.toggleAttribute('aria-invalid', !!nameMsg); }
			err.textContent = msg; err.hidden = !msg; email.toggleAttribute('aria-invalid', !!msg && !email.value);
			if (nameMsg || msg) {
				e.preventDefault();
				(nameMsg ? name : (co && email.value && !co.checked ? co : email)).focus();
			}
		});
	});

	/* ---------- Calendly inline embed (lazy) + verified booking event ---------- */
	var cal = doc.querySelector('.calendly[data-calendly-url]');
	function loadCalendly() {
		if (loadCalendly.done) { return; }
		loadCalendly.done = true;
		var s = doc.createElement('script');
		s.src = 'https://assets.calendly.com/assets/external/widget.js'; s.async = true;
		s.onload = function () {
			if (window.Calendly) { window.Calendly.initInlineWidget({ url: cal.getAttribute('data-calendly-url'), parentElement: cal, utm: utm }); }
			var tries = 0, t = setInterval(function () { var f = cal.querySelector('iframe'); if (f) { f.setAttribute('scrolling', 'no'); f.style.height = '100%'; clearInterval(t); } else if (++tries > 40) { clearInterval(t); } }, 250);
		};
		s.onerror = function () { cal.innerHTML = '<p>The calendar could not load. Please use the booking button above.</p>'; };
		doc.head.appendChild(s);
	}
	if (cal) {
		cal.style.height = '700px'; /* explicit height so the iframe (height:100%) never collapses; refined by Calendly's page_height messages */
		if ('IntersectionObserver' in window) {
			var cio = new IntersectionObserver(function (en) { if (en[0].isIntersecting) { loadCalendly(); cio.disconnect(); } }, { rootMargin: '200px' });
			cio.observe(cal);
		} else { loadCalendly(); }
	}
	window.addEventListener('message', function (e) {
		if (e.origin !== 'https://calendly.com' || !e.data || typeof e.data.event !== 'string') { return; }
		/* Calendly reports its real content height: grow the container so the iframe never scrolls internally. */
		if (e.data.event === 'calendly.page_height' && cal && e.data.payload && e.data.payload.height) {
			var h = parseInt(String(e.data.payload.height).replace(/[^\d]/g, ''), 10);
			if (h > 200) { cal.style.minHeight = '0'; cal.style.height = h + 'px'; }
			return;
		}
		if (e.data.event !== 'calendly.event_scheduled') { return; }
		track('fit_call_booked');
		if (FVR.calendlyOn && FVR.fitThanksUrl) { window.location.href = FVR.fitThanksUrl; }
	});
})();
