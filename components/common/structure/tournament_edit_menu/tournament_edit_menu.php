<?php
    if ($tournament_data['post_status'] == 'publish') {
        $post_status = '公開中';
            $now = date('Y-m-d');
            if ($tournament_data['entry_start'] > $now) {
                $entry_status = '受付前';
            } elseif ($tournament_data['entry_start'] <= $now && $tournament_data['entry_end'] >= $now) {
                $entry_status = '受付中';
            } else {
                $entry_status = '締切';
            }
    } else {
        $post_status = '未公開';
        $entry_status = "--";
    }
        
?>

<div class="Tournament_Edit_Menu">
    <div class="half">
        <div class="sub_title">メニュー&nbsp;&nbsp;|</div>
        <div class="wrap">
            <div class="menu"><a href="<?php echo home_url('Tournament/View?tournament_id=').h($_GET['tournament_id']) ?>">大会情報</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Entry?tournament_id=').h($_GET['tournament_id']) ?>">エントリー</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Topics?tournament_id=').h($_GET['tournament_id']) ?>">トピックス</a></div>
            <div class="menu"><a href="<?php echo home_url('Tournament/View/Tools?tournament_id=').h($_GET['tournament_id']) ?>">事前準備</a></div>
        </div>
    </div>
    <div class="half">
        <div class="sub_title">大会状況&nbsp;&nbsp;|</div>
        <div class="wrap">
            <div class="about">受付状況：</div>
            <div class="about_value"><?php echo $entry_status ?></div>
        </div>
        <div class="wrap">
            <div class="about">公開状況：</div>
            <div class="about_value"><?php echo $post_status ?></div>
        </div>
    </div>
</div>