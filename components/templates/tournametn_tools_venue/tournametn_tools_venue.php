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
                <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Tools?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">会場設定</div>
            </div>
            <div class="wrap">
                <div class="block">
                    <?php if($tournament_data['post_status']=="publish"):?>
                        <div class="about">日付と種目を選択</div>
                        <div class="about_value">
                            <form action="" method="post">
                                <?php create_options_date($tournament_data);
                                    create_options_event($tournament_data); ?>
                                <input type="submit" value="選択">
                            </form>
                        </div>
                    <?php if(!empty($_POST['tournament_date']) && !empty($_POST['tournament_event'])):?>
                        <div class="about">会場を選択</div>
                        <div class="about_value">
                            <?php $event_name = get_single_event_data( $_POST['tournament_event'] );?>
                            <p>日程：<?php echo $_POST['tournament_date'] ?>&nbsp;&nbsp;&nbsp;&nbsp;種目：<?php echo $event_name['event_name']; ?></p>
                            <form id="Update_Venue">
                                <?php get_venue_data_v2(); ?>
                                <?php if($_SESSION['level']===1): ?>
                                    <input type="hidden" name="tournament_id" value="<?php echo h($_GET['tournament_id']) ?>">
                                    <input type="hidden" name="tournament_date" value="<?php echo $_POST['tournament_date'] ?>">
                                    <input type="hidden" name="tournament_event" value="<?php echo $_POST['tournament_event'] ?>">
                                    <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                    <input type="submit" value="登録・更新" id="Update">
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endif;?>
                    <?php else:?>
                        <p style="font-size: 14px;">この機能は大会公開後に利用できます。</p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>

<script src="/components/templates/tournametn_tools_venue/tournametn_tools_venue.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>