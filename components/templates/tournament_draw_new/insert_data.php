<?php 
// セッションの開始
session_start();

// データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
// 共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
// ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

if(!raquty_nonce_check(2)){
    header("Location: " . home_url('Tournament/List'));
}else{
    if(isset($_POST['NewChild'])){
        $tournament_id = $_POST['tournament_id'];
        $event_id = $_POST['event_id'];
        $event_type = $_POST['type'];
        $child_event_name = $_POST['child_event_name'];
        $capacity = $_POST['capacity'];
        $exponent = log($capacity, 2);
        $upper_exponent = ceil($exponent);
        $upper_capacity = pow(2, $upper_exponent);
        $status = 0;

        //総当たりだった時にDBに入れる人数を変更
        if($event_type == 2){
            $upper_capacity = $capacity;
        }



        $sql = 'INSERT INTO child_event_list (tournament_id, event_id, child_event_name,capacity , event_type, status) VALUES (? , ? , ?, ?, ?, ?)';
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param('iisiii', $tournament_id, $event_id, $child_event_name, $upper_capacity ,$event_type, $status);

        // クエリの実行とエラーハンドリング
        if ($stmt->execute()) {
            // INSERTが成功した場合の処理
            //新規子種目IDの取得
            $child_event_id = $cms_access->insert_id;
            //ログの追加
            $log_after = 'child_event_id:'.$child_event_id.'/child_event_name:'.$child_event_name.'/capacity:'.$upper_capacity.'/type:'.$event_type;
            add_log('new_child_event', $tournament_id, $event_id , null, $log_after);
            //リダイレクト
            $_SESSION['new_child_event'] = 'ドローを新規で作成しました。作成完了後、公開状況を「公開中」に変更すると、選手も閲覧できるようになります。';
            $auth = generateRandomString();
            $_SESSION['auth'] = $auth;
            header("Location: " . home_url('Tournament/View/Draw/Edit?tournament_id=').$tournament_id.'&child_event_id='.$child_event_id.'&auth='.$auth);
        }else{
            header("Location: " . home_url('Tournament/List'));
        }
        $stmt->close();
    } else {
        // エラーが発生した場合の処理
    }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
