

<div class="Tournament_Edit_Menu">
    <div class="half">
        <div class="sub_title">メニュー&nbsp;&nbsp;|</div>
        <div class="wrap">
            <div class="menu"><a href="<?php echo home_url('Tournament/View?tournament_id=').h($_GET['tournament_id']) ?>">大会情報</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Entry?tournament_id=').h($_GET['tournament_id']) ?>">エントリー</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Topics?tournament_id=').h($_GET['tournament_id']) ?>">トピックス</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Tools?tournament_id=').h($_GET['tournament_id']) ?>">会場設定</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Draw?tournament_id=').h($_GET['tournament_id']) ?>">ドロー管理</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Operation?tournament_id=').h($_GET['tournament_id']) ?>">大会運営</a></div>
        </div>
    </div>
    
</div>