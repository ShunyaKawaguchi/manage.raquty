<?php 
//全ての種目のエントリーリストを作成
function create_entry_list($tournament_id,$nonce_id) {
    $sql = 'SELECT event_id,type FROM event_list WHERE tournament_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;margin:30px;'>まだ、種目が登録されていません。</p>";
    } else {
        while ($row = $result->fetch_assoc()):
            $event_data = get_event_data($row['event_id']);
            $entry_number_with_cxl = get_entry_number_with_cxl($row['event_id']);
            $entry_number = get_entry_number($row['event_id']);
        ?>
        <div class="Entry_List">
            <form action="<?php echo home_url('Tournament/View/Entry/New?tournament_id=').h($_GET['tournament_id']); ?>" method='post'>
                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                <input type="hidden" name="event_id" value="<?php echo $row['event_id'] ?>">
                <div class="Event_Name"><?php echo $event_data['event_name'] ?><?php if($_SESSION['level']===1):?><input type="submit" style="margin-left:10px;" value="選手追加&nbsp;&gt;&gt;"><?php endif;?></div>
            </form>
            <div class="Event_Detail">
                <?php if ($entry_number_with_cxl !== 0): ?>
                    <div class="Number">エントリー状況&nbsp;:&nbsp;&nbsp;<?php echo $entry_number ?>枠&nbsp;/&nbsp;<?php echo $event_data['capacity'] ?>枠&nbsp;&nbsp;<span class="Display_List"><?php //ここに▼があった ?></span></div>
                <?php endif; ?>
            </div>
            <?php if ($entry_number_with_cxl === 0): ?>
                <p style='font-size:14px;margin:30px;'>まだ、エントリーはありません。</p>
            <?php else: ?>
                <table class="Entry_Listing">
                    <thead>
                        <tr>
                            <th></th>
                            <th>選手ID</th>
                            <th>選手名</th>
                            <th>所属</th>
                            <th>状況</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php get_entry_list($row['event_id'],$_GET['tournament_id'],$row['type'],$nonce_id); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php endwhile;
    }
}


//種目別のエントリーリスト作成
function get_entry_list($event_id,$tournament_id,$type,$nonce_id) {
    // エントリーテーブル名の構築
    $entry_table_name = $tournament_id . '_entrylist';

    // エントリー数を取得するSQL
    $sql = "SELECT * FROM $entry_table_name WHERE event_id = ?";
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // カウンターの初期化
    $count_number = 1;

    // エントリー情報を表示
    while ($row = $result->fetch_assoc()) {
        if($type!=="ダブルス"){
            $player1 = get_player_data($row['user1_id'] );
            if($player1==null){$player1_id = '';}else{$player1_id = $player1['player_id'] ;};
        ?>
        <tr>
            <td><?php echo $count_number ?></td>
            <td><?php echo $player1_id ?></td>
            <td><?php echo $row['user1_name'] ?></td>
            <td><?php echo $row['user1_belonging'] ?></td>
        <?php }else{ 
            $player1 = get_player_data($row['user1_id'] );
            if($player1==null){$player1_id = '';}else{$player1_id = $player1['player_id'] ;};
            $player2 = get_player_data($row['user2_id'] ); 
            if($player2==null){$player2_id = '';}else{$player2_id = $player2['player_id'] ;};?>
            <td><?php echo $count_number ?></td>
            <td><?php echo $player1_id ?><br><?php echo $player2_id ?></td>
            <td><?php echo $row['user1_name'] ?><br><?php echo $row['user2_name'] ?></td>
            <td><?php echo $row['user1_belonging'] ?><br><?php echo $row['user2_belonging'] ?></td>
        <?php } ?>
            <td><?php if($row['draw_id']===9999){echo 'CXL済';}else{echo '受付済';} ?></td>
            <td>
                <form action="<?php echo home_url('Tournament/View/Entry/About?tournament_id='.$tournament_id) ?>" method="post">
                    <input type="hidden" name="entry_id" value="<?php echo $row['id'] ?>">
                    <input type="hidden" name="event_id" value="<?php echo $row['event_id'] ?>">
                    <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                    <input type="submit" value="詳細">
                </form>
            </td>
        </tr>
        <?php
        $count_number++;
    }
}


//種目別のエントリー人数を取得
function get_entry_number($event_id) {
    $entry_table_name = $_GET['tournament_id'] . '_entrylist';
    $sql = "SELECT COUNT(id) as row_count FROM $entry_table_name WHERE event_id = ? AND (draw_id != 9999 OR draw_id IS NULL)";
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['row_count'];
}

function get_entry_number_with_cxl($event_id) {
    $entry_table_name = $_GET['tournament_id'] . '_entrylist';
    $sql = "SELECT COUNT(id) as row_count FROM $entry_table_name WHERE event_id = ? ";
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['row_count'];
}


//種目情報を配列で返す
function get_event_data( $event_id ){
    $sql = "SELECT * FROM event_list WHERE event_id = ?";
    global $cms_access; 
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $event_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row;  
}
?>