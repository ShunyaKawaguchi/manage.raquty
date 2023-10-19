<?php 
//セッションを手動で開始
session_start(); 
//データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
//共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
//ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if ($_SESSION['nonce_id'] == $_POST['raquty_nonce']) {
// INSERTクエリを実行
$insertQuery = "INSERT INTO group_topics ( post_title, post_date, post_modified, post_status,post_author) VALUES ('名称未設定のトピックス', NOW(), NOW(), 'draft',?)";
$stmt = $cms_access->prepare($insertQuery);

if ($stmt) {
    $stmt->bind_param("i",$_SESSION['group_id']);
    
    if ($stmt->execute()) {
        $topics_id = $cms_access->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];
        header("Location: " . home_url('/Topics/Edit?topics_id='.$topics_id));
    } else {
        die('データベース接続エラーが発生しました。管理者に連絡してください。');
    }

    $stmt->close();
} else {
    die('データベース接続エラーが発生しました。管理者に連絡してください。');
}
}

//データベース接続解除
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/disconnect-database.php');

?>