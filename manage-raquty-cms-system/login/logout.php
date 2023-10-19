<?php
session_start();

//global_function読み込み
require_once(dirname(__FILE__).'/../../components/common/global-function.php');

//ログアウト関数
function raquty_user_logout(){
    unset($_SESSION['user_id']); 
    unset($_SESSION['group_name']);
    unset($_SESSION['user_name']);
    unset($_SESSION['level']);
    session_destroy();
}
if(!empty($_POST['logoutFunction'])){
    raquty_user_logout();
}
header("Location: " . home_url('Login_Authorization'));
?>