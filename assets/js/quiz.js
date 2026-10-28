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
		var em = form.querySelector('#quiz-em'), co = form.querySelector('#quiz-co');
		if (em && em.value && co && !co.checked) {
			e.preventDefault(); show(total - 1, false); co.focus(); return;
		}
	});

	show(0, false);
})();
