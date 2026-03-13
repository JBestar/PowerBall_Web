<?php
/**
 * 메인 우측 영역: 보드박스(유머/포토/분석픽공유/자유) + 배너 + 분석 영역
 * main.php 에서 뼈대만 두고 실제 구현은 이 파일에서 처리
 */
$list_humor = $list_humor ?? [];
$list_pick  = $list_pick ?? [];
$list_free  = $list_free ?? [];
$list_photo = $list_photo ?? [];
?>
<div class="boardBox" id="boardBox">
    <ul class="menu">
        <li class="on" rel="humor">유머</li>
        <li rel="photo">포토</li>
        <li rel="pick">분석픽공유</li>
        <li class="none" rel="free">자유</li>
    </ul>
    <?php
    $list = $list_humor;
    $half = (int)ceil(count($list) / 2);
    $leftList = array_slice($list, 0, $half);
    $rightList = array_slice($list, $half);
    $bo = 'humor';
    ?>
    <div class="listBox" id="list_humor" style="display:block;">
        <div class="left">
            <ul class="list">
                <?php foreach ($leftList as $row) : ?>
                <li>
                    <img src="<?php echo site_furl('images/icon_text.png'); ?>" width="30" height="26" alt="">
                    <a href="/bbs/board.php?bo_table=<?= $bo ?>&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>"><?= esc($row->title) ?></a>
                    <span class="comment">[<?= (int)($row->comment_count ?? 0) ?>]</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bar"></div>
        <div class="right">
            <ul class="list">
                <?php foreach ($rightList as $row) : ?>
                <li>
                    <img src="<?php echo site_furl('images/icon_text.png'); ?>" width="30" height="26" alt="">
                    <a href="/bbs/board.php?bo_table=<?= $bo ?>&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>"><?= esc($row->title) ?></a>
                    <span class="comment">[<?= (int)($row->comment_count ?? 0) ?>]</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="listBox" id="list_photo" style="display:none;">
        <ul class="list">
            <?php foreach ($list_photo as $row) :
                $imgSrc = !empty($row->file_path) ? site_furl('uploads/photos/'.$row->file_path) : site_furl('images/transparent.png');
            ?>
            <li class="photo">
                <a href="/bbs/board.php?bo_table=photo&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>">
                    <span class="image"><img src="<?= esc($imgSrc) ?>" alt="<?= esc($row->title) ?>"></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    $list = $list_pick;
    $half = (int)ceil(count($list) / 2);
    $leftList = array_slice($list, 0, $half);
    $rightList = array_slice($list, $half);
    $bo = 'pick';
    ?>
    <div class="listBox" id="list_pick" style="display:none;">
        <div class="left">
            <ul class="list">
                <?php foreach ($leftList as $row) : ?>
                <li>
                    <img src="<?php echo site_furl('images/icon_text.png'); ?>" width="30" height="26" alt="">
                    <a href="/bbs/board.php?bo_table=<?= $bo ?>&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>"><?= esc($row->title) ?></a>
                    <span class="comment">[<?= (int)($row->comment_count ?? 0) ?>]</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bar"></div>
        <div class="right">
            <ul class="list">
                <?php foreach ($rightList as $row) : ?>
                <li>
                    <img src="<?php echo site_furl('images/icon_text.png'); ?>" width="30" height="26" alt="">
                    <a href="/bbs/board.php?bo_table=<?= $bo ?>&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>"><?= esc($row->title) ?></a>
                    <span class="comment">[<?= (int)($row->comment_count ?? 0) ?>]</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
    $list = $list_free;
    $half = (int)ceil(count($list) / 2);
    $leftList = array_slice($list, 0, $half);
    $rightList = array_slice($list, $half);
    $bo = 'free';
    ?>
    <div class="listBox" id="list_free" style="display:none;">
        <div class="left">
            <ul class="list">
                <?php foreach ($leftList as $row) : ?>
                <li>
                    <img src="<?php echo site_furl('images/icon_text.png'); ?>" width="30" height="26" alt="">
                    <a href="/bbs/board.php?bo_table=<?= $bo ?>&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>"><?= esc($row->title) ?></a>
                    <span class="comment">[<?= (int)($row->comment_count ?? 0) ?>]</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bar"></div>
        <div class="right">
            <ul class="list">
                <?php foreach ($rightList as $row) : ?>
                <li>
                    <img src="<?php echo site_furl('images/icon_text.png'); ?>" width="30" height="26" alt="">
                    <a href="/bbs/board.php?bo_table=<?= $bo ?>&wr_id=<?= (int)$row->wr_id ?>" target="mainFrame" title="<?= esc($row->title) ?>"><?= esc($row->title) ?></a>
                    <span class="comment">[<?= (int)($row->comment_count ?? 0) ?>]</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<script>
(function(){
    function initBoardBoxTabs() {
        var box = document.getElementById('boardBox');
        if (!box) return;
        var menuItems = box.querySelectorAll('ul.menu li');
        var listBoxes = box.querySelectorAll('.listBox');
        if (!menuItems.length || !listBoxes.length) return;
        for (var i = 0; i < menuItems.length; i++) {
            menuItems[i].addEventListener('click', function(e) {
                var rel = this.getAttribute('rel');
                if (!rel) return;
                e.preventDefault();
                for (var j = 0; j < menuItems.length; j++) menuItems[j].classList.remove('on');
                this.classList.add('on');
                for (var k = 0; k < listBoxes.length; k++) {
                    listBoxes[k].style.display = (listBoxes[k].id === 'list_' + rel) ? 'block' : 'none';
                }
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBoardBoxTabs);
    } else {
        initBoardBoxTabs();
    }
})();
</script>
<ul class="bannerBox" id="banner_main_area">
    <script>
    document.write("<d"+"iv id='mobonDivBanner_205215'><iframe name='ifrad' id='mobonIframe_205215' src='//www.mediacategory.com/servlet/adBanner?from="+escape(document.referrer)+"&s=205215&igb=71&iwh=200_200&cntad=1&cntsr=2' frameborder='0' scrolling='no' style='height: 200px; width:200px;'></iframe></div>");
    </script>
</ul>
<!-- inner-right (선배님 구조: 파란색으로 선택된 핵심 영역 = 이 주석 아래 중첩 div.inner-right) -->
<div class="inner-right">
    <!-- 메인프레임만. powerballMiniViewDiv 제거 → 메인헤더 중복 원인 제거 -->
    <iframe name="mainFrame" id="mainFrame" src="<?php echo site_furl('frame/dayLog'); ?>?t=<?php echo time(); ?>" scrolling="no" style="width:100%; height:600px; border:1px solid #ddd; overflow:hidden;"></iframe>
</div>
<!-- //inner-right -->
<!-- tmpl -->
<script id="tmpl_board" type="text/x-jquery-tmpl">
<li>
    <img src="<?php echo site_furl('images'); ?>/icon_${type}.png" width="30" height="26" alt="">
    <a href="/bbs/board.php?bo_table=${bo_table}&wr_id=${idx}" target="mainFrame" title="${title}">${title}</a>
    {{html commentView}}
    {{html newIcon}}
</li>
</script>
<script id="tmpl_photo" type="text/x-jquery-tmpl">
<li class="photo">
    <a href="/bbs/board.php?bo_table=${bo_table}&wr_id=${idx}" target="mainFrame" title="${title}">
        <span class="image"><img src="<?php echo site_furl('uploads/photos'); ?>/${file_path}" alt="${title}" class="image"></span>
    </a>
</li>
</script>
<!-- //tmpl -->
