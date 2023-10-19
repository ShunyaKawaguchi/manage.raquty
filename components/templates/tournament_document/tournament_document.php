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
    tournament_variable_settings( $tournament_data );
}else{
    header("Location: " . home_url('Tournament/List'));
}
?>

<div class="Tournament_Document">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
        <div class="main_contents">
            <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
            <div class="Documents">
                <div class="title_wrap">
                    <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">大会資料</div>
                </div>
                    <form method="post" enctype="multipart/form-data" id="Document">
                        <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                        <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
                        <div class="wrap">
                            <div class="block">
                                <div class="about">大会要綱</div>
                                <div class="about_value">
                                    <?php create_reqirement_table() ?>
                                </div>
                            </div>
                            <div class="block">
                                <div class="about">日程表</div>
                                <div class="about_value">
                                    <?php create_timetable_table() ?>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
</div>

<script src="/components/templates/tournament_document/tournament_document.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>