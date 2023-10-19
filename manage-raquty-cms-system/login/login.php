<?php 
session_start();
//データベース認証
require_once(dirname(__FILE__).'/../connect-databese.php');
//共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../components/common/global-function.php');

//ログイン関数
if($_SESSION['nonce_id']==$_POST['raquty_nonce']){
    raquty_user_login(h($_POST['request_url']));
}else{
    $_SESSION['login_error_message'] = 1;
    header("Location: " . home_url('login_authorization'));
}

//データベース接続解除
require_once(dirname(__FILE__).'/../disconnect-database.php');
?>