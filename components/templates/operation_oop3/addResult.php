<?php
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');

//試合結果をデータベースに登録
$resultScore1 = $_POST['result__score1'];
$resultScore2 = $_POST['result__score2'];
$tieBreak = $_POST['tiebreak'];
$courtNum = $_POST['court-num'];
$status = $_POST['data-player-number'];

// プリペアドステートメントを作成
$sql1 = "SELECT * FROM 1_game_index WHERE court_num = ? AND status = ?";
$stmt1 = $tournament_access->prepare($sql1);
if(!$stmt1) {
    echo "データベースエラー: " . $tournament_access->error;
}

// パラメーターをバインド
$stmt1->bind_param("ii", $courtNum, $status);

// SQLクエリを実行
$stmt1->execute();

// 結果を取得
$result = $stmt1->get_result();
if ($result === false) {
  echo "結果の取得エラー: " . $stmt1->error;
} else {
  // 結果を出力
  while ($row = $result->fetch_assoc()) {
    $gameId = $row['game_id'];
    if($resultScore1 > $resultScore2) {
      $winnerId = $row['draw_id_1'];
    } else {
      $winnerId = $row['draw_id_2'];
    }
  }
}

// プリペアドステートメントを作成
$sql2 = "INSERT INTO `1_result`(`game_id`, `score1`, `score2`, `tiebreak`, `winner_id`) VALUES (?, ?, ?, ?, ?)";
$stmt2 = $tournament_access->prepare($sql2);
if(!$stmt2) {
    echo "データベースエラー: " . $tournament_access->error;
}

// パラメーターをバインド
$stmt2->bind_param("iiiii", $gameId, $resultScore1, $resultScore2, $tieBreak, $winnerId);

// SQLクエリを実行
if ($stmt2->execute()) {
  $sql3 = "UPDATE 1_game_index SET status = status - 1 WHERE status >= $status AND court_num = $courtNum";
  if ($tournament_access->query($sql3) !== TRUE) {
    echo  "データ更新エラー: " . $tournament_access->error;
  }
  $stmt2->close(); // ステートメントを閉じる
  $stmt1->close(); // ステートメントを閉じる
  $tournament_access->close(); // データベース接続を閉じる
  sleep(0.85);
  header("Location: ". home_url('Operation/OOP'));
  exit();
} else {
    echo "データ挿入エラー: " . $stmt2->error;
}