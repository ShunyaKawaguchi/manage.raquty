<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if ($_SESSION['nonce_id'] == $_POST['raquty_nonce']  && $_SESSION['level'] === 1) {
   // 認証エラーメッセージを初期化
   unset($_SESSION['delete_group_topics_pw_error']);
   // パスワード認証
   if (raquty_password_authentication()) {
        delete_topics();
   } else {
       // 認証エラーエッセー時を登録
       $_SESSION['delete_group_topics_pw_error'] = 1;
       //リダイレクト
       echo "<script>
           document.addEventListener('DOMContentLoaded', function() {
           const templateId = " . $_POST['topics_id'] . ";
           const form = document.createElement('form');
           form.method = 'post';
           form.action = '" . home_url('Topics/Edit?topics_id=' . $_POST['topics_id']) . "';
               
           document.body.appendChild(form);
           form.submit();
       });
   </script>";
   
   }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');

function delete_topics(){
    if (isset($_POST['topics_id'])) {
        $topics_id = h($_POST['topics_id']);

        // パラメーターをバインド
        $sql = 'DELETE FROM group_topics WHERE post_id = ?';
        global $cms_access;
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('i', $topics_id);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // 削除が成功した場合の処理
            echo "<script>
                    window.onload = function() {
                        const message = 'トピックスの削除が完了しました。';
                        alert(message);
                        const newLocation = '" . home_url('Topics') . "';
                        window.location.href = newLocation;
                    }
                </script>";
        } else {
            // エラーが発生した場合の処理
            error_log("DELETE query failed: " . $stmt->error);
        }

        $stmt->close();

    } else {
        // エラーが発生した場合の処理
    }
}