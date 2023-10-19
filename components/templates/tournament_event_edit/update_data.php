<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if ($_SESSION['nonce_id'] == $_POST['raquty_nonce']) {
    if (isset($_POST['event_id'])) {
        $tournament_id = h($_POST['tournament_id']);
        $event_id = h($_POST['event_id']);
        $event_name = h($_POST['event_name']);
        $type = h($_POST['type']);
        $gender = h($_POST['gender']);
        $capacity = h($_POST['capacity']);
        $target = h($_POST['target']);
        $min_age = h($_POST['min-age']);
        $max_age = h($_POST['max-age']);
        $fee = h($_POST['fee']);

        // パラメーターをバインド
        $sql = 'UPDATE event_list SET event_name = ?, type = ?, gender = ?, capacity = ?, target = ?, `min-age` = ?, `max-age` = ? ,`fee` = ? WHERE event_id = ?';
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('sssisiiii', $event_name, $type, $gender, $capacity, $target, $min_age, $max_age, $fee , $event_id);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // UPDATEが成功した場合の処理
            //ログの追加
            $log_after = $event_name.'/'.$type.'/'.$gender.'/'.$capacity.'人/'.$target.'/'.$min_age.'歳から'.$max_age.'歳まで/'.$fee.'円';
            add_log('update_event', $tournament_id, $event_id , h($_POST['log_before']), $log_after);
            //リダイレクト
            echo "<script>
                    window.onload = function() {
                        const message = '種目の更新が完了しました。';
                        alert(message);
                        const newLocation = '" . home_url('Tournament/View/Event/Edit?tournament_id=') . "' + $tournament_id;
                        window.location.href = newLocation;
                    }
                </script>";
        } else {
            // エラーが発生した場合の処理
            error_log("UPDATE query failed: " . $stmt->error);
        }

        $stmt->close();
    } else {
        // エラーが発生した場合の処理
    }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>