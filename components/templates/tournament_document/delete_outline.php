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
    $before_path = get_document_path_return(h($_POST['tournament_id']), 'outline');
    $document_id = $before_path['document_id']; // 削除するドキュメントのdocument_id
    unlink('../../../'.$before_path['document_path']);

    // 削除クエリを準備します
    $delete_query = "DELETE FROM document_path WHERE document_id = ?";
    $stmt = $cms_access->prepare($delete_query);
    
    if ($stmt) {
        // パラメーターをバインドします
        $stmt->bind_param("i", $document_id);
    
        // クエリを実行します
        if ($stmt->execute()) {
            // データ削除が成功した場合の処理
            //ログ追加
            add_log('delete_outline' , $tournament_id, null , $before_path['document_path'], null);
            //リダイレクト
            echo '<script>alert("大会要綱の削除が完了しました。");</script>';
            ?>
            <script> window.location.href = "<?= home_url('Tournament/View/Document?tournament_id=').$tournament_id ?>";</script>'; 
            <?php
        } else {
            // 削除が失敗した場合の処理
            echo '<script>alert("データの削除に失敗しました。");</script>';
        }
    } else {
        // ステートメントの準備が失敗した場合の処理
        echo '<script>alert("ステートメントの準備に失敗しました。");</script>';
    }
    

}


// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>