<?php
// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');


header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$tournament_id = isset($_GET['tournament_id']) ? $_GET['tournament_id'] : null;
// クエリパラメータに応じてデータを返す
$response = array('get_order_of_play' => get_order_of_play($tournament_id), 'get_result' => get_result($tournament_id));

// オリジンを設定してCORSを有効にする
// JSON形式でデータを出力
echo json_encode($response);
// データベース接続解除
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/disconnect-database.php');
function get_order_of_play($_tournament_id){
    $table_name = strval($_tournament_id)."_game_index";
    $query = "SELECT * FROM $table_name";
    global $tournament_access;
    $stmt = $tournament_access -> prepare($query);
    if($stmt ===  false){
        die("プリペアードステートメントの準備に失敗しました。");
    }
    if($stmt->execute() === false){
        die("クエリの実行に失敗しました。");
    }
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            //ここで選手１の情報を取得
            $table_name2 = strval($_tournament_id)."_entrylist";
            $query2 = "SELECT * FROM $table_name2 WHERE draw_id = ?";
            global $tournament_access;
            $stmt2 = $tournament_access -> prepare($query2);
            if($stmt2 ===  false){
                die("プリペアードステートメントの準備に失敗しました。");
            }
            $stmt2 -> bind_param('s', $row['draw_id_1']);
            if($stmt2->execute() === false){
                die("クエリの実行に失敗しました。");
            }
            $result2 = $stmt2->get_result();
            $row2 = $result2->fetch_assoc();
            //ここで選手２の情報を取得
            $table_name3 = strval($_tournament_id)."_entrylist";
            $query3 = "SELECT * FROM $table_name3 WHERE draw_id = ?";
            global $tournament_access;
            $stmt3 = $tournament_access -> prepare($query3);
            if($stmt3 ===  false){
                die("プリペアードステートメントの準備に失敗しました。");
            }
            $stmt3 -> bind_param('s', $row['draw_id_2']);
            if($stmt3->execute() === false){
                die("クエリの実行に失敗しました。");
            }
            $result3 = $stmt3->get_result();
            $row3 = $result3->fetch_assoc();
            //ここでイベントの種類を取得
            $table_name4 = "event_list";
            $query4 = "SELECT * FROM $table_name4 WHERE tournament_id = ? AND event_id = ?";
            global $cms_access;
            $stmt4 = $cms_access -> prepare($query4);
            if($stmt4 ===  false){
                die("プリペアードステートメントの準備に失敗しました。");
            }
            $stmt4 -> bind_param('ss', $_tournament_id, $row['event_id']);
            if($stmt4->execute() === false){
                die("クエリの実行に失敗しました。");
            }
            $result4 = $stmt4->get_result();
            $row4 = $result4->fetch_assoc();
            if($row4['type'] == 'シングルス'){
                $list = array(
                    //試合の基本情報
                    'game_id' => $row['game_id'],//試合ID
                    'venue_id' => $row['venue_id'],//会場ID
                    'court_num' => $row['court_num'],//コート番号
                    'round' => $row['round'],//ラウンド
                    'match_id'=> $row['match_id'],
                    'event_id' => $row['event_id'],//イベントID
                    'event_name' => $row4['event_name'],//イベント名
                    'type' => $row4['type'],//シングルス、ダブルス
                    'gender' => $row4['gender'],//男子、女子
                    //若番の全情報
                    'draw_id_1' => $row['draw_id_1'],//若番のドローID
                    'small_player1_id' => $row2['user1_id'],//選手１のID
                    'small_player1_name' => $row2['user1_name'],//選手１の名前
                    'small_player1_belonging' => $row2['user1_belonging'],//選手１の所属
                    //遅番の全情報
                    'draw_id_2' => $row['draw_id_2'],//遅番のドローID
                    'big_player1_id' => $row3['user1_id'],//選手２のID
                    'big_player1_name' => $row3['user1_name'],//選手２の名前
                    'big_player1_belonging' => $row3['user1_belonging'],//選手２の所属
                    'status' => $row['status'],//試合中、待ち番号、終了
                    'start_at' => $row['start_at']//試合開始時間
                );
                $response[]= $list;
            }else if($row4['type'] == 'ダブルス'){
                $list = array(
                    //試合の基本情報
                    'game_id' => $row['game_id'],//試合ID
                    'venue_id' => $row['venue_id'],//会場ID
                    'court_num' => $row['court_num'],//コート番号
                    'round' => $row['round'],//ラウンド
                    'match_id'=> $row['match_id'],
                    'event_id' => $row['event_id'],//イベントID
                    'event_name' => $row4['event_name'],//イベント名
                    'type' => $row4['type'],//シングルス、ダブルス
                    'gender' => $row4['gender'],//男子、女子
                    //若番の全情報
                    'draw_id_1' => $row['draw_id_1'],//若番のドローID
                    'small_player1_id' => $row2['user1_id'],//選手１のID
                    'small_player1_name' => $row2['user1_name'],//選手１の名前
                    'small_player1_belonging' => $row2['user1_belonging'],//選手１の所属
                    'small_player2_id' => $row2['user2_id'],//選手２のID
                    'small_player2_name' => $row2['user2_name'],//選手２の名前
                    'small_player2_belonging' => $row2['user2_belonging'],//選手２の所属
                    //遅番の全情報
                    'draw_id_2' => $row['draw_id_2'],//遅番のドローID
                    'big_player1_id' => $row3['user1_id'],//選手１のID
                    'big_player1_name' => $row3['user1_name'],//選手１の名前
                    'big_player1_belonging' => $row3['user1_belonging'],//選手１の所属
                    'big_player2_id' => $row3['user2_id'],//選手２のID
                    'big_player2_name' => $row3['user2_name'],//選手２の名前
                    'big_player2_belonging' => $row3['user2_belonging'],//選手２の所属
                    //試合の状況
                    'status' => $row['status'],//試合中、待ち番号、終了
                    'start_at' => $row['start_at']//試合開始時間
                );
                $response[]= $list;
            }else{
                $response = null;
            }
        }
    }else{
        $response = null;
    }
    return $response;
}
function get_result($_tournament_id){
    $table_name = $_tournament_id."_result";
    $query = "SELECT * FROM $table_name";
    global $tournament_access;
    $stmt = $tournament_access -> prepare($query);
    if($stmt ===  false){
        die("プリペアードステートメントの準備に失敗しました。");
    }
    if($stmt->execute() === false){
        die("クエリの実行に失敗しました。");
    }
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $table_name2 = $_tournament_id."_game_index";
            $query2 = "SELECT * FROM $table_name2 WHERE game_id = ?";
            global $tournament_access;
            $stmt2 = $tournament_access -> prepare($query2);
            if($stmt2 ===  false){
                die("プリペアードステートメントの準備に失敗しました。");
            }
            $stmt2 -> bind_param('s', $row['game_id']);
            if($stmt2->execute() === false){
                die("クエリの実行に失敗しました。");
            }
            $result2 = $stmt2->get_result();
            if($result2->num_rows > 0){
                while($row2 = $result2->fetch_assoc()){
                    if($row2['round']!='コンソレ'&&$row2['round']!='コンソレーション'){
                        $list = array(
                            'id' => $row['id'],
                            'game_id' => $row['game_id'],
                            'event_id' => $row2['event_id'],
                            'score1' => $row['score1'],
                            'score2' => $row['score2'],
                            'draw_id_1' => $row2['draw_id_1'],
                            'draw_id_2' => $row2['draw_id_2'],
                            'tiebreak' => $row['tiebreak'],
                            'winner_id' => $row['winner_id'],
                            'timestamp' => $row['timestamp']
                        );
                    }
                    
                }
            }
            $response[]= $list;
        }
    }else{
        $response = null;
    }
    return $response;
}

?>