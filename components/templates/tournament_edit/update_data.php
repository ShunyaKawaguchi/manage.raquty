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
        $tournament_id = h($_POST['tournament_id']);
        $post_status = h($_POST['after_status']);
        $tournament_data = check_tournament_existance( $tournament_id );
        $log_before = $tournament_data['post_status'];

        // パラメーターをバインド
        $sql = 'UPDATE tournament SET post_status = ? WHERE tournament_id = ?';
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('si', $post_status , $tournament_id);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // UPDATEが成功した場合の処理
            //ログ追加
            add_log('change_tournament_status' , $tournament_id, null , $log_before, $post_status);
            //リダイレクト
            $_SESSION['change_tournament_status'] = '大会の公開状況を更新しました。';
            header("Location: " . home_url('Tournament/View?tournament_id=').$tournament_id);   
            exit;

        } else {
            // エラーが発生した場合の処理
            error_log("UPDATE query failed: " . $stmt->error);
        }

        $stmt->close();
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>