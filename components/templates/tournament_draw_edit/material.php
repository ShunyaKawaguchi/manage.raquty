<?php 
function generate_tournament($num,$type,$nonce_id){ 
    $exponent = log($num, 2);
    $upper_exponent = ceil($exponent) + 1;
    $row_num = pow(2, $upper_exponent);
    ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <?php
                for($i = 0; $i < $upper_exponent; $i++){
                    if ($i == 0) {
                        // echo '<th></th>';
                    }elseif ($i == $upper_exponent - 3) {
                        echo '<th>QFR</th>';
                    } elseif ($i == $upper_exponent - 2) {
                        echo '<th>SFR</th>';
                    } elseif($i == $upper_exponent - 1) {
                        echo '<th>FR</th>';
                    }else{
                        echo '<th>'.$i.'R</th>';
                    }
                }                
                ?>
            </tr>
            </thead>
            <tbody>
                    <?php
                        for($t = 1; $t <= $row_num; $t++){
                            if( $t == 1){
                                $draw_num = 1;
                            }else{
                                $draw_num = ($t + 1)/2;
                            }
                            if($t % 2 == 0){
                                echo '<tr><td></td>';
                            }else{
                                $player_data = getParticipants($draw_num,$_GET['tournament_id'],$_GET['child_event_id']);
                                echo '<tr><td class="player_section bottom"><span style="width:20px;text-align:center;">'.$draw_num.'</span>';
                                if(!empty($player_data)){;?>
                                <form action='/components/templates/tournament_draw_edit/draw_delete.php' method='post'>
                                    <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'];?>">
                                    <input type="hidden" name="child_event_id" value="<?php echo $_GET['child_event_id'];?>">
                                    <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id;?>">
                                    <input type="hidden" name="draw_num" value="<?php echo $draw_num;?>">
                                    <input type="submit" value="<<">
                                </form>
                            
                                <?php if($type == 'シングルス'):?>
                                    <div class="player_data">
                                        <?php echo $player_data['user1_name'];?> ( <?php echo $player_data['user1_belonging'];?> )
                                    </div>
                                    <?php elseif($type == 'ダブルス'):?>
                                    <div class="player_data">
                                        <?php echo $player_data['user1_name'];?> ( <?php echo $player_data['user1_belonging'];?> )<br>
                                        <?php echo $player_data['user2_name'];?> ( <?php echo $player_data['user2_belonging'];?> )
                                    </div>
                                    <?php endif;
                                }else{
                                    echo '<div class="player_data" style="text-align:center;padding-left:50px;">bye</div>';
                                }
                                echo '</td>';
                            }
                                for($s = 1; $s <= $upper_exponent; $s++){
                                    if($s == 1){
                                        // if($t % 4 == 3){
                                        //     echo '<td class="bottom right"></td>';
                                        // }elseif($t % 4 == 2){
                                        //     echo '<td class="right"></td>';
                                        // }elseif($t % 4 == 1){
                                        //     echo '<td class="bottom"></td>';
                                        // }elseif($t % 4 == 0){
                                        //     echo '<td></td>';
                                        // }
                                    }elseif($s == 2){
                                        if($t % 4 == 2){
                                            echo '<td class="bottom left"></td>';
                                        }elseif($t % 4 == 3){
                                            echo '<td class="left"></td>';
                                        }else{
                                            echo '<td></td>';
                                        }
                                    }else{
                                        $num_1 = pow(2, $s);
                                        if($t % $num_1 == $num_1/2){
                                            echo '<td class="bottom left"></td>';
                                        }else{
                                            $first = pow(2, $s - 2) + 1;
                                            if ($s == 3) {
                                                $end = 4;
                                            } else {
                                                $end = pow(2, $s - 1);
                                            }

                                            for($zz = $first; $zz <= $end*1.5; $zz++) {
                                                $resultArray[] = $zz;
                                            }

                                            $remainder = $t % $num_1;

                                            if (in_array($remainder, $resultArray)) {
                                                echo '<td class="left"></td>';
                                            } else {
                                                echo '<td></td>';
                                            }   
                                            unset($resultArray);
                                            $resultArray = array();
                                        }
                                   }
                                }
                            echo '</tr>';
                        }
                    ?>
                </tr>
            </tbody>
        </table>
<?php

}

function import_player_data($event_id,$capacity,$nonce_id,$dont_ids){
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id.'_entrylist';
    $event_data = get_single_event_data($event_id);

    global $tournament_access;
    $sql = "SELECT * FROM $table_name WHERE event_id = ? AND (draw_id IS NULL OR draw_id != 9999)";
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;margin:30px;'>まだ、エントリーがありません。</p>";
    } else {
        while ($row = $result->fetch_assoc()) {
            if (!in_array($row['id'], $dont_ids)):?>
            <div class="player">
                <div class="about">
                    <?php if($event_data['type']=="シングルス"):?>
                    <?php echo $row['user1_name']; ?><br>
                    ( <?php echo $row['user1_belonging']; ?> ) 
                    <?php elseif($event_data['type']=="ダブルス"):?>
                        <?php echo $row['user1_name']; ?> ( <?php echo $row['user1_belonging']; ?> )<br>
                        <?php echo $row['user2_name']; ?> ( <?php echo $row['user2_belonging']; ?> ) 
                    <?php endif;?>
                </div>
                <form action="/components/templates/tournament_draw_edit/draw_register.php" method="post">
                    <input type="hidden" name="entry_id" value="<?php echo $row['id'];?>">
                    <input type="hidden" name="event_id" value="<?php echo $row['event_id'];?>">
                    <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'];?>">
                    <input type="hidden" name="child_event_id" value="<?php echo $_GET['child_event_id'];?>">
                    <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id;?>">
                    <input type="number" name="draw_num" id="DrawNum" required min="1" max="<?php echo $capacity;?>">
                    <input type="submit" value="&gt;&gt;">
                </form>
            </div>
        <?php endif;
        }
    }
}

function getEntryIdsByChildEvent($child_event_id, $tournament_id) {
    global $tournament_access;
    $table_name = $tournament_id . '_drawlist';
    $sql = "SELECT entry_id FROM $table_name WHERE child_event_id = ?";
    $stmt = $tournament_access->prepare($sql);

    if (!$stmt) {
        return []; 
    }

    $stmt->bind_param("i", $child_event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $entry_ids = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $entry_ids[] = $row['entry_id'];
        }
    }

    $stmt->close();

    return $entry_ids;
}

function getParticipants($draw_id,$tournament_id,$child_event_id) {
    global $tournament_access;
    $table_name = $tournament_id . '_drawlist';
    $sql = "SELECT entry_id FROM $table_name WHERE child_event_id = ? AND draw_id = ?";
    $stmt = $tournament_access->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ii", $child_event_id , $draw_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();

        $entry_data = getPlayerInfoByDrawId($row['entry_id'],$tournament_id);
        return $entry_data; 

    } else {
        return false; 
    }

}

function getPlayerInfoByDrawId($entry_id,$tournament_id){
    global $tournament_access;
    $table_name = $tournament_id . '_entrylist';
    $sql = "SELECT * FROM $table_name WHERE id = ?";
    $stmt = $tournament_access->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("i",$entry_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row; 
    } else {
        return false; 
    }


}