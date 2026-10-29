/* FoundVital Reset – site behaviour: nav, reveal, event tracking, Calendly, form hints. */
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

	/* ---------- Analytics: events go to the Google tag that Site Kit loads (no tags loaded by the theme) ---------- */
	window.dataLayer = window.dataLayer || [];

	function track(name, params) {
		window.dataLayer.push(Object.assign({ event: name }, params || {}));
		if (typeof window.gtag === 'function') { window.gtag('event', name, params || {}); }
	}
	window.fvrTrack = track;

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
			var name = form.querySelector('input[name=first_name]'), email = form.querySelector('input[type=email]'), state = form.querySelector('select[name=state]'), co = form.querySelector('input[name=consent]');
			var checks = [
				[name, name && !name.value.trim() ? 'Please enter your first name.' : ''],
				[email, !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value) ? 'Please enter a valid email address.' : (co && !co.checked ? 'Please tick the consent box so we can email you.' : '')],
				[state, state && !state.value ? 'Please choose your state of residence.' : '']
			];
			var firstBad = null;
			checks.forEach(function (c) {
				if (!c[0]) { return; }
				var err = doc.getElementById(c[0].getAttribute('aria-describedby'));
				var consentOnly = c[0] === email && c[1] && email.value && co && !co.checked;
				if (err) { err.textContent = c[1]; err.hidden = !c[1]; }
				if (c[1] && !consentOnly) { c[0].setAttribute('aria-invalid', 'true'); } else { c[0].removeAttribute('aria-invalid'); }
				if (c[1] && !firstBad) { firstBad = consentOnly ? co : c[0]; }
			});
			if (firstBad) {
				e.preventDefault();
				firstBad.focus();
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
