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
        $tournament_id = h($_POST['tournament_id']);
        $topics_id = h($_POST['topics_id']);
        $post_title = h($_POST['post_title']);
        $post_content = h($_POST['post_content']);
        $post_date = h($_POST['post_date']);
        $get_log_before = check_tournament_topics_existance($topics_id );
        $log_before = 'topics_id:'.$topics_id.'/title:'.$get_log_before['post_title'].'/contents:'.$get_log_before['post_content'];
        $log_after = 'topics_id:'.$topics_id.'/title:'.$post_title.'/contents:'.$post_content;


        // パラメーターをバインド
        $sql = 'UPDATE tournament_topics SET post_title = ?, post_content = ?,post_date = ?,post_modified = NOW() WHERE post_id = ?';
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('sssi', $post_title, $post_content, $post_date, $topics_id);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // UPDATEが成功した場合の処理
            //ログ追加
            add_log('update_tournament_topics_content' , $tournament_id, null , $log_before, $log_after);

            //リダイレクト
            echo "<script>
                    window.onload = function() {
                        const message = 'コンテンツの更新が完了しました。';
                        alert(message);
                        const newLocation = '" . home_url('Tournament/View/Topics/Edit?tournament_id=') . $tournament_id . '&topics_id=' . $topics_id . "';
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