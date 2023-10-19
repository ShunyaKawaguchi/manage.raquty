<?php  
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

//パスワード認証から開始
if ($_SESSION['nonce_id2'] == $_POST['raquty_nonce2'] && $_SESSION['level'] === 1) {
    // 認証エラーメッセージを初期化
    unset($_SESSION['delete_venue_pw_error']);
    // パスワード認証
    if (raquty_password_authentication()) {
        //ログを追加
        add_log('delete_event', $_POST['tournament_id'], $_POST['event_id'] , null, null);
        //削除処理
        delete_event_template($_POST['event_id']);
    } else {
        // 認証エラーエッセー時を登録
        $_SESSION['delete_venue_pw_error'] = 1;
        //リダイレクト
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const TournamentId = " . $_POST['tournament_id'] . ";
                    const EventId = " . $_POST['event_id'] . ";
                    const NonceId = '" . $_POST['raquty_nonce'] . "';
                    
                    const form = document.createElement('form');
                    form.method = 'get';
                    form.action ='" . home_url('Tournament/View/Event/Delete') . "';
            
                    const tournamentIdInput = document.createElement('input');
                    tournamentIdInput.type = 'hidden';
                    tournamentIdInput.name = 'tournament_id';
                    tournamentIdInput.value = TournamentId;
            
                    const eventIdInput = document.createElement('input');
                    eventIdInput.type = 'hidden';
                    eventIdInput.name = 'event_id';
                    eventIdInput.value = EventId;
            
                    const nonceIdInput = document.createElement('input');
                    nonceIdInput.type = 'hidden';
                    nonceIdInput.name = 'raquty_nonce';
                    nonceIdInput.value = NonceId;
            
                    form.appendChild(tournamentIdInput);
                    form.appendChild(eventIdInput);
                    form.appendChild(nonceIdInput);
            
                    document.body.appendChild(form);
                    form.submit();
                });
            </script>";
    
    
    }
}else{
    header("Location: " . home_url('Tournament/List'));   

}


function delete_event_template($event_id){
    //該当の行の削除を行う一連の処理
    $sql = 'DELETE FROM event_list WHERE event_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $event_id);
        
        if ($stmt->execute()) {
            // 削除が成功した場合の処理を追加
            //リダイレクト
            echo "<script>
                    alert('種目を削除しました。');
                    window.location.href = '" . home_url('Tournament/View/Event?tournament_id=') . $_POST['tournament_id'] ."';
                </script>";
        } else {
            // エラーが発生した場合の処理を追加
        }
        
        $stmt->close();
    } else {
        // プリペアドステートメントの準備に失敗した場合の処理を追加
    }
}

?>