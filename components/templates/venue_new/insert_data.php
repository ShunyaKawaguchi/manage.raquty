<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

// 会場テンプレートをDBに格納
if($_SESSION['nonce_id']==$_POST['raquty_nonce']){

    if(isset($_SESSION['group_id'])){
        $group_id = $_SESSION['group_id'];
        $venue_name = h($_POST['venue_name']);
        $venue_address = h($_POST['venue_address']);
        $venue_map = h($_POST['venue_map']); 
        $court_number = h($_POST['number_of_court']);
        $court_names = '';

        for ($i = 1; $i <= $court_number; $i++) {
            $court_names .= $i . '番コート';
            if ($i < $court_number) {
                $court_names .= ',';
            }
        }

        $sql = 'INSERT INTO venues (group_id, venue_name, venue_address, venue_map, court_number, court_name) VALUES (?, ?, ?, ?,?,?)';
        $stmt = $organizations_access->prepare($sql);

        // パラメーターをバインド
        $stmt->bind_param('isssis', $group_id, $venue_name, $venue_address, $venue_map, $court_number, $court_names);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            global $organizations_access;
            $template_id = $organizations_access->insert_id;

            //ログ追加
            $log_after = 'template_id='.$template_id.'/venue_name:'.$venue_name.'/venue_adderss:'.$venue_address.'/venue_map:'.$venue_map.'/court_number:'.h($_POST['number_of_court']).'/court_name:'.$court_names;
            add_log('new_venue' , null, null , null ,$log_after);

            //リダイレクト
            echo "<script>
                    window.addEventListener('load', function() {
                        const venueName = '" . $venue_name . "';
                        const message = '会場テンプレート「' + venueName + '」の登録が完了しました。';
                        alert(message);
                        const newLocation = '" . home_url('Tournament/Venue/View?venue_id=') . "' + " . $template_id . ";
                        window.location.href = newLocation;
                    });
                </script>";

        }
         else {
            // エラーが発生した場合の処理
        }
        
        $stmt->close();
    } else {
    }
}

// データベース接続解除
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/disconnect-database.php');
?>
