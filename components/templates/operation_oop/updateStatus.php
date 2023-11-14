<?php 
session_start();

require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/have_user_id.php');


if (!isset($_POST['tournament_id'])) {
    header("Location: " . home_url('Tournament/List'));
    require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
    exit;
}elseif(!isset($_POST['venue_id'])) {
    header("Location: " . home_url('Tournament/View/Operation?tournamet_id=').h($_POST['tournament_id']));
    require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
    exit;
}
$url = 'Tournament/View/Operation/OOP?tournament_id='.h($_POST['tournament_id']).'&venue_id='.h($_POST['venue_id']);

if(!raquty_nonce_check()){
    $_SESSION['OOP_Notice'] = '不正な遷移を検知しました。操作をやり直してください。';
    header("Location: " . home_url($url));
    require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
    exit;
}else{
    $idStrings = $_POST['id'];
    $status_array = explode("-", $idStrings);

    $venue_id = h($_POST['venue_id']);
    $court_num = intval($status_array[0]); 
    $current_status = intval($status_array[1]);
    $new_status = $current_status - 1;

    $request_gameID = search_statusChange_gameID($current_status,$venue_id,$court_num);
    $accept_gameID = search_statusChange_gameID($new_status,$venue_id,$court_num);
    update_status_today($new_status,$request_gameID);
    update_status_today($current_status,$accept_gameID);

    //リダイレクト
    $url = 'Tournament/View/Operation/OOP?tournament_id='.h($_POST['tournament_id']).'&venue_id='.h($_POST['venue_id']);
    header("Location: " . home_url($url));
    exit;

    
}

function update_status_today($new_status,$game_id){
    global $tournament_access;
    $table_name = h($_POST['tournament_id']).'_game_index';
    $sql = "UPDATE $table_name SET status = ? WHERE game_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('ii',$new_status,$game_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    }else{
        $stmt->close();
        $_SESSION['OOP_Notice'] = '試合順番の入れ替えに失敗しました。再度お試しください。';
        $url = 'Tournament/View/Operation/OOP?tournament_id='.h($_POST['tournament_id']).'&venue_id='.h($_POST['venue_id']);
        header("Location: " . home_url($url));
        exit;
    }
}

function search_statusChange_gameID($status, $venue_id,$court_num){
    global $tournament_access;
    $table_name = h($_POST['tournament_id']).'_game_index';
    $sql = "SELECT game_id FROM $table_name WHERE status = ? AND venue_id = ? AND court_num = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("iii",$status, $venue_id,$court_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['OOP_Notice'] = '試合順番の入れ替えに失敗しました。再度お試しください。';
        $url = 'Tournament/View/Operation/OOP?tournament_id='.h($_POST['tournament_id']).'&venue_id='.h($_POST['venue_id']);
        header("Location: " . home_url($url));
        exit;
    } else {
        $row = $result->fetch_assoc() ;
        return $row['game_id'];

    }
}

