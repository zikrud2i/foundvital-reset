/* FoundVital Reset – stepped quiz, email last.
   Steps: the questions, then (when data-final="1") a final step that asks for first name and email.
   Answers stay in the page until the end. The final submit goes to the server in the background; on success
   the visitor is sent to the matching result page, on failure an error shows and every answer is kept.
   Without JS every step is shown on one page and the form still submits normally; the server validates everything. */
(function () {
	'use strict';
	var form = document.querySelector('form.quiz');
	if (!form) { return; }

	var steps = Array.prototype.slice.call(form.querySelectorAll('.quiz__step'));
	var nBack = form.querySelector('[data-quiz-back]');
	var nNext = form.querySelector('[data-quiz-next]');
	var nSubmit = form.querySelector('[data-quiz-submit]');
	var progress = form.querySelector('.quiz__progress');
	var bar = form.querySelector('.quiz__bar');
	var fill = bar.querySelector('span');
	var count = form.querySelector('.quiz__count');
	var consent = form.querySelector('[data-quiz-consent]');
	var questions = parseInt(form.getAttribute('data-questions'), 10) || steps.length;
	var hasFinal = form.getAttribute('data-final') === '1';
	var total = steps.length;
	var capErr = form.querySelector('#quiz-caperr');
	var i = 0;
	var started = false;
	var completed = false;
	var sending = false;

	form.classList.add('is-stepped');
	nNext.hidden = false;

	function answered(step) { return !!step.querySelector('input[type=radio]:checked'); }
	function isFinal(k) { return hasFinal && k === total - 1; }

	/* Meta custom events carry no parameters. They only exist on pages where the pixel is loaded. */
	function metaCustom(name) { if (typeof window.fbq === 'function') { window.fbq('trackCustom', name); } }

	function show(idx, focus) {
		i = idx;
		steps.forEach(function (s, k) { s.hidden = k !== i; s.classList.toggle('is-current', k === i); });
		var last = i === total - 1;
		var final = isFinal(i);
		nBack.hidden = i === 0;
		nNext.hidden = last;
		nSubmit.hidden = !last;
		if (consent) { consent.hidden = !final; }
		progress.hidden = false;
		var done = final ? questions : i;
		bar.setAttribute('aria-valuenow', String(done));
		fill.style.width = Math.round((done / questions) * 100) + '%';
		count.textContent = final ? 'Last step' : 'Question ' + (i + 1) + ' of ' + questions;
		if (final && !completed) {
			completed = true;
			if (window.fvrTrack) { window.fvrTrack('quiz_complete'); }
			metaCustom('QuizComplete');
		}
		if (focus) {
			var first = steps[i].querySelector('input:checked, input, select, legend');
			if (first) { first.focus({ preventScroll: false }); }
		}
	}

	/* The final step: first name and a valid email, both required. */
	function validateFinal() {
		var fn = form.querySelector('#quiz-fn'), em = form.querySelector('#quiz-em');
		if (!fn || !em) { return true; }
		var gap = null, msg = '';
		if (!fn.value.trim()) { gap = fn; msg = 'Please enter your first name.'; }
		else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value)) { gap = em; msg = 'Please enter a valid email address.'; }
		[fn, em].forEach(function (f) { if (f === gap) { f.setAttribute('aria-invalid', 'true'); } else { f.removeAttribute('aria-invalid'); } });
		if (capErr) { capErr.textContent = msg; capErr.hidden = !msg; }
		if (gap) { gap.focus(); return false; }
		return true;
	}

	function validate() {
		if (isFinal(i)) { return validateFinal(); }
		var step = steps[i];
		if (step.querySelector('input[type=radio]') && !answered(step)) {
			var err = step.querySelector('.field__error');
			if (err) { err.hidden = false; }
			step.querySelector('input[type=radio]').focus();
			return false;
		}
		return true;
	}

	nNext.addEventListener('click', function () { if (validate() && i < total - 1) { show(i + 1, true); } });
	nBack.addEventListener('click', function () { if (i > 0) { show(i - 1, true); } });

	form.addEventListener('change', function (e) {
		if (e.target.type === 'radio') {
			var err = steps[i].querySelector('.field__error'); if (err) { err.hidden = true; }
			if (!started) {
				started = true; /* question 1 answered */
				if (window.fvrTrack) { window.fvrTrack('quiz_start'); }
				metaCustom('QuizStart');
			}
		}
	});

	/* Enter moves to the next step; on the final step it submits. */
	form.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'TEXTAREA' && !isFinal(i) && i < total - 1) {
			e.preventDefault();
			if (validate()) { show(i + 1, true); }
		}
	});

	function showError(msg) {
		var target = capErr && isFinal(i) ? capErr : null;
		if (target) { target.textContent = msg; target.hidden = false; target.scrollIntoView({ block: 'center' }); return; }
		var box = form.querySelector('.alert--error');
		if (!box) { box = document.createElement('div'); box.className = 'alert alert--error'; box.setAttribute('role', 'alert'); form.insertBefore(box, form.firstChild); }
		box.textContent = msg;
		box.scrollIntoView({ block: 'center' });
	}

	form.addEventListener('submit', function (e) {
		if (sending) { e.preventDefault(); return; }
		/* Every question first, then the email step; jump back to the first gap otherwise. */
		for (var k = 0; k < total; k++) {
			if (!isFinal(k) && !answered(steps[k])) {
				e.preventDefault(); show(k, true);
				var err = steps[k].querySelector('.field__error'); if (err) { err.hidden = false; }
				return;
			}
		}
		if (hasFinal && !validateFinal()) { e.preventDefault(); show(total - 1, false); return; }
		if (!window.fetch || !window.FormData) { return; } /* plain submit; the server redirects */

		e.preventDefault();
		sending = true;
		var label = nSubmit.querySelector('span'), old = label ? label.textContent : '';
		nSubmit.disabled = true;
		if (label) { label.textContent = 'One moment…'; }
		var data = new FormData(form);
		data.append('fvr_ajax', '1');
		/* getAttribute: the hidden field named "action" shadows form.action */
		fetch(form.getAttribute('action'), { method: 'POST', body: data, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res && res.success && res.data && res.data.redirect) {
					/* Kit has accepted the sign-up: one Lead, no parameters, then on to the result page. */
					if (window.fvrTrack) { window.fvrTrack('quiz_signup_success'); }
					var go = function () { window.location.href = res.data.redirect; };
					if (typeof window.fbq === 'function') { window.fbq('track', 'Lead'); setTimeout(go, 350); } else { go(); }
					return;
				}
				throw new Error((res && res.data && res.data.message) || 'Something went wrong. Please try again.');
			})
			.catch(function (err) {
				sending = false;
				nSubmit.disabled = false;
				if (label) { label.textContent = old; }
				showError(err && err.message ? err.message : 'Something went wrong. Please try again.');
			});
	});

	show(0, false);
})();
