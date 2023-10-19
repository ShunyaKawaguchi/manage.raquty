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
if ($_SESSION['nonce_id'] == $_POST['raquty_nonce'] && $_SESSION['level'] === 1) {
    // 認証エラーメッセージを初期化
    unset($_SESSION['delete_venue_pw_error']);
    // パスワード認証
    if (raquty_password_authentication()) {
        delete_venue_template($_POST['template_id']);
    } else {
        // 認証エラーエッセー時を登録
        $_SESSION['delete_venue_pw_error'] = 1;
        //リダイレクト
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
            const templateId = " . $_POST['template_id'] . ";
            const form = document.createElement('form');
            form.method = 'post';
            form.action ='" . home_url('Tournament/Venue/Delete') . "';
            
            const templateIdInput = document.createElement('input');
            templateIdInput.type = 'hidden';
            templateIdInput.name = 'template_id';
            templateIdInput.value = templateId;
            
            form.appendChild(templateIdInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    </script>";
    
    }
}else{
    header("Location: " . home_url('Tournament/Venue'));   

}


function delete_venue_template($template_id){
    //該当の行の削除を行う一連の処理
    $sql = 'DELETE FROM venues WHERE template_id = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $template_id);
        
        if ($stmt->execute()) {
            // 削除が成功した場合の処理を追加
            delete_venue_data($template_id);
            //ログ追加
            $log_before = 'template_id='.$template_id.'/status:All_Clear';
            add_log('delete_venue_template' , null, null , $log_before ,null);    
            delete_tournament_venue($template_id);

        } else {
            // エラーが発生した場合の処理を追加
        }
        
        $stmt->close();
    } else {
        // プリペアドステートメントの準備に失敗した場合の処理を追加
    }
}

//大会に紐づけられている会場を削除
function delete_venue_data($template_id) {
    // 該当の行の数を確認するクエリ
    $check_sql = 'SELECT COUNT(*) FROM venues WHERE template_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($check_sql);
    
    if ($stmt) {
        $stmt->bind_param('i', $template_id);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            $row_count = $stmt->num_rows;
            $stmt->close();
            
            // 該当の行が1行以上ある場合に削除を実行
            if ($row_count > 0) {
                $delete_sql = 'DELETE FROM venues WHERE template_id = ?';
                $stmt_delete = $cms_access->prepare($delete_sql);
                
                if ($stmt_delete) {
                    $stmt_delete->bind_param('i', $template_id);
                    
                    if ($stmt_delete->execute()) {
                        // 削除が成功した場合の処理を追加
                    
                    } else {
                        // エラーが発生した場合の処理を追加
                    }
                    
                    $stmt_delete->close();
                } else {
                    // プリペアドステートメントの準備に失敗した場合の処理を追加
                }
            } else {
                // 該当の行が存在しない場合の処理を追加
            }
        } else {
            // エラーが発生した場合の処理を追加
        }
    } else {
        // プリペアドステートメントの準備に失敗した場合の処理を追加
    }
}

function delete_tournament_venue($venue_id){
    $sql = 'SELECT tournament_id FROM tournament WHERE group_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);

    $stmt->bind_param("i", $_SESSION['group_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tournament_id = $row['tournament_id'];
        $table_name = $tournament_id.'_venues';
        
        $sql = 'DELETE FROM ' . $table_name . ' WHERE venue_id = ?';
        global $tournament_access;
        $stmt = $tournament_access->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('i', $venue_id);
            
            if ($stmt->execute()) { 

                //リダイレクト
                echo "<script>
                        alert('会場テンプレートを削除しました。');
                        window.location.href = '" . home_url('Tournament/Venue') . "';
                    </script>";
            } else {
                // エラーが発生した場合の処理を追加
            }
            
            $stmt->close();
        } else {
            // プリペアドステートメントの準備に失敗した場合の処理を追加
        }
        }

        $stmt->close();
}