<?php
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');

//試合情報をデータベースに登録
$courtNum = $_POST['court-num'];
$status = $_POST['data-player-number'];

// プリペアドステートメントを作成
$sql = "DELETE FROM `1_game_index` WHERE `court_num` = ? AND `status` = ?";
$stmt = $tournament_access->prepare($sql);
if(!$stmt) {
    echo "データベースエラー: " . $tournament_access->error;
}

// パラメーターをバインド
$stmt->bind_param("ii", $courtNum, $status);

// SQLクエリを実行
if ($stmt->execute()) {
  $sql2 = "UPDATE 1_game_index SET status = status - 1 WHERE status >= $status AND court_num = $courtNum";
  if(!$tournament_access->query($sql2)) {
    echo "データベースエラー: " . $tournament_access->error;
  }
  $stmt->close(); // ステートメントを閉じる
  $tournament_access->close(); // データベース接続を閉じる
  sleep(0.85);
  header("Location: ". home_url('Operation/OOP'));
  exit();
} else {
    echo "データ挿入エラー: " . $stmt->error;
}

?>