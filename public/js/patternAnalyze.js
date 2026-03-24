/* patternAnalyze page logic (patternSearch / moreClick / patternSet toggle) */
(function () {
	'use strict';

	var state = {
		actionType: 'powerballOddEven',
		items: [],
		visibleItems: [],
		page: 0,
		perPage: 20,
		searching: false,
		pendingSearch: false,
		searchTimer: null,
		keepScrollOnAppend: false,
		endYN: 'N'
	};

	function toggleValue(actionType, value) {
		// powerball/number odd-even
		if (actionType === 'powerballOddEven' || actionType === 'numberOddEven') {
			return value === 'odd' ? 'even' : 'odd';
		}

		// powerball/number under-over
		if (actionType === 'powerballUnderOver' || actionType === 'numberUnderOver') {
			return value === 'under' ? 'over' : 'under';
		}

		// number period (small / middle / big)
		if (actionType === 'numberPeriod') {
			if (value === 'small') return 'middle';
			if (value === 'middle') return 'big';
			return 'small';
		}

		return value;
	}

	function renderPatternSet() {
		var $set = $('#patternSet');
		if (!$set.length) return;

		$set.empty();
		var items = state.visibleItems || [];
		for (var i = 0; i < items.length; i++) {
			var it = items[i] || {};
			var $li = $('<li/>').attr('data-idx', i);
			$li.append($('<div/>').addClass('round').text(it.round != null ? it.round : ''));
			$li.append($('<div/>').addClass('img').addClass(it.img || '').attr('data-value', it.value || ''));
			$set.append($li);
		}
	}

	function getPatternCnt() {
		var cnt = parseInt($('#patternCnt').val(), 10);
		if (isNaN(cnt)) cnt = 10;
		return Math.max(1, Math.min(26, cnt));
	}

	function applyVisibleItems() {
		var items = state.items || [];
		var cnt = getPatternCnt();
		var slice = items.slice(items.length - Math.min(cnt, items.length));

		// 최신 구간을 보여주되, 화면에선 오래된 -> 최신 순으로 유지
		state.visibleItems = slice.map(function (it) {
			var round = parseInt(it.round, 10);
			if (isNaN(round)) round = 0;
			var shortRound = round % 1000;
			return {
				round: shortRound,
				value: it.value,
				img: it.img
			};
		});
	}

	function ajaxPatternSet() {
		return $.ajax({
			type: 'POST',
			url: (window.ACTION_BASE_URL || '/'),
			dataType: 'json',
			data: {
				view: 'action',
				action: 'ajaxPatternSet',
				actionType: state.actionType,
				maxCnt: 26
			}
		}).then(function (resp) {
			if (!resp || resp.state !== 'success') return [];
			state.items = resp.items || [];
			applyVisibleItems();
			renderPatternSet();
			return state.items;
		});
	}

	function getSelectedPatternSeq() {
		var cnt = getPatternCnt();

		var items = state.visibleItems || [];
		if (!items.length) return { seq: [], cnt: cnt };

		var effectiveCnt = Math.min(cnt, items.length);
		var seq = items.slice(0, effectiveCnt).map(function (it) { return it && it.value; });
		return { seq: seq, cnt: effectiveCnt };
	}

	function ajaxPatternSearch() {
		var $tbody = $('#patternLogBox tbody.content');
		if (!$tbody.length) return;
		var isAppend = state.page > 0;
		var prevScrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
		var prevParentScrollTop = 0;
		try {
			if (window.parent && window.parent !== window) {
				prevParentScrollTop = window.parent.pageYOffset ||
					(window.parent.document && window.parent.document.documentElement ? window.parent.document.documentElement.scrollTop : 0) ||
					(window.parent.document && window.parent.document.body ? window.parent.document.body.scrollTop : 0) || 0;
			}
		} catch (e) {}

		var sel = getSelectedPatternSeq();
		var seq = sel.seq || [];
		var cnt = sel.cnt || 0;

		if (!seq.length || cnt <= 0) {
			$tbody.empty();
			$('.moreBox').hide();
			if (typeof heightResize === 'function') setTimeout(function () { heightResize(); }, 60);
			return;
		}

		if (state.searching) {
			state.pendingSearch = true;
			return;
		}

		state.searching = true;
		state.pendingSearch = false;
		$('#pageDiv').show();
		$('.moreBox').hide();

		return $.ajax({
			type: 'POST',
			url: (window.ACTION_BASE_URL || '/'),
			dataType: 'json',
			data: {
				view: 'action',
				action: 'ajaxPatternSearch',
				actionType: state.actionType,
				patternSeq: JSON.stringify(seq),
				patternCnt: cnt,
				page: state.page,
				perPage: state.perPage
			}
		}).then(function (data) {
			$('#pageDiv').hide();
			state.searching = false;

			if (!data || data.state !== 'success') return;
			if (state.page === 0) $tbody.empty();

			var $tmpl = $('#tmpl_patternLog').tmpl(data);
			$tbody.append($tmpl);

			state.endYN = data.endYN || 'N';
			if (state.endYN === 'Y') $('.moreBox').hide();
			else $('.moreBox').show();

			// 더보기 시 스크롤이 맨 위로 튀지 않게 현재 위치 유지
			if (isAppend && state.keepScrollOnAppend) {
				setTimeout(function () {
					window.scrollTo(0, prevScrollTop);
					try {
						if (window.parent && window.parent !== window) {
							window.parent.scrollTo(0, prevParentScrollTop);
						}
					} catch (e) {}
				}, 0);
				// heightResize/레이아웃 재계산 직후 한 번 더 복원
				setTimeout(function () {
					window.scrollTo(0, prevScrollTop);
					try {
						if (window.parent && window.parent !== window) {
							window.parent.scrollTo(0, prevParentScrollTop);
						}
					} catch (e) {}
				}, 140);
			}
			state.keepScrollOnAppend = false;

			if (typeof heightResize === 'function') setTimeout(function () { heightResize(); }, 120);

			if (state.pendingSearch) {
				state.page = 0;
				ajaxPatternSearch();
			}
		}).catch(function () {
			$('#pageDiv').hide();
			state.searching = false;
			state.keepScrollOnAppend = false;
			if (state.pendingSearch) {
				state.page = 0;
				ajaxPatternSearch();
			}
		});
	}

	function scheduleSearch(ms) {
		if (state.searchTimer) clearTimeout(state.searchTimer);
		state.searchTimer = setTimeout(function () {
			state.page = 0;
			ajaxPatternSearch();
		}, ms || 0);
	}

	// global functions for inline onclick
	window.patternSearch = function () {
		if (state.searching) return;
		state.page = 0;
		ajaxPatternSearch();
	};

	window.moreClick = function () {
		if (state.searching) return;
		state.page = Math.max(0, state.page + 1);
		state.keepScrollOnAppend = true;
		ajaxPatternSearch();
	};

	$(document).ready(function () {
		// init tab selection
		var $tabs = $('.patternSearchBox .tabMenu li');
		if ($tabs.length) {
			var rel = $tabs.filter('.on').attr('rel');
			state.actionType = rel || state.actionType;
		}

		// spinner init (jquery-ui)
		var $cnt = $('#patternCnt');
		if ($cnt.length && $.fn.spinner) {
			$cnt.spinner({
				min: 1,
				max: 26,
				step: 1
			});
		}
		$cnt.on('spin spinchange spinstop input change keyup', function () {
			applyVisibleItems();
			renderPatternSet();
			// 패턴 개수 변경 시 검색 결과도 즉시 갱신
			scheduleSearch(80);
		});

		// tab click
		$('.patternSearchBox .tabMenu li').on('click', function () {
			var rel = $(this).attr('rel');
			if (!rel) return;
			state.actionType = rel;
			$('.patternSearchBox .tabMenu li').removeClass('on');
			$(this).addClass('on');

			// change pattern type => refresh patternSet + results
			ajaxPatternSet().then(function () {
				state.page = 0;
				ajaxPatternSearch();
			});
		});

		// patternSet toggle (image cycle)
		$('#patternSet').on('click', 'li', function () {
			var idx = parseInt($(this).attr('data-idx'), 10);
			if (isNaN(idx)) return;
			if (!state.visibleItems[idx]) return;

			var curVal = state.visibleItems[idx].value;
			var newVal = toggleValue(state.actionType, curVal);
			state.visibleItems[idx].value = newVal;
			state.visibleItems[idx].img = 'sp-' + newVal;

			var $imgDiv = $(this).find('div.img');
			$imgDiv.attr('class', 'img ' + state.visibleItems[idx].img);
			$imgDiv.attr('data-value', newVal);

			// 패턴 이미지 토글 즉시 검색
			scheduleSearch(0);
		});

		// first load
		ajaxPatternSet().then(function () {
			state.page = 0;
			ajaxPatternSearch();
		});
	});
})();

