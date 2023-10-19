<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//セキュリティ対策のためnonce2利用
$nonce_id2 = raquty_nonce2();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
}else{
    header("Location: " . home_url('Tournament/List'));
}
//選手情報を取得
if ($_SESSION['nonce_id'] !== $_POST['raquty_nonce']) {
    header("Location: " . home_url('Tournament/List'));
}else{
    if(empty($_POST['entry_id'])){
        header("Location: " . home_url('Tournament/List'));
    }else{
        $player_info = get_entry_player_info( h($_GET['tournament_id']) , $_POST['entry_id'] );
        if(empty($player_info)){
            header("Location: " . home_url('Tournament/List'));
        }
    }
}
//種目取得
$event_data = get_single_event_data(h($_POST['event_id']));
//アラートを追加
alert('update_entry');
?>

<div class="Entry_About">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="Entry_information">
            <div class="title_wrap">
                <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Entry?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">エントリー情報</div>
            </div>
            <div class="wrap">
                <div class="block">
                    <div class="about">選手1</div>
                    <div class="about_value">
                        <?php $user_data = get_player_data($player_info['user1_id']);?>
                        <?php if($user_data == null){$player1_id = '登録なし';}else{$player1_id = $user_data['player_id'];};?>
                        <div class="info">選手ID&nbsp;:&nbsp;<?php echo $player1_id ?></div>
                        <div class="info">選手名&nbsp;:&nbsp;<?php echo $player_info['user1_name'] .'&nbsp;(&nbsp;'.$user_data['user_name_kana'].'&nbsp;)&nbsp;'?></div>
                        <div class="info">所&nbsp;&nbsp;&nbsp;属&nbsp;:&nbsp;<?php echo $player_info['user1_belonging'] ?></div>
                        <?php if(!empty($user_data['user_mail'])){ $user_email = $user_data['user_mail']; }else{ $user_email = '登録なし';}?>
                        <div class="info">連絡先&nbsp;:&nbsp;<?php echo $user_email ?></div>
                        <div class="info">受付状況&nbsp;:&nbsp;<?php if($player_info['draw_id']==9999){echo 'キャンセル済';}else{echo '受付済';} ?></div>
                    </div>
                </div>
                <?php if($event_data['type']=='ダブルス'):?>
                    <div class="block">
                        <div class="about">選手2</div>
                        <div class="about_value">
                            <?php $user_data2 = get_player_data($player_info['user2_id']);?>
                            <?php if($user_data2 == null){$player2_id = '登録なし';}else{$player2_id = $user_data2['player_id'];};?>
                            <div class="info">選手ID&nbsp;:&nbsp;<?php echo $player2_id ?></div>
                            <div class="info">選手名&nbsp;:&nbsp;<?php echo $player_info['user2_name'] .'&nbsp;(&nbsp;'.$user_data2['user_name_kana'].'&nbsp;)&nbsp;'?></div>
                            <div class="info">所&nbsp;&nbsp;&nbsp;属&nbsp;:&nbsp;<?php echo $player_info['user2_belonging'] ?></div>
                            <?php if(!empty($user_data2['user_mail'])){ $user_email2 = $user_data2['user_mail']; }else{ $user_email2 = '登録なし';}?>
                            <div class="info">連絡先&nbsp;:&nbsp;<?php echo $user_email2 ?></div>
                            <div class="info">受付状況&nbsp;:&nbsp;<?php if($player_info['draw_id']==9999){echo 'キャンセル済';}else{echo '受付済';} ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if($_SESSION['level']==1): ?>
                    <div class="block">
                        <div class="submit_wrap">
                            <form action="<?php echo home_url('/Tournament/View/Entry/Edit?tournament_id='.$_GET['tournament_id']) ?>" method="post">
                                <input type='hidden' name='entry_id' value='<?php echo $_POST['entry_id'] ?>'>
                                <input type="hidden" name="event_id" value="<?php echo $_POST['event_id'] ?>">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $_POST['raquty_nonce'] ?>">
                                <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id2 ?>">
                                <input type='submit' value='編集'>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>