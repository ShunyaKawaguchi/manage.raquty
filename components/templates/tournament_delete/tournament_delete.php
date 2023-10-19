<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id']) && $_SESSION['level'] === 1){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
    tournament_variable_settings( $tournament_data );
}else{
    header("Location: " . home_url('Tournament/List'));
}
?>

<div class="Tournament_Delete">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
        <div class="main_contents">
            <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
            <div class="Delete_Tournament">
                <div class="sub_title">大会削除</div>
                <div class="wrap">
                    <div class="block">
                        <form method="post" id="Delete_Event_Form">
                            <div class="section">
                                <p>大会「<?php echo h($tournament_data['tournament_name']) ?>」を削除します。<br>
                                    この操作は取り消せません。慎重に行ってください。</p>
                            </div>
                            <div class="section">
                                <label for='user_password'>raquty&nbsp;Adminパスワード</label><br>
                                <input type="password" name="user_password" id="user_password" required>
                                <?php if(isset($_SESSION['delete_tournament_pw_error'])):?>
                                <div class="Not_Correct_PW">パスワードが認証できませんでした。</div>
                                <?php unset($_SESSION['delete_tournament_pw_error']);
                                        endif;?>
                            </div>
                            <div class="section_2">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $_SESSION['nonce_id'] ?>">
                                <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id']; ?>">
                                <input type="submit" value="削除" name="Delete" id="Delete">
                            </div>
                        </form>   
                    </div>
                </div>
            </div>
        </div>
</div>

<script src="/components/templates/tournament_delete/tournament_delete.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>