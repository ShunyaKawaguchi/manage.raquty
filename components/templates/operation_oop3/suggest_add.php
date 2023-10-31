<?php
// データベースに接続
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');

// ドロップダウンメニューと試合IDから値を受け取る
$court_num = $_POST['oop'];
$game_id = $_POST['add'];

// テーブルを更新するSQL文を準備
$sql = "UPDATE 1_game_index SET court_num = ?, status = 1 WHERE game_id = ?";

// SQL文を実行する準備
$stmt2 = $tournament_access->prepare($sql);

// パラメータをバインド
$stmt2->bind_param("ii", $court_num, $game_id);

// SQL文を実行
if ($stmt2->execute()) {
    $stmt2->close(); // ステートメントを閉じる
    $tournament_access->close(); // データベース接続を閉じる
    unset($_SESSION['error']);
    sleep(0.85);
    header("Location:" .home_url('Operation/OOP'));
    exit();
} else {
  echo "エラー: " . $stmt2->error;
}
?>
