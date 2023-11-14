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
    $game_type = $_POST['type'];

    if(insert_gamecard_data()){
        $_SESSION['OOP_Notice'] = '試合カードの追加に成功しました。';
        
    }else{
        $_SESSION['OOP_Notice'] = '試合カードの追加に失敗しました。再度お試しください。';
    }
    header("Location: " . home_url($url));
    require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
    exit;
}

function insert_gamecard_data(){
    $child_event_id = -1;
    $venue_id = h($_POST['venue_id']);
    $category = h($_POST['type']);
    $player_data_array_1 = explode("-", $_POST['player_1']);
    $player_data_array_2 = explode("-", $_POST['player_2']);
    $player_data_array_3 = explode("-", $_POST['player_3']);
    $player_data_array_4 = explode("-", $_POST['player_4']);
    $player_event_1 = intval($player_data_array_1[0]); 
    $draw_id_1 = intval($player_data_array_1[1]);
    $entry_id_1 = intval($player_data_array_1[2]);
    $player_event_2 = intval($player_data_array_2[0]); 
    $draw_id_2 = intval($player_data_array_2[1]);
    $entry_id_2 = intval($player_data_array_2[2]);
    $player_event_3 = intval($player_data_array_3[0]); 
    $draw_id_3 = intval($player_data_array_3[1]);
    $entry_id_3 = intval($player_data_array_3[2]);
    $player_event_4 = intval($player_data_array_4[0]); 
    $draw_id_4 = intval($player_data_array_4[1]);
    $entry_id_4 = intval($player_data_array_4[2]);

    $player_data_1 = get_user_info_today($entry_id_1);
    if($player_data_1['user1_id']==$draw_id_1){
        $player_1_name = $player_data_1['user1_name'];
        $player_1_belonging = $player_data_1['user1_belonging'];
    }else{
        $player_1_name = $player_data_1['user2_name'];
        $player_1_belonging = $player_data_1['user2_belonging'];
    }
    $player_data_2 = get_user_info_today($entry_id_2);
    if($player_data_2['user1_id']==$draw_id_2){
        $player_2_name = $player_data_2['user1_name'];
        $player_2_belonging = $player_data_2['user1_belonging'];
    }else{
        $player_2_name = $player_data_2['user2_name'];
        $player_2_belonging = $player_data_2['user2_belonging'];
    }
    $player_data_3 = get_user_info_today($entry_id_3);
    if($player_data_3['user1_id']==$draw_id_3){
        $player_3_name = $player_data_3['user1_name'];
        $player_3_belonging = $player_data_3['user1_belonging'];
    }else{
        $player_3_name = $player_data_3['user2_name'];
        $player_3_belonging = $player_data_3['user2_belonging'];
    }
    $player_data_4 = get_user_info_today($entry_id_4);
    if($player_data_4['user1_id']==$draw_id_4){
        $player_4_name = $player_data_4['user1_name'];
        $player_4_belonging = $player_data_4['user1_belonging'];
    }else{
        $player_4_name = $player_data_4['user2_name'];
        $player_4_belonging = $player_data_4['user2_belonging'];
    }

    // // 重複をチェックする
    // if ($draw_id_1 == $draw_id_2 || $draw_id_1 == $draw_id_3 || $draw_id_1 == $draw_id_4 ||
    // $draw_id_2 == $draw_id_3 || $draw_id_2 == $draw_id_4 ||
    // $draw_id_3 == $draw_id_4) {
    // $result = false;
    // } else {
    // $result = true;
    // }

    // if(!$result){
    //     $_SESSION['OOP_Notice'] = '選手の重複があるため、試合カードを追加できませんでした。';
    //     $url = 'Tournament/View/Operation/OOP?tournament_id='.h($_POST['tournament_id']).'&venue_id='.h($_POST['venue_id']);
    //     header("Location: " . home_url($url));
    //     exit;
    // }

    $entry_id = category_loder($category,$draw_id_1,$draw_id_2,$draw_id_3,$draw_id_4,$player_1_name,$player_2_name,$player_3_name,$player_4_name,$player_1_belonging,$player_2_belonging,$player_3_belonging,$player_4_belonging);
    $draw1_id = $entry_id;
    $draw2_id = $entry_id + 1;
    

    $status = -1;
    global $tournament_access;
    $table_name = h($_POST['tournament_id']).'_game_index';
    $sql = "INSERT INTO $table_name (child_event_id, venue_id,category,draw_id_1,draw_id_2,	status) VALUES (? , ? , ?, ?, ? ,?)";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('iiiiii',$child_event_id, $venue_id,$category,$draw1_id,$draw2_id,$status);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    }else{
        $stmt->close();
        return false;
    }


}

