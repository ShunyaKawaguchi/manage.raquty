<?php
function create_court($venue_id,$nonce_id){
    $sql = "SELECT venue_name , court_name FROM venues WHERE template_id = ?";
    global $organizations_access; 
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $venue_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) { 
        $row = $result->fetch_assoc();

        //会場名を表示
?>
        <div class="venue_name"><?=$row['venue_name'];?></div>
        <!-- oop関連js -->
        <script src="/components/templates/operation_oop/oop_material.min.js"></script>

        <div class="court_wrap">
<?php   //コートを1面ずつ出力する
        $courts = explode(",", $row['court_name']); 
        $court_num = 1;
        $count = 1;
        foreach( $courts as $court ) { ?>
            <div class="court">
                <?php
                if(isset($_SESSION['game_id'])){
                    ?>
                    <div class="court-selector" onclick="selectCourt(<?= $court ?>)">
                        <form id="court-select-form" action="" method="post">
                            <input type="hidden" name="court_num" id="court_num" value="<?= $count ?>">
                            <input type="hidden" name="venue_id" id="venue_id" value="<?= $venue_id ?>">
                            <input type="submit" class="court-submit" id="court-<?= $count ?>" style="display:none;">
                        </form>
                    </div>
                    <?php
                }?>
                <div class="court_name"><?=$court?></div>
                    <div class="oop_card_wrap">

                    <?php
                    for($status= 1;$status<4;$status++) { //NOWからFINまでぶん回す
                        $table_name2 = h($_GET['tournament_id']).'_game_index';
                        $venue_id = h($_GET['venue_id']).'';
                        $sql = "SELECT * FROM $table_name2 WHERE venue_id = ? AND court_num = ? AND status = ?";
                        global $tournament_access;
                        $stmt = $tournament_access->prepare($sql);
                        $stmt->bind_param("iii", $venue_id,$court_num,$status); 
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $stmt->close();

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {     
                                //oopメインコンテンツ（ネクネクまで表示）?>
                                <div class="oop_card">
                                    <?php create_game_card($court_num,$status,$row,$nonce_id);?>
                                </div>
<?php
                                }
                            }
                        }
?>
                
                    </div>
            </div>  
<?php       $court_num++;$count++;
        } //foreach文ここで終わり ?> 
        </div>
<?php
    } else {
        //会場情報が取得できなかった→不正な遷移か何か→リダイレクト
    }
}

