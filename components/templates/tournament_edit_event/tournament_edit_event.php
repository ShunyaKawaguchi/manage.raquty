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

<div class="Tournament_edit_Event">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
        <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="Event_information">
            <div class="title_wrap">
                <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">種目情報</div>
            </div>
        <div class="wrap">
            <div class="block">
                <div class="about">種目一覧<?php if($_SESSION['level']===1):?><button style="margin-left:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Event/New?tournament_id=').$_GET['tournament_id']; ?>'">種目追加&nbsp;&gt;&gt;</button><?php endif;?></div>
                <div class="about_value">
                    <?php create_event_list( $_GET['tournament_id'] , $nonce_id); ?>
                </div>
            </div>
        </div>
        </div>
        </div>        
    </div>
</div>


<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>