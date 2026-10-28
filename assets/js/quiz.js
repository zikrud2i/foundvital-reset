/* FoundVital Reset – stepped quiz. Without JS every question is shown on one page and the form still submits. */
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
	var total = steps.length;
	var i = 0;
	var started = false;

	form.classList.add('is-stepped');
	progress.hidden = false;
	nNext.hidden = false;

	function answered(step) { return !!step.querySelector('input[type=radio]:checked'); }

	function show(idx, focus) {
		i = idx;
		steps.forEach(function (s, k) { s.hidden = k !== i; s.classList.toggle('is-current', k === i); });
		var isLast = i === total - 1;
		nBack.hidden = i === 0;
		nNext.hidden = isLast;
		nSubmit.hidden = !isLast;
		var done = Math.min(i, questions);
		bar.setAttribute('aria-valuenow', String(done));
		fill.style.width = Math.round((done / total) * 100) + '%';
		count.textContent = (i + 1 <= questions ? 'Question ' + (i + 1) + ' of ' + questions : 'Last step') ;
		if (focus) {
			var first = steps[i].querySelector('input:checked, input, legend');
			if (first) { first.focus({ preventScroll: false }); }
		}
	}

	function validate() {
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

	form.addEventListener('submit', function (e) {
		/* Make sure every question was answered; jump back to the first gap otherwise. */
		for (var k = 0; k < questions; k++) {
			if (!answered(steps[k])) { e.preventDefault(); show(k, true); var err = steps[k].querySelector('.field__error'); if (err) { err.hidden = false; } return; }
		}
		/* Signing up is optional, but once any detail is filled in, all of them (and consent) are required. */
		var fn = form.querySelector('#quiz-fn'), em = form.querySelector('#quiz-em'), st = form.querySelector('#fvr-st-quiz'), co = form.querySelector('#quiz-co');
		var capErr = form.querySelector('#quiz-caperr');
		if (!em || !fn || !st || !co) { return; }
		var any = fn.value.trim() || em.value.trim() || st.value;
		var gap = null, msg = '';
		if (any) {
			if (!fn.value.trim()) { gap = fn; msg = 'Please enter your first name.'; }
			else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value)) { gap = em; msg = 'Please enter a valid email address.'; }
			else if (!st.value) { gap = st; msg = 'Please choose your state of residence.'; }
			else if (!co.checked) { gap = co; msg = 'Please tick the consent box so we can email you, or clear the fields to skip.'; }
		}
		[fn, em, st].forEach(function (f) { if (f === gap) { f.setAttribute('aria-invalid', 'true'); } else { f.removeAttribute('aria-invalid'); } });
		if (capErr) { capErr.textContent = msg; capErr.hidden = !msg; }
		if (gap) { e.preventDefault(); show(total - 1, false); gap.focus(); }
	});

	show(0, false);
})();
