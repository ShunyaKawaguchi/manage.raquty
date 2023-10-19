<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
}else{
    header("Location: " . home_url('Tournament/List'));
}
?>

<div class="Tools">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="Venue_Select">
            <div class="title_wrap">
                <div class="sub_title">会場設定</div><button style="margin-left:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Tools/Venue?tournament_id=').$_GET['tournament_id']; ?>'">編集</button>
            </div>
            <div class="wrap">
                <div class="block">
                    <?php get_tournament_venue($tournament_data); ?>
                </div>
            </div>
        </div>
    </div>
<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>