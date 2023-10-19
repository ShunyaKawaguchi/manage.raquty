<?php
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

// 会場テンプレートをDBに格納
if($_SESSION['nonce_id']==$_POST['raquty_nonce']){

    if(isset($_SESSION['group_id'])){
        $venue_name = h($_POST['venue_name']);
        $venue_address = h($_POST['venue_address']);
        $venue_map = h($_POST['venue_map']);
        $template_id = h($_POST['template_id']);

        $sql = 'UPDATE venues SET venue_name = ?, venue_address = ?, venue_map = ? WHERE template_id = ?';
        $stmt = $organizations_access->prepare($sql);

        // パラメーターをバインド
        $stmt->bind_param('sssi', $venue_name, $venue_address, $venue_map, $template_id);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // UPDATEが成功した場合の処理を追加

            //コート情報を更新+ログ追加
            update_venue_detail($venue_name,$venue_address,$venue_map);

            //リダイレクト
            echo "<script>
                        window.onload = function() {
                            const venueName = '" . $venue_name . "';
                            const message = '会場テンプレート「' + venueName + '」の更新が完了しました。';
                            alert(message);
                            const newLocation = '" . home_url('Tournament/Venue/View?venue_id=') . "' + " . $template_id . ";
                            window.location.href = newLocation;
                        }
                    </script>";

            } else {
                // エラーが発生した場合の処理を追加
            }

            $stmt->close();

        } else {
            // エラーが発生した場合の処理を追加
        }
}

// データベース接続解除
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/disconnect-database.php');

function update_venue_detail($venue_name,$venue_address,$venue_map){
    $venue_data = check_venue_existance( h($_POST['template_id']) );
    
    if($venue_data['court_number'] == $_POST['number_of_court']){
        //コート面数が変更されない

        //コート名
        $court_names = implode(",", $_POST['court_name']);

        //SQL
        $sql = 'UPDATE venues SET court_name = ? WHERE template_id = ?';
        global $organizations_access;
        $stmt = $organizations_access->prepare($sql);

        // パラメーターをバインド
        $stmt->bind_param('si', $court_names, $_POST['template_id']);

        if ($stmt->execute()) {
            //会場情報変更の全てのログを追加
            $log_before = 'template_id='.$venue_data['template_id'].'/venue_name:'.$venue_data['venue_name'].'/venue_adderss:'.$venue_data['venue_address'].'/venue_map:'.$venue_data['venue_map'].'/court_number:'.$venue_data['court_number'].'/court_name:'.$venue_data['court_name'];
            $log_after = 'template_id='.$venue_data['template_id'].'/venue_name:'.$venue_name.'/venue_adderss:'.$venue_address.'/venue_map:'.$venue_map.'/court_number:'.h($_POST['number_of_court']).'/court_name:'.$court_names;
            add_log('update_venue' , null, null , $log_before, $log_after);
        }

        $stmt->close();

    }else{
        //コート面数が変更される

        //SQL
        $sql = 'UPDATE venues SET court_number = ? , court_name = ? WHERE template_id = ?';
        global $organizations_access;
        $stmt = $organizations_access->prepare($sql);
        $court_number = h($_POST['number_of_court']);
        $court_names = '';

        $court_names = array(); // 配列として初期化

        for ($i = 1; $i <= $court_number; $i++) {
            $court_names[] = $i . '番コート'; 
        }
        
        $court_names_str = implode(',', $court_names); 
        
        // パラメーターをバインド
        $stmt->bind_param('isi', $_POST['number_of_court'], $court_names_str, $_POST['template_id']);
        
        if ($stmt->execute()) {
            //会場情報変更の全てのログを追加
            $log_before = 'template_id='.$venue_data['template_id'].'/venue_name:'.$venue_data['venue_name'].'/venue_adderss:'.$venue_data['venue_address'].'/venue_map:'.$venue_data['venue_map'].'/court_number:'.$venue_data['court_number'].'/court_name:'.$venue_data['court_name'];
            $log_after = 'template_id='.$venue_data['template_id'].'/venue_name:'.$venue_name.'/venue_adderss:'.$venue_address.'/venue_map:'.$venue_map.'/court_number:'.h($_POST['number_of_court']).'/court_name:'.$court_names_str;
            add_log('update_venue' , null, null , $log_before, $log_after);
        }


        $stmt->close();
    }
}
