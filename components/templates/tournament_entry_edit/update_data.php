<?php
session_start();

require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/have_user_id.php');

function showAlertAndSubmitForm($tournament_id, $nonce_id, $entry_id, $event_id) {
    $_SESSION['update_entry'] = '選手情報の更新が完了しました。';
    echo "<script>
        window.onload = function() {
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

if ($_SESSION['nonce_id3'] == $_POST['raquty_nonce3']) {
    $tournament_id = h($_POST['tournament_id']);
    $entry_id = h($_POST['entry_id']);
    $event_id = h($_POST['event_id']);
    $nonce_id = h($_POST['raquty_nonce']);
    $nonce_id2 = h($_POST['raquty_nonce2']);
    $player1_name = h($_POST['player1_name']);
    $player1_belonging = h($_POST['player1_belonging']);
    if (isset($_POST['player2_name'])) {
        $player2_name = h($_POST['player2_name']);
    } else {
        $player2_name = "";
    }
    if (isset($_POST['player2_belonging'])) {
        $player2_belonging = h($_POST['player2_belonging']);
    } else {
        $player2_belonging = "";
    }

    unset($_SESSION['edit_player_pw_error']);
    
    if (raquty_password_authentication()) {
        if (isset($_POST['player2_name'])) {
            $player2_name = h($_POST['player2_name']);
        } else {
            $player2_name = "";
        }
        if (isset($_POST['player2_belonging'])) {
            $player2_belonging = h($_POST['player2_belonging']);
        } else {
            $player2_belonging = "";
        }
        $player_status = h($_POST['player_status']);
        $table_name = $tournament_id . "_entrylist";

        $sql = "UPDATE $table_name SET user1_name = ?, user1_belonging = ?, user2_name = ?, user2_belonging = ?, draw_id = ? WHERE id = ?";
        $stmt = $tournament_access->prepare($sql);
        $stmt->bind_param('ssssii', $player1_name, $player1_belonging, $player2_name, $player2_belonging, $player_status, $entry_id);

        if ($stmt->execute()) {
            //ログ追加
            $log_after = 'user1_name:'.$player1_name.'/user1_belonging:'.$player1_belonging.'/user2_name:'.$player2_name.'/user2_belonging:'.$player2_belonging.'/status:'.$player_status;
            add_log('edit_entry' , $tournament_id, $event_id , $_POST['log_before'] , $log_after);

            //リダイレクト
            showAlertAndSubmitForm($tournament_id, $nonce_id, $entry_id, $event_id);
        } else {
            error_log("UPDATE query failed: " . $stmt->error);
        }

        $stmt->close();
    } else {
        $_SESSION['edit_player_pw_error'] = 1;
        $url = home_url("Tournament/View/Entry/Edit?tournament_id=" . $tournament_id);
        
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '$url';

            const nonceIdInput = document.createElement('input');
            nonceIdInput.type = 'hidden';
            nonceIdInput.name = 'raquty_nonce';
            nonceIdInput.value = '$nonce_id';

            const nonceId2Input = document.createElement('input');
            nonceId2Input.type = 'hidden';
            nonceId2Input.name = 'raquty_nonce2';
            nonceId2Input.value = '$nonce_id2';

            const entryIdInput = document.createElement('input');
            entryIdInput.type = 'hidden';
            entryIdInput.name = 'entry_id';
            entryIdInput.value = '$entry_id';

            const eventIdInput = document.createElement('input');
            eventIdInput.type = 'hidden';
            eventIdInput.name = 'event_id';
            eventIdInput.value = '$event_id';

            form.appendChild(nonceIdInput);
            form.appendChild(nonceId2Input);
            form.appendChild(entryIdInput);
            form.appendChild(eventIdInput);

            document.body.appendChild(form);
            form.submit();
        });
        </script>";
    }
}

require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>
