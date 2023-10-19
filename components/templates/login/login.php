<?php
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();

//リダイレクト先指定
if(isset($_SESSION['request_url'])){
    $request_url = h($_SESSION['request_url']);
}else{
    $request_url = home_url('');
}

//ログイン失敗メッセージ
if(!empty($_SESSION['login_error_message'])){
    $notice = '入力内容に誤りがあります。再度、お試しください。';
}else{
    unset($_SESSION['login_error_message']);
}
?>

<div class="LOGIN">
    <div class="top">
        <div class="raquty">raquty&nbsp;Admin</div>
        <div class="title">ログイン画面</div>
    </div>
    <div class="Login_Form">
        <?php if(isset($notice)): ?><div class="login_notice"><?php echo $notice ?></div><?php endif; ?>
        <form method="post" action="" id="Login_Authorization">
            <div class="wrap">
                <label for="user_email">メールアドレス</label>
            </div>
            <div class="wrap">
                <input type="email" name="user_email" id="user_email">
            </div>
            <div class="wrap">
                <label for="user_password">パスワード</label>
            </div>
            <div class="wrap">
                <input type="password" name="user_password" id="user_password">
            </div>
            <div class="wrap">
                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                <input type="hidden" name="request_url" value="<?php echo $request_url ?>">
                <input type="submit" id="Login" value="ログイン">
            </div>
        </form>
        <div class="forget">パスワード再発行は<a href="">こちら</a></div>
    </div>
    <div class="notice">raquty&nbsp;Adminを利用するには、加盟登録が必要です。</div>
</div>

<?php
//エラーメッセージを初期化
if(!empty($_SESSION['login_error_message'])){
    unset($_SESSION['login_error_message']);
}
?>

</body>
</html>