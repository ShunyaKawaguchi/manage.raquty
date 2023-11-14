<?php 
function get_consolation_player_name($_draw_id){
    //コンソレーションの試合の選手名を取得する
    global $tournament_access;
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id.'_entrylist';
    $sql = "SELECT * FROM $table_name WHERE id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('i', $_draw_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    //resultは１行しかないはず
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $list = array(
            'user1_name' => $row['user1_name'],
            'user1_belonging' => $row['user1_belonging'],
            'user2_name' => $row['user2_name'],
            'user2_belonging' => $row['user2_belonging']
        );
    }else{
        $list = null;
    }
    return $list;
}
function get_player_name($_child_event_id, $_entry_id){
    global $tournament_access;
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id.'_drawlist';
    $sql = "SELECT * FROM $table_name WHERE child_event_id = ? AND draw_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('ii', $_child_event_id, $_entry_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    //resultは１行しかないはず
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $entry_id = $row['entry_id'];
    }else{
        return null;
    }
    $table_name2 = $tournament_id.'_entrylist';
    $sql2 = "SELECT * FROM $table_name2 WHERE id = ?";
    $stmt2 = $tournament_access->prepare($sql2);
    $stmt2->bind_param('i', $entry_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $stmt2->close();
    //result2は１行しかないはず
    if ($result2->num_rows === 1) {
        $row2 = $result2->fetch_assoc();
        $list = array(
            'user1_name' => $row2['user1_name'],
            'user1_belonging' => $row2['user1_belonging'],
            'user2_name' => $row2['user2_name'],
            'user2_belonging' => $row2['user2_belonging']
        );
    }else{
        $list = null;
    }
    return $list;
}
function get_round_name($_child_event_id, $_round){
    //決勝、準決勝、準々決勝だけは、文字列で返す
    //それ以外は、数字＋回戦という文字列で返す
    $child_event = get_child_event($_child_event_id);
    $scale = $child_event['capacity'];
    $number_of_round = log($scale, 2);
    if($_round == $number_of_round){
        return '決勝';
    }else if($_round == $number_of_round-1){
        return '準決勝';
    }else if($_round == $number_of_round-2){
        return '準々決勝';
    }else{
        return $_round.'回戦';
    }
}
function get_consolation(){
    //コンソレーションの試合をデータベースから取得する
    global $tournament_access;
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id.'_game_index';
    //child_event_idが-1かつstatusが-1の試合を取得する
    $sql = "SELECT * FROM $table_name WHERE child_event_id = ? AND status = ?";
    $child_event_id = -1;
    $status = -1;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("ii", $child_event_id, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        $game_list = array();
        while ($row = $result->fetch_assoc()) {
            $game_list[] = $row;
        }
        return $game_list;
    } else {
        return null;
    }
}
function create_suggest($_child_event_id){
    //game_indexから、child_event_idが_child_event_idでstatusが-1の試合を取得する
    global  $tournament_access;
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id.'_game_index';
    $sql = "SELECT * FROM $table_name WHERE child_event_id = ? AND status = ?";
    $status = -1;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("ii", $_child_event_id, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        $game_list = array();
        while ($row = $result->fetch_assoc()) {
            $game_list[] = $row;
        }
        return $game_list;
    } else {
        return null;
    }
}

