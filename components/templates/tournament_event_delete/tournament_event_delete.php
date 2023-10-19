<?php
// 必要なファイルをインクルード
require_once(dirname(__FILE__) . '/../../common/structure/sidebar/sidebar.php');

// 正しい遷移かどうかを判定
if ($_SESSION['nonce_id'] != $_GET['raquty_nonce']) {
    header("Location: " . home_url('Tournament/List'));
    exit; 
}else{
    // セキュリティ対策のためnonce2を利用（nonceとの重複を防ぐ）
    $nonce_id = raquty_nonce2();
}

// パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているか確認
if (!empty($_GET['tournament_id'])) {
    $tournament_data = check_tournament_existance($_GET['tournament_id']);
} else {
    header("Location: " . home_url('Tournament/List'));
    exit; 
}

// 削除権限確認
if (!empty($_GET['event_id']) && $_SESSION['level'] === 1) {
    $event_data = get_single_event_data(h($_GET['event_id']));
} else {
    header("Location: " . home_url('Tournament/List'));
    exit; 
}
?>

<div class="Venue_Delete">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo $tournament_data['tournament_name'] ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="Delete_venue">
            <div class="sub_title">種目削除</div>
                <div class="wrap">
                    <div class="block">
                        <form id="Delete_Event_Form">
                            <div class="section">
                                <p>種目「<?php echo $event_data['event_name'] ?>」を削除します。<br>
                                    この操作は取り消せません。慎重に行ってください。</p>
                            </div>
                            <div class="section">
                                <label for='user_password'>raquty&nbsp;Adminパスワード</label><br>
                                <input type="password" name="user_password" id="user_password" required>
                                <?php if(isset($_SESSION['delete_venue_pw_error'])):?>
                                <div class="Not_Correct_PW">パスワードが認証できませんでした。</div>
                                <?php unset($_SESSION['delete_venue_pw_error']);
                                      endif;?>
                            </div>
                            <div class="section_2">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $_SESSION['nonce_id'] ?>">
                                <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id ?>">
                                <input type="hidden" name="event_id" value="<?php echo $_GET['event_id']; ?>">
                                <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id']; ?>">
                                <input type="submit" value="削除" name="Delete" id="Delete">
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>



<script src="/components/templates/tournament_event_delete/tournament_event_delete.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>