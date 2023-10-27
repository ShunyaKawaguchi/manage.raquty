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
    $status = $_POST['status'];
    $child_event_id = $_POST['child_event_id'];
    $auth = generateRandomString();
    $_SESSION['auth'] = $auth;

    if(!raquty_password_authentication()){
        $_SESSION['change_status'] = 'パスワード認証に失敗しました。再度お試しください。';
        header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=').$tournament_id.'&child_event_id='.$child_event_id.'&auth='.$auth);
        exit;
    }else{
        if($status == 0 || $status == 1){
            update_child_event_status($status,$child_event_id);
            $_SESSION['change_status'] = 'ドローの公開状況を更新しました。';
            header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=').$tournament_id.'&child_event_id='.$child_event_id.'&auth='.$auth);
            exit;
        }elseif($status == 9999){
            delete_child_event($child_event_id);
            $_SESSION['delete_draw'] = 'ドローを削除しました。';
            header("Location: " . home_url('Tournament/View/Draw/?tournament_id=').$tournament_id);
            exit;

        }
    }
}

function update_child_event_status($status,$child_event_id){
    global $cms_access;
    $sql = "UPDATE child_event_list SET status = ? WHERE id = ?";
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param('ii',$status,$child_event_id);
    if ($stmt->execute()) {
        $data = check_child_event_existance($_POST['tournament_id'], $child_event_id);

        $log_before = 'child_event_id:'.$child_event_id.'/status:'.$data['status'];
        $log_after = 'child_event_id:'.$child_event_id.'/status:'.$status;
        add_log('change_status(child_event)' , $_POST['tournament_id'], $_POST['event_id'] , $log_before, $log_after);
    }
    $stmt->close();
}

function delete_child_event($child_event_id){
global $cms_access;
// 削除のためにUPDATE文をDELETE文に変更し、WHERE条件を使って特定の行を削除する
$sql = "DELETE FROM child_event_list WHERE id = ?";
$stmt = $cms_access->prepare($sql);
$stmt->bind_param('i', $child_event_id);
if ($stmt->execute()) {
    $log_before = 'child_event_id:'.$child_event_id;
    add_log('delete_child_event' , $_POST['tournament_id'], $_POST['event_id'] , $log_before, null);
    delete_child_event_drawlist($child_event_id);
}
$stmt->close();
}

function delete_child_event_drawlist($child_event_id){
    $table_name = $_POST['tournament_id'].'_drawlist';
    global $tournament_access;
    // 削除のためにUPDATE文をDELETE文に変更し、WHERE条件を使って特定の行を削除する
    $sql = "DELETE FROM $table_name WHERE child_event_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('i', $child_event_id);
    if ($stmt->execute()) {
        $log_before = 'child_event_id:'.$child_event_id;
        add_log('delete_child_event_drawlist' , $_POST['tournament_id'], $_POST['event_id'] , $log_before, 'All_Data_Deleted');
    }
    $stmt->close();
    }
