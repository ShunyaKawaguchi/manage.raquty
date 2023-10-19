<?php
if (empty($_POST['newtournament']) || $_POST['newtournament'] !== 'newtournament') {
    header("Location: " . home_url('404'));
    exit;
}

// セッションを手動で開始
session_start();

//データベース認証
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/connect-databese.php');
//共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../../../components/common/global-function.php');
//ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/../../../manage-raquty-cms-system/have_user_id.php');

// 新規で大会の行をデータベースに作成
$insertQuery = "INSERT INTO tournament (tournament_name, group_id, post_date, post_modified, post_status) VALUES (?, ?, NOW(), NOW(), 'draft')";
$stmt = $cms_access->prepare($insertQuery);

if ($stmt) {
    $tournamentName = "名称未設定の大会";
    $groupId = $_SESSION['group_id'];

    $stmt->bind_param("si", $tournamentName, $groupId);

    if ($stmt->execute()) {
        $tournament_id = $cms_access->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];

        // エントリーテーブル作成
        $table_name = $tournament_id . "_entrylist";
        $sql = "CREATE TABLE $table_name (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            draw_id INT(11) NULL,
            user1_id INT(11),
            user1_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            user1_belonging VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            user2_id INT(11),
            user2_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
            user2_belonging VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
            event_id INT(11) NOT NULL
        )";

        //会場データテーブルを作成
        if ($tournament_access->query($sql) === TRUE) {
            $table_name2 = $tournament_id . "_venues";
            $sql2 = "CREATE TABLE $table_name2 (
                id bigint(20) AUTO_INCREMENT PRIMARY KEY,
                event_date date NOT NULL,
                event_id bigint(20) NOT NULL,
                venue_id bigint(20) NOT NULL
            )";

            // クエリを実行して会場テーブルを作成
            if ($tournament_access->query($sql2) === TRUE) {
                //ログ
                add_log('create_tournament' , $tournament_id, null , null, null);

                header("Location: " . home_url('/Tournament/View?tournament_id=' . $tournament_id));
                exit; // リダイレクトした後、スクリプトを終了することが重要です
            } else {
                echo "会場テーブルの作成に失敗しました: " . $tournament_access->error;
            }
        } else {
            echo "エントリーテーブルの作成に失敗しました: " . $tournament_access->error;
        }
    } else {
        echo "トーナメントの挿入に失敗しました: " . $stmt->error;
    }
    $stmt->close();
} else {
    die('データベース接続エラーが発生しました。管理者に連絡してください。');
}

// データベース接続解除
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/disconnect-database.php');
?>
