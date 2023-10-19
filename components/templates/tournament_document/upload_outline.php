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
    // アップロードされたファイルを保存するディレクトリを指定
    $upload_dir = '../../../files/document/outline/';
    $upload_dir_db = 'files/document/outline/';
    // 保存するファイル名を決定（ユニークなランダムな名前にすることが一般的） 
    $file_name = uniqid() . '.pdf';
    $file_path =$upload_dir.$file_name;
    $file_path_db =$upload_dir_db.$file_name;


    // ファイルをサーバーに保存する
    if(move_uploaded_file($_FILES['outline']['tmp_name'], $file_path)) {
        if(get_document_path_tf($tournament_id, 'outline')){
            $before_path = get_document_path_return(h($_POST['tournament_id']), 'outline');
            unlink('../../../'.$before_path['document_path']);
            $document_type = 'outline';
            $insert_query = "UPDATE document_path SET group_id = ? , tournament_id = ? , document_type = ? , document_path = ? WHERE document_id = ?";
            $stmt = $cms_access->prepare($insert_query);
            if ($stmt) {
                $stmt->bind_param("iissi",$_SESSION['group_id'] , $tournament_id , $document_type , $file_path_db , $before_path['document_id']);
                if ($stmt->execute()) {
                    //データ更新完了
                    //ログ挿入
                    add_log('upload_outline' , $tournament_id, null , $before_path['document_path'] , $file_path_db);
                    //リダイレクト
                    echo '<script>alert("大会要綱のアップロードが完了しました。");</script>';
                    ?>
                    <script> window.location.href = "<?= home_url('Tournament/View/Document?tournament_id=').$tournament_id ?>";</script>'; 
        
                <?php
                }  
            }      
        }else{
            $insert_query = "INSERT INTO document_path (group_id, tournament_id, document_type, document_path) VALUES (?, ?, ?, ?)";
            $stmt = $cms_access->prepare($insert_query);

            if ($stmt) {
                $document_type = 'outline'; 
                $stmt->bind_param("iiss", $_SESSION['group_id'], $tournament_id, $document_type, $file_path_db);

                if ($stmt->execute()) {
                    //ログ挿入
                    add_log('upload_outline' , $tournament_id, null , null, $file_path_db);
                    //リダイレクト
                    echo '<script>alert("大会要綱のアップロードが完了しました。");</script>';?>
                    <script> window.location.href = "<?= home_url('Tournament/View/Document?tournament_id=').$tournament_id ?>";</script>'; 
                    <?php

                } else {
                    echo '<script>alert("データの挿入に失敗しました。");</script>';?>
                    <script> window.location.href = "<?= home_url('Tournament/View/Document?tournament_id=').$tournament_id ?>";</script>'; 
                    <?php

                }
            } else {
                echo '<script>alert("ステートメントの準備に失敗しました。");</script>';?>
                    <script> window.location.href = "<?= home_url('Tournament/View/Document?tournament_id=').$tournament_id ?>";</script>'; 
                    <?php
            }

        }

    }
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>