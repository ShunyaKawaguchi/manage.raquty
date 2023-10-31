<?php 
alert('operation_err');
if(!check_tournament_existance( h($_GET['tournament_id']) )){
    $_SESSION['operation_err']='トーナメントが選択されていません。';
    header("Location: " . home_url('Tournament/List'));
    exit;
}
?>

<div class="venue-selector">
    <?php
        get_tournament_venue($_GET['tournament_id']);
    ?>
</div>

<?php 
function get_tournament_venue($tournament_id) {
    $sql = "SELECT template_id FROM venues WHERE tournament_id = ?";
    global $cms_access,$organizations_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sql2 = "SELECT venue_name, template_id FROM venues WHERE template_id = ?";
            $stmt2 = $organizations_access->prepare($sql2);
            $stmt2->bind_param("i", $row['template_id']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows === 1) {
                $row = $result2->fetch_assoc();
                // 1. すべてのGETパラメータを取得
                $allParams = $_GET;

                // 2. 特定のキーのGETパラメータを除外
                $excludeKey = 'venue_id'; // 除外したいキーを指定
                if (isset($allParams[$excludeKey])) {
                    unset($allParams[$excludeKey]);
                }
                

                ?>

                <div class="single_menu">
                    <a href="<?=home_url('Tournament/View/Operation').'?venue_id='.$row['template_id'].'&'.http_build_query($allParams)?>"><?= $row['venue_name']?></a>
                
                </div>

<?php        }
    }

    $stmt->close();
}
}