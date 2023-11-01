<?php
function create_court($venue_id){
    $sql = "SELECT venue_name , court_name FROM venues WHERE template_id = ?";
    global $organizations_access; 
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $venue_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $roop_number = 1;
    if ($result->num_rows > 0) { 
        $row = $result->fetch_assoc();

        //会場名を表示
?>
        <div class="venue_name"><?=$row['venue_name'];?></div>
        <!-- oop関連js -->
        <script src="/components/templates/operation_oop/oop_material.min.js"></script>

        <div class="court_wrap">
<?php   //コートを1面ずつ出力する
        $courts = explode(",", $row['court_name']); 
        foreach( $courts as $court ) { ?>
            <div class="court">
                <div class="court_name"><?=$court?></div>
                    <div class="oop_card_wrap">
                <?php   //oopメインコンテンツ（ネクネクまで表示）
                        for($i= 1;$i<=3;$i++) { ?>
                            <div class="oop_card">
                                <?php create_game_card($roop_number,$i);?>
                            </div>
                <?php   } //for文ここで終わり ?>
                    </div>
            </div>
<?php       $roop_number++;
        } //foreach文ここで終わり ?> 
        </div>
<?php
    } else {
        //会場情報が取得できなかった→不正な遷移か何か→リダイレクト
    }
}

function create_game_card($roop_number,$roop_number2){
    $id = $roop_number.$roop_number2;
    ?>
    <div class="game_card">

        <div class="cxl" id ="<?=$id;?>"><i class="fas fa-times"></i></div>
        <div class="result" id ="<?=$id;?>"><i class="fas fa-check"></i></div>
        <?php if($roop_number2 > 1):?>
        <div class="up"><i class="arrow-icon fas fa-arrow-up"></i></div>
        <div class="change"><i class="arrow-icon fas fa-chevron-left"></i></div>
        <?php endif;?>

        <div class="event_section">
            <div class="child_event">【11/4】男子シングルス</div>
            <div class="round">１回戦</div>
        </div>
        <div class="player_section" style="display:block;" id ="<?=$id;?>">
            <div class="wrap">
                <div class="draw_number">1</div>
                <div class="wrap_2">
                    <div class="player">川口駿也</div>
                    <div class="belonging">（仙台第三）</div>
                </div>
            </div>
            <div class="vs">VS</div>
            <div class="wrap">
                <div class="draw_number">1</div>
                <div class="wrap_2">
                    <div class="player">川口駿也</div>
                    <div class="belonging">（仙台第三）</div>
                </div>
            </div>
        </div>
        <div class="menu_section" style="display:none;" id ="<?=$id;?>">
            <div class="wrap" id ="<?='win_1_'.$id;?>">
                <div class="winner">
                    <input type="radio" name="winner" id ="<?='win_1_'.$id;?>">
                </div>
                <div class="wrap_2">
                    <div class="player">川口駿也</div>
                    <div class="belonging">（仙台第三）</div>
                </div>
                <div class="wrap_3">
                    <input type="text">
                </div>
            </div>
            <div class="wrap" id ="<?='win_2_'.$id;?>">
                <div class="winner">
                    <input type="radio" name="winner" id ="<?='win_2_'.$id;?>">
                </div>
                <div class="wrap_2">
                    <div class="player">川口駿也</div>
                    <div class="belonging">（仙台第三）</div>
                </div>
                <div class="wrap_3">
                    <input type="text">
                </div>
            </div>
            <div class="wrap">
                <div class="tiebreak">
                    <div class="tb_wrap">
                        <input type="checkbox" name="tiebreak">
                        <span>タイブレーク</span>
                    </div>
                    <div class="submit_wrap">
                        <input type="submit" class="back" value="戻る" data-target-id="<?=$id;?>">
                        <input type="submit" value="登録">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}