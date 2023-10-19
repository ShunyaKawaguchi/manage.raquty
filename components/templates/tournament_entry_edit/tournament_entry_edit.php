<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//セキュリティ対策のためnonce3利用
$nonce_id3 = raquty_nonce3();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id']) && $_SESSION['level']===1){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
}else{
    header("Location: " . home_url('Tournament/List'));
}
//選手情報を取得
if ($_SESSION['nonce_id2'] !== $_POST['raquty_nonce2']) {
    header("Location: " . home_url('Tournament/List'));
}else{
    if(empty(h($_POST['entry_id']))){
        header("Location: " . home_url('Tournament/List'));
    }else{
        $player_info = get_entry_player_info( h($_GET['tournament_id']) , h($_POST['entry_id'] ));
        if(empty($player_info)){
            header("Location: " . home_url('Tournament/List'));
        }
    }
}
//種目取得
$event_data = get_single_event_data(h($_POST['event_id']));
?>

<div class="Entry_Edit">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
        <div class="main_contents">
            <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
            <div class="Entry_information">
                <div class="title_wrap">
                    <form action="<?php echo home_url('Tournament/View/Entry/About?tournament_id=').$_GET['tournament_id']?>" method="post">
                        <input type='hidden' name='entry_id' value='<?php echo $_POST['entry_id'] ?>'>
                        <input type="hidden" name="event_id" value="<?php echo $_POST['event_id'] ?>">
                        <input type='hidden' name='raquty_nonce' value='<?php echo $_POST['raquty_nonce'] ?>'>
                        <div class="sub_title"><input type='submit' style="margin-right:20px;" id="Entry_Back" value="&lt;&lt;">エントリー編集</div>
                    </form>
                </div>
                <form id="Edit_Player">
                    <div class="wrap">
                        <div class="player_title">選手1</div>
                        <div class="block">
                            <div class="about">
                                <label for="player1_name">選手名</label>
                            </div>
                            <div class="about_value">
                                <input type="text" name="player1_name" id="player1_name" value="<?php echo $player_info['user1_name'] ?>" placeholder="<?php echo $player_info['user1_name'] ?>" required>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">
                                <label for="player1_belonging">所属</label>
                            </div>
                            <div class="about_value">
                                <input type="text" name="player1_belonging" id="player1_belonging" value="<?php echo $player_info['user1_belonging'] ?>" placeholder="<?php echo $player_info['user1_belonging'] ?>" required>
                            </div>
                        </div>
                    </div>
                    <?php if($event_data['type']=='ダブルス'):?>
                        <div class="wrap">
                            <div class="player_title">選手2</div>
                            <div class="block">
                                <div class="about">
                                    <label for="player1_name">選手名</label>
                                </div>
                                <div class="about_value">
                                    <input type="text" name="player2_name" id="player2_name" value="<?php echo $player_info['user2_name'] ?>" placeholder="<?php echo $player_info['user2_name'] ?>" required>
                                </div>
                            </div>
                            <div class="block">
                                <div class="about">
                                    <label for="player2_belonging">所属</label>
                                </div>
                                <div class="about_value">
                                    <input type="text" name="player2_belonging" id="player2_belonging" value="<?php echo $player_info['user2_belonging'] ?>" placeholder="<?php echo $player_info['user2_belonging'] ?>" required>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="wrap">
                        <div class="block">
                            <div class="about">受付ステータス</div>
                            <div class="about_value">
                                <select name="player_status">
                                    <option value="">受付済</option>
                                    <option value="9999" <?php if($player_info['draw_id']==9999){echo 'selected';}?>>キャンセル済</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="wrap" style="padding-bottom:10px">
                        <div class="block">
                            <div class="about" style="font-family: Audiowide;">raquty&nbsp;Adminパスワード</div>
                            <div class="about_value">
                                <input type="password" name="user_password" id="user_password" required>
                                <?php if(isset($_SESSION['edit_player_pw_error'])):?>
                                <div class="Not_Correct_PW">パスワードが認証できませんでした。</div>
                                <?php unset($_SESSION['edit_player_pw_error']);
                                      endif;?>

                            </div>
                        </div>
                    </div>
                    <div class="wrap" style="border: none;">
                        <div class="block" style="margin:0;">
                            <div class="submit_wrap">
                                <?php $log_before = 'user1_id:'.$player_info['user1_id'].'/user1_name:'.$player_info['user1_name'].'/user1_belonging:'.$player_info['user1_belonging'].'/user2_id:'.$player_info['user2_id'].'/user2_name:'.$player_info['user2_name'].'/user2_belonging:'.$player_info['user2_belonging'].'/status:'.$player_info['draw_id']; ?>
                                <input type="hidden" name="log_before" value="<?php echo $log_before ?>">
                                <input type='hidden' name='entry_id' value='<?php echo $_POST['entry_id'] ?>'>
                                <input type="hidden" name="tournament_id" value="<?php echo h($_GET['tournament_id']) ?>">
                                <input type="hidden" name="event_id" value="<?php echo $_POST['event_id'] ?>">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $_POST['raquty_nonce'] ?>">
                                <input type="hidden" name="raquty_nonce2" value="<?php echo $_POST['raquty_nonce2'] ?>">
                                <input type="hidden" name="raquty_nonce3" value="<?php echo $nonce_id3 ?>">
                                <input type='submit' value='更新' id="Update_data">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>

<script src="/components/templates/tournament_entry_edit/tournament_entry_edit.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>