function make_base($_child_event_id){
    $capacity = get_child_event($_child_event_id)['capacity'];
    $event_type = get_child_event($_child_event_id)['event_type'];
    if($event_type == 1){
        //トーナメント進行だと判断
        $number_of_round = log($capacity, 2);
        $number_of_game_in_round = $capacity/2;
        for ($r = 1; $r <= $number_of_round; $r++) { 
            for($g = 1; $g <= $number_of_game_in_round; $g++){
                global $tournament_access;
                $tournament_id = h($_GET['tournament_id']);
                $table_name = $tournament_id.'_game_index';
                $sql = "SELECT * FROM $table_name WHERE child_event_id = ? AND round = ? AND match_id = ?";
                $stmt = $tournament_access->prepare($sql);
                $stmt->bind_param('iii', $_child_event_id, $r, $g);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                
                if ($result->num_rows > 0) {
                    //存在するならないもしない
                } else {
                    //もし試合が存在しなかったら、試合を作成する
                    //もし１回戦なら
                    if($r == 1){
                        //奇番の選手情報データベースから取得する
                        $table_name2 = $tournament_id.'_drawlist';
                        $sql2 = "SELECT * FROM $table_name2 WHERE child_event_id = ? AND draw_id = ?";
                        $stmt2 = $tournament_access->prepare($sql2);
                        $draw_id = $g*2-1;
                        $stmt2->bind_param('ii', $_child_event_id, $draw_id);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $stmt2->close();
                        //result2は１行しかないはず
                        if ($result2->num_rows === 1) {
                            $row2 = $result2->fetch_assoc();
                            $player1 = $row2['draw_id'];
                        }else{
                            $player1 = null;
                        }
                        //偶番の選手情報データベースから取得する
                        $table_name3 = $tournament_id.'_drawlist';
                        $sql3 = "SELECT * FROM $table_name3 WHERE child_event_id = ? AND draw_id = ?";
                        $stmt3 = $tournament_access->prepare($sql3);
                        $draw_id = $g*2;
                        $stmt3->bind_param('ii', $_child_event_id, $draw_id);
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        $stmt3->close();
                        //result3は１行しかないはず
                        if ($result3->num_rows === 1) {
                            $row3 = $result3->fetch_assoc();
                            $player2 = $row3['draw_id'];
                        }else{
                            $player2 = null;
                        }
                        //試合情報をデータベースに登録する
                        //もしplayer1とplayer2が両方ともnullでないなら
                        if($player1 != null && $player2 != null){
                            $sql4 = "INSERT INTO $table_name (child_event_id, round, match_id, draw_id_1, draw_id_2, status) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt4 = $tournament_access->prepare($sql4);
                            $status = -1;
                            $stmt4->bind_param('iiiiii', $_child_event_id, $r, $g, $player1, $player2, $status);
                            $stmt4->execute();
                            $stmt4->close();
                        }else{
                            //もしplayer1がnullでplayer2がnullでないなら
                            if($player1 == null && $player2 != null){
                                $sql4 = "INSERT INTO $table_name (child_event_id, round, match_id, draw_id_1, draw_id_2, status) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt4 = $tournament_access->prepare($sql4);
                                $player1 = 0;
                                $status = 0;
                                $stmt4->bind_param('iiiiii', $_child_event_id, $r, $g, $player1, $player2, $status);
                                $stmt4->execute();
                                $stmt4->close();
                                //今登録した試合のgame_idを取得する
                                $sql5 = "SELECT * FROM $table_name WHERE child_event_id = ? AND round = ? AND match_id = ?";
                                $stmt5 = $tournament_access->prepare($sql5);
                                $stmt5->bind_param('iii', $_child_event_id, $r, $g);
                                $stmt5->execute();
                                $result5 = $stmt5->get_result();
                                $stmt5->close();
                                if ($result5->num_rows === 1) {
                                    $row5 = $result5->fetch_assoc();
                                    $game_id = $row5['game_id'];
                                    //score_indexテーブルにもplayer2の勝利として登録する
                                    $table_name6 = $tournament_id.'_score_index';
                                    $sql6 = "INSERT INTO $table_name6 (game_id, score1, score2, tiebreak, winner_id) VALUES (?, ?, ?, ?, ?)";
                                    $stmt6 = $tournament_access->prepare($sql6);
                                    $score1 = 0;
                                    $score2 = 0;
                                    $tiebreak = 0;
                                    $stmt6->bind_param('iiiii', $game_id, $score1, $score2, $tiebreak, $player2);
                                    $stmt6->execute();
                                    $stmt6->close();
                                }else{
                                    //エラー
                                }
                            }else if($player1 != null && $player2 == null){
                                //もしplayer1がnullでないでplayer2がnullなら
                                $sql4 = "INSERT INTO $table_name (child_event_id, round, match_id, draw_id_1, draw_id_2, status) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt4 = $tournament_access->prepare($sql4);
                                $player2 = 0;
                                $status = 0;
                                $stmt4->bind_param('iiiiii', $_child_event_id, $r, $g, $player1, $player2, $status);
                                $stmt4->execute();
                                $stmt4->close();
                                //今登録した試合のgame_idを取得する
                                $sql5 = "SELECT * FROM $table_name WHERE child_event_id = ? AND round = ? AND match_id = ?";
                                $stmt5 = $tournament_access->prepare($sql5);
                                $stmt5->bind_param('iii', $_child_event_id, $r, $g);
                                $stmt5->execute();
                                $result5 = $stmt5->get_result();
                                $stmt5->close();
                                if ($result5->num_rows === 1) {
                                    $row5 = $result5->fetch_assoc();
                                    $game_id = $row5['game_id'];
                                    //score_indexテーブルにもplayer1の勝利として登録する
                                    $table_name6 = $tournament_id.'_score_index';
                                    $sql6 = "INSERT INTO $table_name6 (game_id, score1, score2, tiebreak, winner_id) VALUES (?, ?, ?, ?, ?)";
                                    $stmt6 = $tournament_access->prepare($sql6);
                                    $score1 = 0;
                                    $score2 = 0;
                                    $tiebreak = 0;
                                    $stmt6->bind_param('iiiii', $game_id, $score1, $score2, $tiebreak, $player1);
                                    $stmt6->execute();
                                    $stmt6->close();
                                }else{
                                    //エラー
                                }
                            }
                        }
                    }else{
                        //もし２回戦以降なら
                        //前回戦の試合情報がgame_indexに登録されているか確認する
                        //奇番の試合情報データベースから取得する
                        $table_name2 = $tournament_id.'_game_index';
                        $sql2 = "SELECT * FROM $table_name2 WHERE child_event_id = ? AND round = ? AND match_id = ?";
                        $stmt2 = $tournament_access->prepare($sql2);
                        $round = $r-1;
                        $match_id = $g*2-1;
                        $stmt2->bind_param('iii', $_child_event_id, $round, $match_id);//前回戦の試合情報
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $stmt2->close();
                        //result2は１行しかないはず
                        if ($result2->num_rows === 1) {
                            $row2 = $result2->fetch_assoc();
                            $status1 = $row2['status'];
                        }else{
                            $status1 = null;
                        }
                        //前回戦の試合情報がgame_indexに登録されているか確認する
                        //偶番の試合情報データベースから取得する
                        $table_name3 = $tournament_id.'_game_index';
                        $sql3 = "SELECT * FROM $table_name3 WHERE child_event_id = ? AND round = ? AND match_id = ?";
                        $stmt3 = $tournament_access->prepare($sql3);
                        $round = $r-1;
                        $match_id = $g*2;
                        $stmt3->bind_param('iii', $_child_event_id, $round, $match_id);//前回戦の試合情報
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        $stmt3->close();
                        //result3は１行しかないはず
                        if ($result3->num_rows === 1) {
                            $row3 = $result3->fetch_assoc();
                            $status2 = $row3['status'];
                        }else{
                            $status2 = null;
                        }
                        //もしstatus1,status2がともに0なら、score_indexから奇番の勝者と偶番の勝者を取得する
                        if($status1 == 0 && $status2 == 0){
                            //奇番の勝者を取得する
                            $table_name4 = $tournament_id.'_score_index';
                            $sql4 = "SELECT * FROM $table_name4 WHERE game_id = ?";
                            $stmt4 = $tournament_access->prepare($sql4);
                            $stmt4->bind_param('i', $row2['game_id']);
                            $stmt4->execute();
                            $result4 = $stmt4->get_result();
                            $stmt4->close();
                            //result4は１行しかないはず
                            if ($result4->num_rows === 1) {
                                $row4 = $result4->fetch_assoc();
                                $winner1 = $row4['winner_id'];
                            }else{
                                $winner1 = null;
                            }
                            //偶番の勝者を取得する
                            $table_name5 = $tournament_id.'_score_index';
                            $sql5 = "SELECT * FROM $table_name5 WHERE game_id = ?";
                            $stmt5 = $tournament_access->prepare($sql5);
                            $stmt5->bind_param('i', $row3['game_id']);
                            $stmt5->execute();
                            $result5 = $stmt5->get_result();
                            $stmt5->close();
                            //result5は１行しかないはず
                            if ($result5->num_rows === 1) {
                                $row5 = $result5->fetch_assoc();
                                $winner2 = $row5['winner_id'];
                            }else{
                                $winner2 = null;
                            }
                            //試合情報をデータベースに登録する
                            //もしwinner1とwinner2が両方ともnullでないなら
                            if($winner1 != null && $winner2 != null){
                                $sql6 = "INSERT INTO $table_name (child_event_id, round, match_id, draw_id_1, draw_id_2, status) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt6 = $tournament_access->prepare($sql6);
                                $status = -1;
                                $stmt6->bind_param('iiiiii', $_child_event_id, $r, $g, $winner1, $winner2, $status);
                                $stmt6->execute();
                                $stmt6->close();
                            }else{
                                //少なくとも一方がnullなら
                                //何もしない
                            }
                        }

                    }
                    
                }
            }
            $number_of_game_in_round = $number_of_game_in_round/2;
        }
    }elseif($event_type == 2){
        //総当たり戦だと判断
        //まずは、drawlistから、child_event_idが$_child_event_idであるものをすべて取得する
        global $tournament_access;
        $tournament_id = h($_GET['tournament_id']);
        $table_name = $tournament_id.'_drawlist';
        $sql = "SELECT * FROM $table_name WHERE child_event_id = ?";
        $stmt = $tournament_access->prepare($sql);
        $stmt->bind_param('i', $_child_event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $draw_list = array();
            while ($row = $result->fetch_assoc()) {
                $draw_list[] = $row;
            }
        } else {
            $draw_list = null;
        }
        //drawlistから取得したものを元に、試合を作成する
        //capacityが総当たりの人数そのままなので、そのまま使う  
        $repeat = $capacity-1;//初期化
        for ($i=1; $i < $capacity; $i++) { 
            for ($j=$i+1; $j < $repeat+$i+1; $j++) { 
                //試合情報がすでに登録されているか確認する
                $table_name2 = $tournament_id.'_game_index';
                $sql2 = "SELECT * FROM $table_name2 WHERE child_event_id = ? AND draw_id_1 = ? AND draw_id_2 = ?";
                $stmt2 = $tournament_access->prepare($sql2);
                $stmt2->bind_param('iii', $_child_event_id, $i, $j);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $stmt2->close();
                //result2は１行しかないはず
                if ($result2->num_rows === 1) {
                    //存在するならないもしない
                } else {
                    //もし試合が存在しなかったら、試合を作成する
                    //試合情報をデータベースに登録する
                    $sql3 = "INSERT INTO $table_name2 (child_event_id, draw_id_1, draw_id_2, status) VALUES (?, ?, ?, ?)";
                    $stmt3 = $tournament_access->prepare($sql3);
                    $status = -1;
                    $stmt3->bind_param('iiii', $_child_event_id, $i, $j, $status);
                    $stmt3->execute();
                    $stmt3->close();
                }
            }
            $repeat--;
        }
    }
    
}
function create_setting(){
    $child_event_list = get_all_child_event();
    if($child_event_list != null){
        foreach($child_event_list as $child_event){
            global ${'player_list_'.$child_event['id']};
            //上記で作成した配列に、get_all_draw_list()の結果を格納する
            ${'player_list_'.$child_event['id']} = get_draw_list($child_event['id']);
            
        }
    }
}
//suggestの対象となるトーナメントの情報を全て取得する
//capacityもここでわかる
function get_all_child_event(){
    global $cms_access;
    $sql = "SELECT * FROM child_event_list WHERE tournament_id = ? AND status = ?";
    $status = 1;
    $tournament_id = h($_GET['tournament_id']);
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("ii", $tournament_id, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $child_event_list = array();
        while ($row = $result->fetch_assoc()) {
            $child_event_list[] = $row;
        }
        return $child_event_list;
    } else {
        return null;
    }
}
function get_child_event($_child_event_id){
    global $cms_access;
    $sql = "SELECT * FROM child_event_list WHERE tournament_id = ? AND status = ? AND id = ?";
    $status = 1;
    $tournament_id = h($_GET['tournament_id']);
    $child_event_id = h($_child_event_id);
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("iii", $tournament_id, $status, $child_event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return null;
    }
}
//get_all_child_eventで取得した全てのトーナメントの情報を元に、それぞれのドロー情報を取得する

