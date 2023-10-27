<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if(!raquty_nonce_check(2)){
    header("Location: " . home_url('Tournament/List'));
}else{
    $tournament_id = $_POST['tournament_id'];
    $child_event_id = $_POST['child_event_id'];
    $draw_id = $_POST['draw_num'];
    $auth = generateRandomString();
    $_SESSION['auth'] = $auth;

    $table_name = $tournament_id . '_drawlist';
    $sql = "DELETE FROM $table_name WHERE child_event_id = ? AND draw_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('ii', $child_event_id,$draw_id);
    
    if ($stmt->execute()) {
        header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=') . $tournament_id . '&child_event_id=' . $child_event_id . '&auth=' . $auth);
        $stmt->close();
    } else {
        $_SESSION['insert_data_draw'] = 'ドロー登録の削除に失敗しました。再度お試しください。';
        header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=') . $tournament_id . '&child_event_id=' . $child_event_id . '&auth=' . $auth);
        $stmt->close();
    }
    }

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');

