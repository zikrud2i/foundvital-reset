/* FoundVital Reset – stepped quiz. Step 0 is the required email subscription ("Start My Free Reset Quiz"),
   then one step per question. Without JS every step is shown on one page and the form still submits;
   the server validates everything again. */
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
	var questions = parseInt(form.getAttribute('data-questions'), 10) || steps.length;
	var hasIntro = form.getAttribute('data-intro') === '1';
	var offset = hasIntro ? 1 : 0; /* index of the first question step */
	var total = steps.length;
	var nextLabel = nNext.querySelector('span');
	var capErr = form.querySelector('#quiz-caperr');
	var i = 0;
	var started = false;

	form.classList.add('is-stepped');
	nNext.hidden = false;

	function answered(step) { return !!step.querySelector('input[type=radio]:checked'); }

	function show(idx, focus) {
		i = idx;
		steps.forEach(function (s, k) { s.hidden = k !== i; s.classList.toggle('is-current', k === i); });
		var isIntro = hasIntro && i === 0;
		var isLast = i === total - 1;
		nBack.hidden = i === 0;
		nNext.hidden = isLast;
		nSubmit.hidden = !isLast;
		if (nextLabel) { nextLabel.textContent = nNext.getAttribute(isIntro ? 'data-label-start' : 'data-label-next') || nextLabel.textContent; }
		progress.hidden = isIntro;
		var done = Math.max(0, i - offset); /* questions already answered */
		bar.setAttribute('aria-valuenow', String(done));
		fill.style.width = Math.round((done / questions) * 100) + '%';
		count.textContent = isIntro ? '' : 'Question ' + (i - offset + 1) + ' of ' + questions;
		if (focus) {
			var first = steps[i].querySelector('input:checked, input, select, legend');
			if (first) { first.focus({ preventScroll: false }); }
		}
	}

	/* The email step: first name, email, state and consent are all required before the quiz starts. */
	function validateIntro() {
		var fn = form.querySelector('#quiz-fn'), em = form.querySelector('#quiz-em'), st = form.querySelector('#fvr-st-quiz'), co = form.querySelector('#quiz-co');
		if (!fn || !em || !st || !co) { return true; }
		var gap = null, msg = '';
		if (!fn.value.trim()) { gap = fn; msg = 'Please enter your first name.'; }
		else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value)) { gap = em; msg = 'Please enter a valid email address.'; }
		else if (!st.value) { gap = st; msg = 'Please choose your state of residence.'; }
		else if (!co.checked) { gap = co; msg = 'Please tick the consent box to subscribe. A FoundVital email subscription is required to take the quiz.'; }
		[fn, em, st].forEach(function (f) { if (f === gap) { f.setAttribute('aria-invalid', 'true'); } else { f.removeAttribute('aria-invalid'); } });
		if (capErr) { capErr.textContent = msg; capErr.hidden = !msg; }
		if (gap) { gap.focus(); return false; }
		return true;
	}

	function validate() {
		if (hasIntro && i === 0) { return validateIntro(); }
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
			if (!started && window.fvrTrack) { started = true; window.fvrTrack('quiz_start'); }
		}
	});

	/* Pressing Enter in the email step should start the quiz, not submit a half-finished form. */
	form.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && hasIntro && i === 0 && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'TEXTAREA') {
			e.preventDefault();
			if (validate()) { show(1, true); }
		}
	});

	form.addEventListener('submit', function (e) {
		/* Email step first, then every question; jump back to the first gap otherwise. */
		if (hasIntro && !validateIntro()) { e.preventDefault(); show(0, false); return; }
		for (var k = offset; k < total; k++) {
			if (!answered(steps[k])) {
				e.preventDefault(); show(k, true);
				var err = steps[k].querySelector('.field__error'); if (err) { err.hidden = false; }
				return;
			}
		}
	});

	show(0, false);
})();
