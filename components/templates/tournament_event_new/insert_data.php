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
        $tournament_id = h($_POST['tournament_id']);
        $event_name = h($_POST['event_name']);
        $type = h($_POST['type']);
        $gender = h($_POST['gender']);
        $capacity = h($_POST['capacity']);
        $target = h($_POST['target']);
        $min_age = h($_POST['min-age']);
        $max_age = h($_POST['max-age']);
        $fee = h($_POST['fee']);

        // パラメーターをバインド
        $sql = 'INSERT INTO event_list (tournament_id, event_name, type, gender, capacity, target, `min-age`, `max-age`, `fee`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('isssisiii', $tournament_id, $event_name, $type, $gender, $capacity, $target, $min_age, $max_age, $fee);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // INSERTが成功した場合の処理
            //新規種目IDの取得
            $event_id = $cms_access->insert_id;
            //ログの追加
            $log_after = $event_name.'/'.$type.'/'.$gender.'/'.$capacity.'人/'.$target.'/'.$min_age.'歳から'.$max_age.'歳まで/'.$fee.'円';
            add_log('new_event', $tournament_id, $event_id , null, $log_after);
            //リダイレクト
            echo "<script>
                    window.onload = function() {
                        const message = '新しい種目が追加されました。';
                        alert(message);
                        const newLocation = '" . home_url('Tournament/View/Event?tournament_id=') . "' + $tournament_id;
                        window.location.href = newLocation;
                    }
                </script>";
        } else {
            // エラーが発生した場合の処理
            error_log("INSERT query failed: " . $stmt->error);
        }

        $stmt->close();
    } else {
        // エラーが発生した場合の処理
    }

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>
