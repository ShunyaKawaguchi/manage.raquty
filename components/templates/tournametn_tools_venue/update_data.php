<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if ($_SESSION['nonce_id'] === $_POST['raquty_nonce']) {
    update_tournament_venue_v2();
    echo '<script>
            const message = "情報を更新しました。";
            alert(message);
          </script>';
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateId = " . json_encode($_POST['tournament_event']) . ";
            const templateId2 = " . json_encode($_POST['tournament_date']) . ";
            
            const form = document.createElement('form');
            form.method = 'post';
            form.action = " . json_encode(home_url('Tournament/View/Tools/Venue?tournament_id=' . $_POST['tournament_id'])) . ";
            
            const templateIdInput = document.createElement('input');
            templateIdInput.type = 'hidden';
            templateIdInput.name = 'tournament_event';
            templateIdInput.value = templateId;
            
            form.appendChild(templateIdInput);
    
            const templateIdInput2 = document.createElement('input');
            templateIdInput2.type = 'hidden';
            templateIdInput2.name = 'tournament_date';
            templateIdInput2.value = templateId2;
            
            form.appendChild(templateIdInput2);
            
            document.body.appendChild(form);
            form.submit();
        });
    </script>";
          
}


// データベース接続解除
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/disconnect-database.php');


function update_tournament_venue_v2(){
    global $tournament_access;
    
    $table_name = $_POST['tournament_id'] . '_venues';
    $delete_sql = 'DELETE FROM ' . $table_name . ' WHERE event_id = ? AND event_date = ?';
    
    $stmt_delete = $tournament_access->prepare($delete_sql);
    
    if ($stmt_delete) {
        $event_id = $_POST['tournament_event'];
        $event_date = $_POST['tournament_date'];
        $stmt_delete->bind_param('is', $event_id , $event_date);
        
        if ($stmt_delete->execute()) {
            // 削除が成功した場合の処理
            insert_venue_v2();
        } else {
            // エラーが発生した場合の処理
        }
        
        $stmt_delete->close();
    }
}

function insert_venue_v2() {
    global $tournament_access;
    
    $venues = $_POST['venue'];
    $tournament_date = $_POST['tournament_date'];
    $tournament_event = $_POST['tournament_event'];
    $table_name = $_POST['tournament_id'] . '_venues';

    $sql = 'INSERT INTO ' . $table_name . ' (event_date, event_id, venue_id) VALUES (?, ?, ?)';
    $stmt = $tournament_access->prepare($sql);

    foreach ($venues as $single_venue) {
        $stmt->bind_param('sii', $tournament_date, $tournament_event, $single_venue);

        if ($stmt->execute()) {
            // 成功した場合の処理を追加
        } else {
            // エラーが発生した場合の処理を追加
        }
    }

    $stmt->close();
}

