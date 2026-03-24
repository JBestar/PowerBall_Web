(function () {
	'use strict';

	var pollTimer = null;

	// 기존 버튼 onclick 호환용 최소 스텁
	window.chatManager = window.chatManager || function (cmd) {
		if (cmd === 'refresh') {
			location.reload();
			return;
		}
		if (cmd === 'clearChat') {
			$('#roomList').empty();
			return;
		}
	};
	window.fontZoom = window.fontZoom || function () {};
	window.openChatRoom = window.openChatRoom || function () {};

	function escapeHtml(str) {
		return String(str || '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function renderMessages(messages, stickBottom) {
		var $roomList = $('#roomList');
		if (!$roomList.length) return;

		$roomList.empty();
		var rows = messages || [];
		for (var i = 0; i < rows.length; i++) {
			var m = rows[i] || {};
			var uid = escapeHtml(m.uid);
			var tm = escapeHtml(m.time);
			var msg = escapeHtml(m.message).replace(/\r\n|\r|\n/g, '<br>');
			var html = '<li style="border-bottom:1px solid #e5e5e5;padding:6px 8px;line-height:1.5;">'
				+ '<div><strong style="color:#404040;">' + uid + '</strong>'
				+ ' <span style="color:#999;font-size:11px;">[' + tm + ']</span>';
			if (m.canDelete) {
				html += ' <a href="#" class="chat-del" data-id="' + parseInt(m.id, 10) + '"'
					+ ' style="color:#c11a20;font-size:11px;">삭제</a>';
			}
			html += '</div>'
				+ '<div style="color:#555;word-break:break-all;">' + msg + '</div>'
				+ '</li>';
			$roomList.append(html);
		}

		if (stickBottom) {
			$roomList.scrollTop($roomList.prop('scrollHeight'));
		} else {
			// 최근글을 위에서부터 보이게
			$roomList.scrollTop(0);
		}
	}

	function loadChatList(stickBottom) {
		$.ajax({
			type: 'POST',
			url: (window.ACTION_BASE_URL || '/'),
			dataType: 'json',
			data: {
				view: 'action',
				action: 'ajaxChatList'
			}
		}).done(function (resp) {
			if (!resp || resp.state !== 'success') return;
			$('#connectUserCnt').text(resp.connectUserCnt || 0);
			renderMessages(resp.messages || [], !!stickBottom);
		});
	}

	function sendMessage() {
		var $msg = $('#roomMsg').length ? $('#roomMsg') : $('#msg');
		if (!$msg.length) return;
		var val = $.trim($msg.val());
		if (!val) return;

		$.ajax({
			type: 'POST',
			url: (window.ACTION_BASE_URL || '/'),
			dataType: 'json',
			data: {
				view: 'action',
				action: 'ajaxChatSend',
				message: val
			}
		}).done(function (resp) {
			if (!resp || resp.state !== 'success') return;
			$msg.val('');
			autoResizeRoomMsg();
			loadChatList(true);
		});
	}

	function syncInputLabel() {
		// placeholder 사용으로 별도 label 토글 불필요
	}

	function autoResizeRoomMsg() {
		var el = document.getElementById('roomMsg');
		if (!el) return;
		el.style.height = '22px';
		var nextH = Math.max(22, Math.min(el.scrollHeight, 58)); // 1~3줄
		el.style.height = nextH + 'px';
		adjustRoomLayout();
	}

	function adjustRoomLayout() {
		var $box = $('#roomListBox');
		var $list = $('#roomList');
		var $input = $('#roomInputWrap');
		if (!$box.length || !$list.length || !$input.length) return;

		var total = $box.innerHeight() || 573;
		var inputH = $input.outerHeight(true) || 30;
		var listH = Math.max(220, total - inputH);
		$list.css('height', listH + 'px');
	}

	function deleteMessage(id) {
		if (!id) return;
		$.ajax({
			type: 'POST',
			url: (window.ACTION_BASE_URL || '/'),
			dataType: 'json',
			data: {
				view: 'action',
				action: 'ajaxChatDelete',
				id: id
			}
		}).done(function (resp) {
			if (!resp || resp.state !== 'success') return;
			loadChatList();
		});
	}

	function activateRoomTab() {
		$('#channelList a').removeClass('on');
		$('#channelList a[type="roomList"]').addClass('on');
		$('#chatListBox').hide();
		$('#connectListBox').hide();
		$('#ruleBox').hide();
		$('#roomListBox').show();
		$('#roomMsg').focus();
	}

	$(document).ready(function () {
		activateRoomTab();

		$('#channelList a').on('click', function (e) {
			e.preventDefault();
			var tp = $(this).attr('type');
			$('#channelList a').removeClass('on');
			$(this).addClass('on');
			$('#chatListBox,#connectListBox,#ruleBox,#roomListBox').hide();
			if (tp === 'roomList') $('#roomListBox').show();
			else if (tp === 'connectList') $('#connectListBox').show();
			else if (tp === 'rule') $('#ruleBox').show();
			else $('#chatListBox').show();
			if (tp === 'roomList') $('#roomMsg').focus();
		});

		$('#sendBtn').on('click', function () {
			sendMessage();
		});
		$('#msg').on('keydown', function (e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				sendMessage();
			}
		});
		$('#msg').on('input keyup change focus blur', function () {
			syncInputLabel();
		});
		$('#roomSendBtn').on('click', function () {
			sendMessage();
		});
		$('#roomMsg').on('keydown', function (e) {
			// Shift+Enter: 줄바꿈, Enter: 전송
			if (e.keyCode === 13 && !e.shiftKey) {
				e.preventDefault();
				sendMessage();
			}
		});
		$('#roomMsg').on('input keyup change focus blur', function () {
			autoResizeRoomMsg();
			syncInputLabel();
		});

		$('#roomList').on('click', '.chat-del', function (e) {
			e.preventDefault();
			var id = parseInt($(this).attr('data-id'), 10);
			if (!id) return;
			deleteMessage(id);
		});

		loadChatList(false);
		autoResizeRoomMsg();
		syncInputLabel();
		adjustRoomLayout();
		$(window).on('resize', function () { adjustRoomLayout(); });
		pollTimer = setInterval(function () { loadChatList(false); }, 5000);
	});
})();

