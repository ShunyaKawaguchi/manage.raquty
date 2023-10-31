<?php 
    if(isset($_GET['venue_id'])){
        $venue_id = $_GET['venue_id'];
    }else{
        $venue_id = $_GET['venue_id'] = '';
    }
    if(isset($_GET['tournament_id'])){
        $tournament_id = $_GET['tournament_id'];
    }else{
        $tournament_id = '';
    }
    check_venue_existance($venue_id, $tournament_id);
?>

<div class="Operation_menu">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View?tournament_id=').h($_GET['tournament_id']) ; ?>'">&lt;&lt;</button>大会運営メニュー</div>
    <div class="menu">
        <a href="<?php echo home_url('Tournament/View/Operation/OOP?tournament_id=').h($_GET['tournament_id']) ?>" class="single_menu">
            <div class="title">OOP</div>
            <p>大会のオーダーオブプレイを更新できます。</p>
        </a>
        <a href="<?php echo home_url('Tournament/View/Operation/Tournament?tournament_id=').h($_GET['tournament_id']) ?>" class="single_menu">
            <div class="title">ドロー</div>
            <p>大会ドローの途中経過をリアルタイムで確認できます。</p>
        </a>
        <a href="<?php echo home_url('Tournament/View/Operation/Court_situation?tournament_id=').h($_GET['tournament_id']) ?>" class="single_menu">
            <div class="title">コート状況</div>
            <p>各コートの試合入り状況がコートマップ上で確認できます。</p>
        </a>
    </div>

</div>

<script src="/components/templates/tournament_menu/tournament_menu.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>