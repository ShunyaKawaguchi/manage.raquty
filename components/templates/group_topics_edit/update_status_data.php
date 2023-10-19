<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if ($_SESSION['nonce_id'] == $_POST['raquty_nonce']) {
    if (isset($_POST['topics_id'])) {
        $topics_id = h($_POST['topics_id']);
        $post_title = h($_POST['post_title']);
        $post_content = h($_POST['post_content']);
        $post_status = h($_POST['Publishing_Settings']);
        $post_date = h($_POST['post_date']);

        // パラメーターをバインド
        $sql = 'UPDATE group_topics SET post_title = ?, post_content = ?, post_status = ?,post_date = ?,post_modified = NOW() WHERE post_id = ?';
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('ssssi', $post_title, $post_content, $post_status, $post_date, $topics_id);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // UPDATEが成功した場合の処理
            echo "<script>
                    window.onload = function() {
                        const message = '公開の状況更新が完了しました。';
                        alert(message);
                        const newLocation = '" . home_url('Topics/Edit?topics_id=') .  $topics_id . "';
                        window.location.href = newLocation;
                    }
                </script>";
            } else {
            // エラーが発生した場合の処理
            error_log("UPDATE query failed: " . $stmt->error);
        }

        $stmt->close();
    } else {
        // エラーが発生した場合の処理
    }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>