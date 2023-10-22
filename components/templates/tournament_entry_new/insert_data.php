<?php 
session_start();

require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/have_user_id.php');

if ($_SESSION['nonce_id2'] !== $_POST['raquty_nonce2']) {
    header("Location: " . home_url('Tournament/List'));
} else {
    // エントリー済みか判定
    $player1_id = !empty($_POST['player1_id'])?  h($_POST['player1_id']) : -1; 
    if(isset($_POST['player2_id'])){
        $player2_id = !empty($_POST['player2_id']) ? h($_POST['player2_id']) : -1; 
    }else{
        $player2_id = -1;
    }

    if (entry_exists($player1_id, $player2_id)) {
        $player_data1 = get_player_data_by_playerID($player1_id);
        $player_data2 = $player2_id ? get_player_data_by_playerID($player2_id) : -1;
        if($player_data1!==false){
            $user1_id = $player_data1['user_id'];
        }else{
            $user1_id =null;
        }
        if($player_data2!==false){
            $user2_id = $player_data2['user_id'];
        }else{
            $user2_id =null;
        }
        //entry_eventテーブルにも情報追加
        insert_user_log($user1_id,h($_POST['event_id']));
        $check_event = get_single_event_data( h($_POST['event_id']) );
        if($check_event['type']==='ダブルス'){
            insert_user_log($user2_id,h($_POST['event_id'])); 
        }

        //エントリーリストに追加
        insert_data($user1_id,$user2_id);

    } else {
        $err = 'エントリー済みの選手IDが含まれます。初めからやり直してください。';
        back_to_form($err);
    }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');

function back_to_form($err) {
    $tournament_id = $_POST['tournament_id'];
    $nonce_id = $_POST['raquty_nonce'];
    $nonce_id2 = $_POST['raquty_nonce2'];
    $event_id = $_POST['event_id'];

    $url = home_url("Tournament/View/Entry/New?tournament_id=" . $tournament_id);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const err = <?php echo json_encode($err); ?>;

            if (err) {
                alert(err); 
            }

            const form = document.createElement('form');
            form.method = 'post';
            form.action = '<?php echo $url; ?>';

            const inputs = [
                { name: 'raquty_nonce', value: '<?php echo $nonce_id; ?>' },
                { name: 'raquty_nonce2', value: '<?php echo $nonce_id2; ?>' },
                { name: 'event_id', value: '<?php echo $event_id; ?>' },
                { name: 'tournament_id', value: '<?php echo $tournament_id; ?>' }
            ];

            inputs.forEach(inputData => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputData.name;
                input.value = inputData.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    </script>
    <?php
}

function entry_exists($player1_id, $player2_id) {
    $tournament_id = $_POST['tournament_id'];
    $event_id = $_POST['event_id'];
    $table_name = $tournament_id . "_entrylist";

    $player_data1 = get_player_data_by_playerID($player1_id);
    $player_data2 = $player2_id ? get_player_data_by_playerID($player2_id) : -1;

    if($player1_id!=-1){
        if($player_data1==false){
            $err = '実在しない選手IDが入力されました。最初からやり直してください。';
            back_to_form($err);
            exit;
        }else{
            $user1_id = $player_data1['user_id'];
        }
    }else{
        $user1_id = -1;
    }
    if($player2_id!=-1){
        if($player_data2==false){
            $err = '実在しない選手IDが入力されました。最初からやり直してください。';
            back_to_form($err);
            exit;
        }else{
            $user2_id = $player_data2['user_id'];
        }
    }else{
        $user2_id = -1;
    }

    // プレイヤー1がエントリー済みかどうかをチェック
    $sql1 = "SELECT * FROM $table_name WHERE (user1_id = ? OR user2_id = ?) AND event_id = ? AND (draw_id != 9999 OR draw_id IS NULL)";
    global $tournament_access;
    $stmt1 = $tournament_access->prepare($sql1);

    if (!$stmt1) {
        return false;
    }

    $stmt1->bind_param("iii",  $user1_id ,  $user1_id , $event_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($result1 && $result1->num_rows > 0) {
        $stmt1->close();
        return false;
    }

    // プレイヤー2がエントリー済みかどうかをチェック
    if ($player2_id && $player_data2) {
        $sql2 = "SELECT * FROM $table_name WHERE (user1_id = ? OR user2_id = ?) AND event_id = ? AND (draw_id != 9999 OR draw_id IS NULL)";
        $stmt2 = $tournament_access->prepare($sql2);

        if (!$stmt2) {
            return false;
        }

        $stmt2->bind_param("iii",  $user2_id ,  $user2_id , $event_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2 && $result2->num_rows > 0) {
            $stmt2->close();
            return false;
        }
    }

    return true;
}

function insert_data($user1_id,$user2_id) {
    $event_id = h($_POST['event_id']);
    $user1_name = isset($_POST['player1_name']) ? h($_POST['player1_name']) : '';
    $user1_belonging = isset($_POST['player1_belonging']) ? h($_POST['player1_belonging']) : '';
    $user2_name = isset($_POST['player2_name']) ? h($_POST['player2_name']) : '';
    $user2_belonging = isset($_POST['player2_belonging']) ? h($_POST['player2_belonging']) : '';
    $tournament_id = isset($_POST['tournament_id']) ? h($_POST['tournament_id']) : '';
    if($user1_id == 0){$user1_id = null;};
    if($user2_id == 0){$user2_id = null;};
    $table_name = $tournament_id . "_entrylist";

    // パラメーターをバインド
    $sql = 'INSERT INTO ' . $table_name . ' (user1_id, user1_name, user1_belonging, user2_id, user2_name, user2_belonging, event_id) VALUES (?, ?, ?, ?, ?, ?, ?)';
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param('ississi', $user1_id, $user1_name, $user1_belonging, $user2_id, $user2_name, $user2_belonging, $event_id);

    if ($stmt->execute()) {
        // INSERTが成功した場合の処理
        //ログ追加
        $log_after = 'user1_id:'.$user1_id.'/user1_name:'.$user1_name.'/user1_belonging:'.$user1_belonging.'/user2_id:'.$user2_id.'/user2_name:'.$user2_name.'/user2_belonging:'.$user2_belonging;
        add_log('add_entry' , $tournament_id, $event_id , null, $log_after);

        //リダイレクト処理など
        $last_inserted_event_id = mysqli_insert_id($tournament_access);
        showAlertAndSubmitForm($tournament_id, h($_POST['raquty_nonce2']), $last_inserted_event_id, $event_id);
    } else {
        // エラーが発生した場合の処理
        error_log("INSERT query failed: " . $stmt->error);
    }

    $stmt->close();
}

function showAlertAndSubmitForm($tournament_id, $nonce_id2, $entry_id, $event_id) {
    $nonce_id = $_POST['raquty_nonce'];
    echo "<script>
        window.onload = function() {
            const message = '選手の追加登録が完了しました。';
            alert(message);

            const form = document.createElement('form');
            form.method = 'post';
            form.action = '" . home_url("Tournament/View/Entry/About?tournament_id=" . $tournament_id) . "';

            const nonceIdInput = document.createElement('input');
            nonceIdInput.type = 'hidden';
            nonceIdInput.name = 'raquty_nonce';
            nonceIdInput.value = '$nonce_id';

            const entryIdInput = document.createElement('input');
            entryIdInput.type = 'hidden';
            entryIdInput.name = 'entry_id';
            entryIdInput.value = '$entry_id'; 

            const eventIdInput = document.createElement('input');
            eventIdInput.type = 'hidden';
            eventIdInput.name = 'event_id';
            eventIdInput.value = '$event_id';

            form.appendChild(nonceIdInput);
            form.appendChild(entryIdInput);
            form.appendChild(eventIdInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>";
}

function insert_user_log($user_id, $event_id) {
    global $user_access;

    // 既存のデータを検索
    $checkQuery = "SELECT * FROM entry_event WHERE user_id = ? AND event_id = ?";
    $checkStmt = $user_access->prepare($checkQuery);

    if (!$checkStmt) {
        die('データベース接続エラーが発生しました。管理者に連絡してください。');
    }

    $checkStmt->bind_param("ii", $user_id, $event_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // 既にデータが存在する場合は処理を終了
        $checkStmt->close();
        return;
    }

    $checkStmt->close();

    if(!empty($user_id)) {
        // データを挿入
        $insertQuery = "INSERT INTO entry_event (user_id, event_id) VALUES (?, ?)";
        $stmt = $user_access->prepare($insertQuery);

        if (!$stmt) {
            die('データベース接続エラーが発生しました。管理者に連絡してください。');
        }

        $stmt->bind_param("ii", $user_id, $event_id);

        if ($stmt->execute()) {
            // データ挿入完了
        } else {
            die('データベース接続エラーが発生しました。管理者に連絡してください。');
        }

        $stmt->close();
    }
}
