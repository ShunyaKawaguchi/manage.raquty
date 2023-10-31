<?php
require_once(dirname(__FILE__) . '/../../../manage-raquty-cms-system/connect-databese.php');
require_once(dirname(__FILE__) . '/../../../components/common/global-function.php');
session_start();

//試合情報をデータベースに登録
$courtNum = $_POST['court-num'];
$round = $_POST['round'];
$category = $_POST['category'];
$playerId = $_POST['player__name'];
$playerId2 = $_POST['player__name2'];
$status = $_POST['data-player-number'];

$confirm = "SELECT * FROM `1_game_index` WHERE (`draw_id_1` = ? OR `draw_id_1` = ? OR `draw_id_2` = ? OR `draw_id_2` = ?) AND `round` = ?";
$stmt1 = $tournament_access->prepare($confirm);
$stmt1->bind_param("iiiis", $playerId, $playerId2, $playerId, $playerId2, $round);
$stmt1->execute();
$result = $stmt1->get_result();
$stmt1->close();

if ($result->num_rows > 0  && $round != 'コンソレ') {
    $_SESSION['error'] = "選手はすでに試合に登録されています。";
    $tournament_access->close(); // データベース接続を閉じる
    sleep(0.85);
    header("Location: ". home_url('Operation/OOP'));
    exit();
} elseif ($playerId == $playerId2) {
    $_SESSION['same'] = "この試合を登録することはできません。";
    $tournament_access->close(); // データベース接続を閉じる
    sleep(0.85);
    header("Location: ". home_url('Operation/OOP'));
    exit();
} else {
    $sql = "INSERT INTO `1_game_index`(`court_num`, `round`, `category`, `draw_id_1`, `draw_id_2`, `status`) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt2 = $tournament_access->prepare($sql);
    $stmt2->bind_param("issiii", $courtNum, $round, $category, $playerId, $playerId2, $status);

    if ($stmt2->execute()) {
        $stmt2->close(); // ステートメントを閉じる
        $tournament_access->close(); // データベース接続を閉じる
        unset($_SESSION['error']);
        sleep(0.85);
        header("Location: ". home_url('Operation/OOP'));
        exit();
    } else {
        echo "データ挿入エラー: " . $stmt2->error;
    }
}
