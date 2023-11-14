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
    $game_id = $_POST['game_id'];
    $score1 = $_POST['score1'];
    $score2 = $_POST['score2'];
    $winner_id = $_POST['winner_id'];
    if(!empty($_POST['tiebreak'])){
        $tiebrak = $_POST['tb_score'];
    }else{
        $tiebrak = null;
    }

    global $tournament_access;
    $table_name = h($_POST['tournament_id']).'_score_index';
    $sql = "INSERT INTO $table_name (game_id, score1,score2,tiebreak,winner_id) VALUES (? , ? , ?, ? ,?)";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('iiiii',$game_id, $score1,$score2,$tiebrak,$winner_id);
    $stmt->execute();
    $stmt->close();

    $idStrings = $_POST['id'];
    $status_array = explode("-", $idStrings);

    $venue_id = h($_POST['venue_id']);
    $court_num = intval($status_array[0]); 
    $current_status = intval($status_array[1]);

    if($current_status == 3){
        //ネクネクだから該当行のステータス変更だけでOK
        UpdateCourtStatus_2(0,3,$court_num,$venue_id);
    }elseif($current_status == 2){
        UpdateCourtStatus_2(0,2,$court_num,$venue_id);
        UpdateCourtStatus_2(2,3,$court_num,$venue_id);
    }elseif($current_status == 1){
        UpdateCourtStatus_2(0,1,$court_num,$venue_id);
        UpdateCourtStatus_2(1,2,$court_num,$venue_id);
        UpdateCourtStatus_2(2,3,$court_num,$venue_id);
    }
    

    //リダイレクト
    $_SESSION['OOP_Notice'] = '結果の登録が完了しました。';
    $url = 'Tournament/View/Operation/OOP?tournament_id='.h($_POST['tournament_id']).'&venue_id='.h($_POST['venue_id']);
    header("Location: " . home_url($url));
    exit;

}


function UpdateCourtStatus_2($new_status,$current_status,$court_num,$venue_id){
    global $tournament_access;
    $table_name = h($_POST['tournament_id']).'_game_index';
    $sql = "UPDATE $table_name SET status = ? WHERE status = ? AND court_num = ? AND venue_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('iiii',$new_status,$current_status,$court_num,$venue_id);
    
    if ($stmt->execute()) {
        $stmt->close();
    }else{
        $stmt->close();    
    }
}
