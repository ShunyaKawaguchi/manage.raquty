<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し（Upddateと共有）
require_once(dirname(__FILE__).'/../venue_edit/material.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//会場存在確認&削除権限確認
if(!empty($_POST['template_id']) && $_SESSION['level']===1){
    $venue_data = check_venue_existance( $_POST['template_id'] );
}else{
    header("Location: " . home_url('Tournament/Venue'));
}
?>

<div class="Venue_Delete">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/Venue/View?venue_id=').$_POST['template_id']; ?>'">&lt;&lt;</button>会場管理</div>
    <div class="main_contents">
        <div class="Delete_venue">
            <div class="sub_title">会場削除</div>
                <div class="wrap">
                    <div class="block">
                        <form method="post" id="Delete_Venue_Form">
                            <div class="section">
                                <p>会場テンプレート「<?php echo $venue_data['venue_name'] ?>」を削除します。<br>
                                    既に大会と会場を紐づけている場合、該当の大会の会場データは削除されます。</p>
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
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="hidden" name="template_id" value="<?php echo $_POST['template_id']; ?>">
                                <input type="submit" value="削除" name="Delete" id="Delete">
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>



<script src="/components/templates/venue_delete/venue_delete.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>