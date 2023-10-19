<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( $_GET['tournament_id'] );
}else{
    header("Location: " . home_url('Tournament/List'));
}
?>

<div class="Tournament_Topics">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
            <?php if(!get_tournamnet_status()):?>
                <p style='font-size:14px;margin:30px;'>「トピックス」は大会情報を公開してから利用可能な機能です。</p>
            <?php else:?>
                <div class="Topics_information">
                    <form method="post" id="New_Topics_Form">
                        <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                        <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
                        <div class="title_wrap">トピックス一覧<?php if($_SESSION['level']===1):?><input type="submit" style="margin-left:20px;" id="New_Topics" value="新規作成&nbsp;&gt;&gt;"><?php endif;?></div>
                    </form>
                    <?php create_topics_list(); ?>
                </div>
            <?php endif;?>
    </div>
</div>

<script src="/components/templates/tournament_topics/tournament_topics.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>