function get_draw_list($child_event_id){
    global  $tournament_access;
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id.'_drawlist';
    $sql = "SELECT * FROM $table_name WHERE child_event_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('i', $child_event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $draw_list[] = $row;
        }
        return $draw_list;
    } else {
        return null;
    }
}
function get_game_finished_suggest(){//suggestの対象外の試合を取得する
    global  $tournament_access;
    $table_name = h($_GET['tournamnet_id']).'_game_index';
    $sql = "SELECT * FROM $table_name";
    $stmt = $tournament_access->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if($row['status'] == 0){
                $table_name2 = h($_GET['tournamnet_id']).'_score_index';
                $sql2 = "SELECT * FROM $table_name2 WHERE game_id = ?";
                $stmt2 = $tournament_access->prepare($sql2);
                $stmt2->bind_param('s', $row['game_id']);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $stmt2->close();
                if ($result2->num_rows > 0) {
                    while ($row2 = $result2->fetch_assoc()) {
                        $draw_list = array(
                            'game_id' => $row['game_id'],
                            'child_event_id' => $row['child_event_id'],
                            'venue_id' => $row['venue_id'],
                            'round' => $row['round'],
                            'match_id' => $row['match_id'],
                            'draw_id_1' => $row['draw_id_1'],
                            'draw_id_2' => $row['draw_id_2'],
                            'status' => $row['status'],
                            'start_at' => $row['start_at'],
                            'score1' => $row2['score1'],
                            'score2' => $row2['score2'],
                            'tiebreak' => $row2['tiebreak'],
                            'winner_id' => $row2['winner_id'],
                            'timestamp' => $row2['timestamp']
                        );
                    }
                }
            }
            $draw_list = array(
                'game_id' => $row['game_id'],
                'child_event_id' => $row['child_event_id'],
                'venue_id' => $row['venue_id'],
                'round' => $row['round'],
                'match_id' => $row['match_id'],
                'draw_id_1' => $row['draw_id_1'],
                'draw_id_2' => $row['draw_id_2'],
                'status' => $row['status'],
                'start_at' => $row['start_at'],
                'score1' => null,
                'score2' => null,
                'tiebreak' => null,
                'winner_id' => null,
                'timestamp' => null
            );
        }
        return $draw_list;
    } else {
        return null;
    }
}
?>