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
    unset($_SESSION['delete_tournament_pw_error']);
    // パスワード認証
    if (raquty_password_authentication()) {
        delete_tournament_template($_POST['tournament_id']);
    } else {
        // 認証エラーエッセー時を登録
        $_SESSION['delete_tournament_pw_error'] = 1;
        //リダイレクト
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const TournamentId = " . $_POST['tournament_id'] . ";
                    
                    const form = document.createElement('form');
                    form.method = 'get';
                    form.action ='" . home_url('Tournament/View/Delete') . "';
            
                    const tournamentIdInput = document.createElement('input');
                    tournamentIdInput.type = 'hidden';
                    tournamentIdInput.name = 'tournament_id';
                    tournamentIdInput.value = TournamentId;
            
                    form.appendChild(tournamentIdInput);
            
                    document.body.appendChild(form);
                    form.submit();
                });
            </script>";
    }
} else {
    header("Location: " . home_url('Tournament/List'));   
}

function delete_tournament_template($tournament_id) {
    global $cms_access,$tournament_access;

    // 削除するテーブル名を指定
    $table_name = $tournament_id . "_entrylist";
    $table_name2 = $tournament_id . "_venues";


    // 各SQLステートメントを個別に定義
    $sql1 = "DELETE FROM event_list WHERE tournament_id = ?";
    $sql2 = "DELETE FROM tournament WHERE tournament_id = ?";
    $sql3 = "DELETE FROM document_path WHERE tournament_id = ?";
    $sql4 = "DELETE FROM tournament_topics WHERE tournament_id = ?";
    $sql5 = "DELETE FROM venues WHERE tournament_id = ?";
    $sql6 = "DROP TABLE $table_name";
    $sql7 = "DROP TABLE $table_name2";

    // プリペアードステートメントを作成
    $stmt1 = $cms_access->prepare($sql1);
    $stmt2 = $cms_access->prepare($sql2);
    $stmt3 = $cms_access->prepare($sql3);
    $stmt4 = $cms_access->prepare($sql4);
    $stmt5 = $cms_access->prepare($sql5);
    $stmt6 = $tournament_access->prepare($sql6);
    $stmt7 = $tournament_access->prepare($sql7);


    // パラメータをバインド
    $stmt1->bind_param("i", $tournament_id);
    $stmt2->bind_param("i", $tournament_id);
    $stmt3->bind_param("i", $tournament_id);
    $stmt4->bind_param("i", $tournament_id);
    $stmt5->bind_param("i", $tournament_id);


    // 各ステートメントを実行
    $stmt1->execute();
    $stmt2->execute();
    $stmt3->execute();
    $stmt4->execute();
    $stmt5->execute();

    // ステートメントをクローズ
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
    $stmt4->close();
    $stmt5->close();

    // テーブルを削除
    $stmt6->execute();
    $stmt6->close();
    $stmt7->execute();
    $stmt7->close();

    // 削除が成功した場合の処理を追加
    //ログを追加
    add_log('delete_tournament' , $tournament_id, null , null , null);
    //リダイレクト
    echo "<script>
            alert('大会を削除しました。');
            window.location.href = '" . home_url('Tournament/List') . "';
         </script>";
}
?>
