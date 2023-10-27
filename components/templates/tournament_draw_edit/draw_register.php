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
    $event_id = $_POST['event_id'];
    $child_event_id = $_POST['child_event_id'];
    $entry_id = $_POST['entry_id'];
    $draw_id = $_POST['draw_num'];
    $auth = generateRandomString();
    $_SESSION['auth'] = $auth;

    if(isEntryAlreadyChecked($tournament_id, $draw_id, $child_event_id)){
        $_SESSION['insert_data_draw'] = 'ドロー番号:'.$draw_id.'には既に他の選手が登録されています。';
        header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=').$tournament_id.'&child_event_id='.$child_event_id.'&auth='.$auth);
        exit;
    }else{
        $table_name = $tournament_id . '_drawlist';
        $sql = "INSERT INTO $table_name (entry_id, 	event_id, child_event_id, draw_id) VALUES (? , ? , ?, ?)";
        $stmt = $tournament_access->prepare($sql);
        $stmt->bind_param('iiii', $entry_id, $event_id, $child_event_id,$draw_id);

        if ($stmt->execute()) {
            header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=').$tournament_id.'&child_event_id='.$child_event_id.'&auth='.$auth);
            $stmt->close();
        }else{
            $_SESSION['insert_data_draw'] = 'ドローの登録に失敗しました。再度お試しください。';
            header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=').$tournament_id.'&child_event_id='.$child_event_id.'&auth='.$auth);
            $stmt->close();
        }

    }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');

function isEntryAlreadyChecked($tournament_id, $draw_id, $child_event_id) {
    global $tournament_access;

    $table_name = $tournament_id . '_drawlist';

    $sql = "SELECT * FROM $table_name WHERE draw_id = ? AND child_event_id = ?";
    $stmt = $tournament_access->prepare($sql);

    if (!$stmt) {
        return true;
    }

    $stmt->bind_param("ii", $draw_id, $child_event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}