function category_loder($category,$draw_id_1,$draw_id_2,$draw_id_3,$draw_id_4,$player_1_name,$player_2_name,$player_3_name,$player_4_name,$player_1_belonging,$player_2_belonging,$player_3_belonging,$player_4_belonging){
    global $tournament_access;
    if($category == 1){
        $draw_id = 1;
        $user1 = $draw_id_1;
        $event_id = -1;

        for ($ii = 0; $ii < 2; $ii++) {
            $table_name = h($_POST['tournament_id']) . '_entrylist';
            $sql = "INSERT INTO $table_name (draw_id, user1_id, user1_name, user1_belonging, event_id) VALUES (?, ?, ? ,?, ?)";
            $stmt = $tournament_access->prepare($sql);
            $stmt->bind_param('iissi', $draw_id, $user1, $player_1_name, $player_1_belonging, $event_id);
        
            if ($ii == 0) {
                $user1 = $draw_id_1;
            } else {
                // 2週目のデータを設定する
                $user1 = $draw_id_3; // 新しいデータの取得方法に合わせて設定
                $player_1_name = $player_3_name; // 新しいデータの取得方法に合わせて設定
                $player_1_belonging = $player_3_belonging; // 新しいデータの取得方法に合わせて設定
            }
        
            $stmt->execute();
            $stmt->close();
        }
        
         //挿入した2個目のentry_idを取得して、1をマイナスして返す
         $entry_id = mysqli_insert_id($tournament_access);
         $first_entry_id = $entry_id - 1;
 
         return $first_entry_id;

    }elseif($category == 2){
        $draw_id = 1;
        $user1 = $draw_id_1;
        $user2 = $draw_id_2;
        $event_id = -1;

        for($i = 0; $i < 2; $i++){
            $table_name = h($_POST['tournament_id']) . '_entrylist';
            $sql = "INSERT INTO $table_name (draw_id, user1_id, user1_name, user1_belonging, user2_id, user2_name, user2_belonging, event_id) VALUES (?, ?, ? ,?, ? ,?, ? ,?)";
            $stmt = $tournament_access->prepare($sql);
            $stmt->bind_param('iississi', $draw_id, $user1, $player_1_name, $player_1_belonging, $user2, $player_2_name, $player_2_belonging, $event_id);
            
            if ($i == 0) {
                $user1 = $draw_id_1;
                $user2 = $draw_id_2;
            } else {
                // 2週目のデータを設定する
                $user1 = $draw_id_3; 
                $user2 = $draw_id_4;
                $player_1_name = $player_3_name; 
                $player_1_belonging = $player_3_belonging;
                $player_2_name = $player_4_name; 
                $player_2_belonging = $player_4_belonging;
            }
        
            $stmt->execute();
            $stmt->close();
        }
        //挿入した2個目のentry_idを取得して、1をマイナスして返す
        $entry_id = mysqli_insert_id($tournament_access);
        $first_entry_id = $entry_id - 1;

        return $first_entry_id;
    }
}

function get_user_info_today($entry_id){
    global $tournament_access;
    $table_name = h($_POST['tournament_id']).'_entrylist';
    $sql = "SELECT * FROM $table_name WHERE id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("i",$entry_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false;
    } else {
        $row = $result->fetch_assoc() ;
        return $row;
    }
}