function create_game_card($roop_number,$roop_number2,$row,$nonce_id){
    $id = $roop_number.'-'.$roop_number2;

    if($row["child_event_id"] == -1) {
        //コンソレの時の処理（選手情報取得まで）
        //ドロー名称・選手情報取得
        if( $row["category"] == 1 ) {
            $child_event_name ='シングルス';

            //仮のコンソレのentry_idからエントリーデータを取ってくる
            $entry_1 = get_player($row['draw_id_1']);
            $entry_1_name = $entry_1['user1_name'];
            $entry_1_belonging = $entry_1['user1_belonging'];

            //仮のコンソレのentry_idからエントリーデータを取ってくる
            $entry_2 = get_player($row['draw_id_2']);
            $entry_2_name = $entry_2['user1_name'];
            $entry_2_belonging = $entry_2['user1_belonging'];

        }elseif($row['category'] == 2) {
            $child_event_name = 'ダブルス';

            //仮のコンソレのentry_idからエントリーデータを取ってくる
            $entry_1 = get_player($row['draw_id_1']);
            $entry_1_name = $entry_1['user1_name'].' ・ '.$entry_1['user2_name'];
            $entry_1_belonging = $entry_1['user1_belonging'].' ・ '.$entry_1['user2_belonging'];

            //仮のコンソレのentry_idからエントリーデータを取ってくる
            $entry_2 = get_player($row['draw_id_2']);
            $entry_2_name = $entry_2['user1_name'].' ・ '.$entry_2['user2_name'];
            $entry_2_belonging = $entry_2['user1_belonging'].' ・ '.$entry_2['user2_belonging'];
        }

        //ラウンド名称
        $round ='コンソレーション';
    }else{
        //コンソレ以外の時の処理（選手情報取得まで）
        //ドローデータ取得
        $child_event_data = check_child_event_existance2($row['child_event_id']);

        //ドロー名称
        $child_event_name = $child_event_data['child_event_name'];
        if($child_event_data['event_type'] == 1){//トーナメント
            //ラウンド計算
            $child_capacity = $child_event_data['capacity'];
            $exponent = log($child_capacity, 2);
            
            if ($exponent === false) {
                // エラー処理：$child_capacity がゼロまたは負の場合
                $round = 'Invalid Capacity';
            } elseif ($exponent == $row['round']) {
                $round = 'FR';
            } elseif ($exponent - 1 == $row['round']) {
                $round = 'SFR';
            } elseif ($exponent - 2 == $row['round']) {
                $round = 'QFR';
            } else {
                $round = $row['round'] . 'R';
            }
        }elseif($child_event_data['event_type'] == 2){//総当たり
            //ラウンド計算なし
            $round = '総当たり';
        }

        //選手データ取得
        $event_data = get_event_data($child_event_data['event_id']);
        if($event_data['type'] == 'シングルス'){
            $entry_1 = get_entry_id($row['child_event_id'],$row['draw_id_1']);
            $entry_1 = get_player($entry_1['entry_id']);
            $entry_1_name = $entry_1['user1_name'];
            $entry_1_belonging = $entry_1['user1_belonging'];
            $entry_2 = get_entry_id($row['child_event_id'],$row['draw_id_2']);
            $entry_2 = get_player($entry_2['entry_id']);
            $entry_2_name = $entry_2['user1_name'];
            $entry_2_belonging = $entry_2['user1_belonging'];
        }elseif($event_data['type'] == 'ダブルス'){
            $entry_1 = get_entry_id($row['child_event_id'],$row['draw_id_1']);
            $entry_1 = get_player($entry_1['entry_id']);
            $entry_1_name = $entry_1['user1_name'].' ・ '.$entry_1['user2_name'];
            $entry_1_belonging = $entry_1['user1_belonging'].' ・ '.$entry_1['user2_belonging'];
            $entry_2 = get_entry_id($row['child_event_id'],$row['draw_id_2']);
            $entry_2 = get_player($entry_2['entry_id']);
            $entry_2_name = $entry_2['user1_name'].' ・ '.$entry_2['user2_name'];
            $entry_2_belonging = $entry_2['user1_belonging'].' ・ '.$entry_2['user2_belonging'];
        }
    }
    ?>
    <div class="game_card display">
        <form action="/components/templates/operation_oop/backToSuggest.php" method="post">
            <input type="hidden" name="tournament_id" value="<?=h($_GET['tournament_id']);?>">
            <input type="hidden" name="venue_id" value="<?=h($_GET['venue_id']);?>">
            <input type="hidden" name="raquty_nonce" value="<?=$nonce_id;?>">
            <input type="hidden" name="id" value="<?=$id;?>">
            <button class="cxl" id ="<?=$id;?>"><i class="fas fa-times"></i></button>
        </form>
        <div class="result" id ="<?=$id;?>"><i class="fas fa-check"></i></div>
        <?php if($roop_number2 > 1):?>
        <form action="/components/templates/operation_oop/updateStatus.php" method="post" id =<?='Exit_'.$id;?>>
            <input type="hidden" name="tournament_id" value="<?=h($_GET['tournament_id']);?>">
            <input type="hidden" name="venue_id" value="<?=h($_GET['venue_id']);?>">
            <input type="hidden" name="raquty_nonce" value="<?=$nonce_id;?>">
            <input type="hidden" name="id" value="<?=$id;?>">
            <button class="up" id =<?='Exit_'.$id;?>><i class="arrow-icon fas fa-arrow-up"></i></button>
        </form>
        <?php endif;?>

        <form action="/components/templates/operation_oop/InputResult.php" method="post" id =<?='Result_'.$id;?>>
            <div class="event_section">
                <div class="child_event"><?=$child_event_name;?></div>
                <div class="round"><?=$round;?></div>
            </div>
            <div class="player_section" style="display:block;" id ="<?=$id;?>">
                <div class="wrap">
                    <div class="draw_number"><?=$row['draw_id_1'];?></div>
                    <div class="wrap_2">
                        <div class="player"><?=$entry_1_name;?></div>
                        <div class="belonging"><?=$entry_1_belonging;?></div>
                    </div>
                </div>
                <div class="vs">VS</div>
                <div class="wrap">
                    <div class="draw_number"><?=$row['draw_id_2'];?></div>
                    <div class="wrap_2">
                        <div class="player"><?=$entry_2_name;?></div>
                        <div class="belonging"><?=$entry_2_belonging;?></div>
                    </div>
                </div>
            </div>
            <div class="menu_section" style="display:none;" id ="<?=$id;?>">
                <div class="wrap" id ="<?='win_1_'.$id;?>">
                    <div class="winner">
                        <input type="radio" name="winner_id" value="<?=$row['draw_id_1']?>" required>
                    </div>
                    <div class="wrap_2">
                        <div class="player"><?=$entry_1_name;?></div>
                        <div class="belonging"><?=$entry_1_belonging;?></div>
                    </div>
                    <div class="wrap_3">
                        <input type="text" name="score1" required>
                    </div>
                </div>
                <div class="wrap" id ="<?='win_2_'.$id;?>">
                    <div class="winner">
                        <input type="radio" name="winner_id" value ="<?=$row['draw_id_2']?>">
                    </div>
                    <div class="wrap_2">
                        <div class="player"><?=$entry_2_name;?></div>
                        <div class="belonging"><?=$entry_2_belonging;?></div>
                    </div>
                    <div class="wrap_3">
                        <input type="text" name="score2" required>
                    </div>
                </div>
                <div class="wrap">
                    <div class="tiebreak">
                        <div class="tb_wrap">
                            <input type="checkbox" name="tiebreak">
                            <span>タイブレーク</span>
                            <select name="tb_score" style="text-align: center;">
                                <?php for($s= 0;$s<20;$s++):?>
                                    <option value="<?=$s;?>"><?=$s;?></option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="submit_wrap">
                            <input type="hidden" name="id" value="<?=$id;?>">
                            <input type="hidden" name="tournament_id" value="<?=h($_GET['tournament_id']);?>">
                            <input type="hidden" name="venue_id" value="<?=h($_GET['venue_id']);?>">
                            <input type="hidden" name="game_id" value="<?=$row['game_id'];?>">
                            <input type="hidden" name="raquty_nonce" value="<?=$nonce_id;?>">
                            <!-- <input type="submit" name="back" formaction="" class="back" value="戻る" data-target-id="<?=$id;?>"> -->
                            <input type="submit" name='result_submit' value="登録" id =<?='Result_'.$id;?> style="margin-right:40px;">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

function create_player_option($number){
    $venue_id = h($_GET['venue_id']);
    $event_ids = get_venue_data_today($venue_id);

    if(!empty($event_ids)){
        echo '<select name="player_'.$number.'"><option readonly>選択してください</option>';
            foreach($event_ids as $event_id){
                get_event_entrylist_today($event_id);
            }
        echo '</select>';
    }
}

function get_venue_data_today($venue_id) {
    global $tournament_access;
    $table_name = h($_GET['tournament_id']) . '_venues';
    $todayDate = date("Y-m-d");
    $sql = "SELECT event_id FROM $table_name WHERE venue_id = ? AND event_date = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("is", $venue_id,$todayDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $event_ids = array();

    if ($result->num_rows === 0) {
        return $event_ids;
    } else {
        while ($row = $result->fetch_assoc()) {
            $event_ids[] = $row['event_id'];
        }
        return $event_ids;
        
    }
}

function get_event_entrylist_today($event_id) {
    $event_data = get_single_event_data( $event_id );
    if(!empty($event_data)) {
        if($event_data['type'] == 'シングルス') {
            option_generater_today(1,$event_id,$event_data['event_name']);
        }elseif($event_data['type'] == 'ダブルス') {
            option_generater_today(2,$event_id, $event_data['event_name']);
        }
    }
}

function option_generater_today($type,$event_id,$event_name){
    global $tournament_access;
    $table_name = h($_GET['tournament_id']) . '_entrylist';
    $sql = "SELECT * FROM $table_name WHERE event_id = ? AND (draw_id IS NULL OR draw_id NOT IN (9999, 1))";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = '';

    if($type == 1){
        if ($result->num_rows > 0) {
            $options .= '<optgroup label="'.$event_name.'">';

            while ($row = $result->fetch_assoc()) {
                if(!empty($row['user1_belonging'])) {
                    $belonging = $row['user1_belonging'];
                }else{
                    $belonging = 'フリー';
                }

                $option = '<option value="' .$row['event_id'].'-'. $row['user1_id'] .'-'.$row['id']. '">' . $row['user1_name'] . ' ( '.$belonging.' ) </option>';
                $options .= $option; 
            }

            $options .= '</optgroup>';
        }

        echo $options;

    }elseif($type == 2){
        if ($result->num_rows > 0) {
            $options .= '<optgroup label="'.$event_name.'">';

            while ($row = $result->fetch_assoc()) {
                if(!empty($row['user1_belonging'])) {
                    $belonging = $row['user1_belonging'];
                }else{
                    $belonging = 'フリー';
                }
                if(!empty($row['user2_belonging'])) {
                    $belonging_2 = $row['user2_belonging'];
                }else{
                    $belonging_2 = 'フリー';
                }

                $option = '<option value="'  .$row['event_id'].'-'. $row['user1_id'] .'-'.$row['id']. '">' . $row['user1_name'] . ' ( '.$belonging.' ) </option><option value="' .$row['event_id'].'-'. $row['user2_id'] .'-'.$row['id']. '">' . $row['user2_name'] . ' ( '.$belonging_2.' ) </option>';
                $options .= $option; 
            }

            $options .= '</optgroup>';
        }

        echo $options;
    }
}

function get_entry_id($child_event_id,$draw_id){
    global $tournament_access;
    $table_name = h($_GET['tournament_id']).'_drawlist';
    $sql = "SELECT * FROM $table_name WHERE child_event_id = ? AND draw_id = ?";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("ii", $child_event_id,$draw_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $row = $result->fetch_assoc();
    return $row;
}

