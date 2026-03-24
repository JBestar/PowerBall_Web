/**
 * 로그인 페이지: 입력 삭제 버튼 등
 */
(function($){
	$(function(){
		var $form = $('#loginForm');
		if (!$form.length) return;

		// 삭제 버튼 표시/숨김 및 클릭 시 입력 비우기
		$form.find('.input_box').each(function(){
			var $box = $(this);
			var $input = $box.find('input[type="text"], input[type="password"]');
			var $btn = $box.find('.btn_clear');

			function toggleClear(){
				if ($input.val().length > 0) {
					$btn.addClass('show').css('display', 'block');
				} else {
					$btn.removeClass('show').css('display', 'none');
				}
			}

			$input.on('input focus', toggleClear);
			$input.on('blur', function(){
				setTimeout(toggleClear, 150);
			});

			$btn.on('click', function(e){
				e.preventDefault();
				$input.val('').focus();
				$btn.removeClass('show').css('display', 'none');
			});

			toggleClear();
		});
	});
})(jQuery);
