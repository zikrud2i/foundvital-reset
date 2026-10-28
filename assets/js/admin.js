/* Media picker for Reset Settings. */
jQuery(function ($) {
	$('.fvr-media').each(function () {
		var $wrap = $(this), $input = $wrap.find('input[type=hidden]'), $prev = $wrap.find('.fvr-media__preview'), frame;
		$wrap.on('click', '.fvr-media__pick', function (e) {
			e.preventDefault();
			if (!frame) {
				frame = wp.media({ title: 'Choose image', multiple: false, library: { type: 'image' } });
				frame.on('select', function () {
					var a = frame.state().get('selection').first().toJSON();
					$input.val(a.id);
					var src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
					$prev.empty().append($('<img>', { src: src, alt: '' }));
				});
			}
			frame.open();
		});
		$wrap.on('click', '.fvr-media__clear', function (e) {
			e.preventDefault();
			$input.val('');
			$prev.empty();
		});
	});
});
