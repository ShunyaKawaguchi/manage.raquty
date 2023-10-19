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
    $tournament_name = h($_POST['tournament_name']);
    $date = h($_POST['date']);
    if (substr($date, 0, 1) === ',') {
        $date = substr($date, 1);
    }
    $target = h($_POST['target']);
    $entry_start = h($_POST['entry_start']);
    $entry_end = h($_POST['entry_end']);
    $draw_open = h($_POST['draw_open']);
    $comment = h($_POST['comment']);
    $region = h($_POST['prefecture']); 
    $tournament_id = h($_POST['tournament_id']); 
    $venue = $_POST['venue'];

    $sql = 'UPDATE tournament SET tournament_name = ?, post_modified = NOW(), date = ?, target = ?, entry_start = ?, entry_end = ?, draw_open = ?, comment = ?, region = ? WHERE tournament_id = ?';
    $stmt = $cms_access->prepare($sql);

    $stmt->bind_param('ssssssssi', $tournament_name, $date, $target, $entry_start, $entry_end, $draw_open, $comment, $region, $tournament_id);

    if ($stmt->execute()) {
        // 更新が成功した場合の処理
        //会場データ更新
        update_tournament_venue( $tournament_id , $venue );
        //ログ
        $venue_txt = '';
        $first = true;
        foreach($venue as $single_venue) {
            if ($first) {
                $venue_txt = $single_venue;
                $first = false;
            } else {
                $venue_txt = $venue_txt . ',' . $single_venue;
            }
        }
        $log_before = $_POST['log_before'];
        $log_after = $tournament_name.'/date:'.$date.'/'.$target.'/entry_start:'.$entry_start.'/entry_end:'.$entry_end.'/draw_open:'.$draw_open.'/'.$comment.'/'.$region.'/venue:'.$venue_txt;
        add_log('update_tournament' , $tournament_id, null , $log_before, $log_after);
        echo "<script>
                    window.addEventListener('load', function() {
                        const message = '情報を更新しました。';
                        alert(message);
                        const newLocation = '" . home_url('Tournament/View/Information?tournament_id=') . "' + " . $tournament_id . ";
                        window.location.href = newLocation;
                    });
                </script>";

    } else {
        // エラーが発生した場合の処理
    }

    $stmt->close();
}


// データベース接続解除
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/disconnect-database.php');


function update_tournament_venue( $tournament_id , $venue ){
    if(!empty($venue)){
            $delete_sql = 'DELETE FROM venues WHERE tournament_id = ?';
            global $cms_access;
            $stmt_delete = $cms_access->prepare($delete_sql);
            
            if ($stmt_delete) {
                $stmt_delete->bind_param('i', $tournament_id);
                
                if ($stmt_delete->execute()) {
                    // 削除が成功した場合の処理
                    //再度会場データーを追加
                    insert_venue($tournament_id , $venue);
                } else {
                    // エラーが発生した場合の処理
                }
                
                $stmt_delete->close();
            }
    }
}

function insert_venue($tournament_id , $venue){
    global $cms_access;
    foreach( $venue as $single_venue){

        $sql = 'INSERT INTO venues (tournament_id , template_id) VALUES (?, ?)';
        $stmt = $cms_access->prepare($sql);

        $stmt->bind_param('ii', $tournament_id, $single_venue);

        if ($stmt->execute()) {
        
        }
         else {
            // エラーが発生した場合の処理を追加
        }
    }
}
?>
