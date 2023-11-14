<?php 
    // check_venue_existance($venue_id, $tournament_id);
    //セキュリティ
    $nonce_id = raquty_nonce() ;
    //大会データ読み込み
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
    //アラート
    alert('OOP_Notice');
    if(isset($_POST['selector-value'])&&$_POST['selector-value']!==''){
        $_SESSION['selector-value'] = $_POST['selector-value'];
        unset($_POST['selector-value']);
    }
    if(isset($_POST['select-again'])){
        if(isset($_SESSION['game_id'])){
            unset($_SESSION['game_id']);
            unset($_POST['select-again']);
        }

    }

    if(isset($_POST['game_id'])){
        $_SESSION['game_id'] = $_POST['game_id'];
        //postの値を未定義の状態にする
        unset($_POST['game_id']);
    }
    if(isset($_POST['court_num'])){
        //もし$_SESSION['game_id']が存在しないなら
        //$_SESSION['court_num']は定義せず、$_POST['court_num']をunsetする
        if(!isset($_SESSION['game_id'])){
            unset($_POST['court_num']);
        }else{
            $_SESSION['court_num'] = $_POST['court_num'];
            //postの値を未定義の状態にする
            unset($_POST['court_num']);
        }        
    }
    if(isset($_SESSION['game_id']) && isset($_SESSION['court_num'])){
        $game_id = $_SESSION['game_id'];
        $court_num = $_SESSION['court_num'];
        $venue_id = h($_GET['venue_id']);
        $tournament_id = h($_GET['tournament_id']);
        $table_name = $tournament_id.'_game_index';
        //game_indexテーブルのcourt_num=$court_numかつvenue_id=$venue_idかつ、status=0もしくは-1でない試合のうち最もstatusが最も大きいものを１つだけ取得する
        $sql = "SELECT * FROM $table_name WHERE court_num = ? AND venue_id = ? AND status != 0 AND status != -1 ORDER BY status DESC LIMIT 1";
        $stmt = $tournament_access->prepare($sql);
        $stmt->bind_param('ii', $court_num, $venue_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows === 0){
            $status = 1;
        }else if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if($row['status']==1){
                //$statusを２にする。
                $status = 2;
            }else if($row['status']==2){
                //$statusを３にする。
                $status = 3;
            }else{
                $status = null;
            }
        }
        if($status != null){
            //game_indexテーブルのcourt_num,venue_id,status全てを更新する
            $sql2 = "UPDATE $table_name SET court_num = ?, venue_id = ?, status = ? WHERE game_id = ?";
            $stmt2 = $tournament_access->prepare($sql2);
            $stmt2->bind_param('iiii', $court_num, $venue_id, $status, $game_id);
            $stmt2->execute();
            $stmt2->close();
        }
        //$_SESSION['game_id']を未定義にする
        unset($_SESSION['game_id']);
        //$_SESSION['court_num']を未定義にする
        unset($_SESSION['court_num']);
    }

    //oopメインコンテンツ読み込み
    require_once(dirname(__FILE__).'/oop_material.php') ;
    //サジェスト関連読み込み
    require_once(dirname(__FILE__).'/suggest_material.php') ;
?>
<!-- suggset関連js -->
<script src="/components/templates/operation_oop/suggest_material.min.js"></script>

<div class="OOP">
    <div class="page_title">
        <div class="title_wrap">
            <button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Operation?tournament_id=').h($_GET['tournament_id']); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?>
        </div>
    </div>
    <div class="wrap">
        <div class="main">
            <!-- ここにオーダオブプレイを表示する -->
            <div class="courts">
            <?php create_court(h($_GET['venue_id']),$nonce_id);?>
            </div>
        </div>  
        <div class="suggest">
            <div class="wrapper">
                <div class="court-select-alert" style="font-size: small;display:none;">&nbsp;&lt;---&nbsp;コートを選択してください</div>
                <form action="" method="post">
                    <input type="hidden" name="selector-value" class="selector-value" value="">
                    <input type="hidden" name="select-again" value="">
                    <button type="submit" id="select-again" style="display:none;">選択解除</button>
                </form>
                <div class="select-wrapper">
                    <select name="search" id="search">
                        <!-- ここにget_all_child_event()の結果＋すべてという選択肢を作る -->
                        <?php
                            if(isset($_SESSION['selector-value']) && $_SESSION['selector-value'] === 'all'){
                                echo '<option value="all" selected>すべて</option>';
                            }else{
                                echo '<option value="all">すべて</option>';
                            }
                        ?>
                        <?php
                            $child_event_list = get_all_child_event($_GET['tournament_id']);
                            if($child_event_list != null){
                                foreach($child_event_list as $child_event){
                                    if(isset($_SESSION['selector-value']) && $_SESSION['selector-value'] === $child_event['child_event_name']){
                                        echo '<option value="'.$child_event['id'].'" selected>'.$child_event['child_event_name'].'</option>';
                                    }else{
                                        echo '<option value="'.$child_event['id'].'">'.$child_event['child_event_name'].'</option>';
                                    }
                                }
                            }
                        ?>
                        <?php
                            if(isset($_SESSION['selector-value']) && $_SESSION['selector-value'] === 'consolation'){
                                echo '<option value="consolation" selected>コンソレーション</option>';
                            }else{
                                echo '<option value="consolation">コンソレーション</option>';
                            }
                        ?>
                    </select>
                </div>
                <?php
                    if(isset($_SESSION['game_id'])){
                        echo '<script>createCourtSelector();</script>';
                    }
                ?>
                <div class="content">
                    <?php
                        if($child_event_list != null){

                            foreach($child_event_list as $child_event){
                                make_base($child_event['id']);
                                $suggest = create_suggest($child_event['id']);
                                if($suggest != null){
                                    foreach($suggest as $game){
                                        if(get_player_name($child_event['id'],$game['draw_id_1'])['user2_name']==null){
                                            //シングルスだと判断する
                                            echo '<div class="game_card event-'.$child_event['id'].'" id="game-'.$game['game_id'].'" onclick="selectSuggest(\''.$game['game_id'].'\');">';
                                                echo '<div class="event_section">';
                                                    echo '<div class="child_event">'.get_child_event($child_event['id'])['child_event_name'].'</div>';
                                                    if(get_child_event($child_event['id'])['event_type']==1){
                                                        echo '<div class="round">'.get_round_name($child_event['id'],$game['round']).'</div>';
                                                    }else if(get_child_event($child_event['id'])['event_type']==2){
                                                        //総当たり戦
                                                        echo '<div class="round">総当たり戦</div>';
                                                    }
                                                echo '</div>';
                                                echo '<div class="player_section" style="display:block">';
                                                    echo '<div class="wrap">';  
                                                        echo '<div class="draw_number">'.$game['draw_id_1'].'</div>';
                                                        echo '<div class="wrap_2">';
                                                            echo '<div class="player">'.get_player_name($child_event['id'],$game['draw_id_1'])['user1_name'].'</div>';
                                                            echo '<div class="belonging">'.get_player_name($child_event['id'],$game['draw_id_1'])['user1_belonging'].'</div>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                    echo '<div class="vs">VS</div>';
                                                    echo '<div class="wrap">';
                                                        echo '<div class="draw_number">'.$game['draw_id_2'].'</div>';
                                                        echo '<div class="wrap_2">';
                                                            echo '<div class="player">'.get_player_name($child_event['id'],$game['draw_id_2'])['user1_name'].'</div>';
                                                            echo '<div class="belonging">'.get_player_name($child_event['id'],$game['draw_id_2'])['user1_belonging'].'</div>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                echo '</div>';
                                                echo '<form id="game_id-is-'.$game['game_id'].'" action="" method="post">';
                                                    echo '<input type="hidden" name="selector-value" class="selector-value" value="">';
                                                    echo '<input type="hidden" name="game_id" id "game_id" value="'.$game['game_id'].'">';
                                                    echo '<input type="submit" id="submit-'.$game['game_id'].'" style="display:none;">';
                                                echo '</form>';
                                            echo '</div>';
                                        }else{
                                            //ダブルスだと判断する
                                            echo '<div class="game_card event-'.$child_event['id'].'" id="game-'.$game['game_id'].'" onclick="selectSuggest(\''.$game['game_id'].'\');">';
                                                echo '<div class="event_section">';
                                                    echo '<div class="child_event">'.get_child_event($child_event['id'])['child_event_name'].'</div>';
                                                    echo '<div class="round">'.get_round_name($child_event['id'],$game['round']).'</div>';
                                                echo '</div>';
                                                echo '<div class="player_section" style="display:block">';
                                                    echo '<div class="wrap">';  
                                                        echo '<div class="draw_number">'.$game['draw_id_1'].'</div>';
                                                        echo '<div class="wrap_2">';
                                                            echo '<div class="player">'.get_player_name($child_event['id'],$game['draw_id_1'])['user1_name'].'・'.get_player_name($child_event['id'],$game['draw_id_1'])['user2_name'].'</div>';
                                                            echo '<div class="belonging">'.get_player_name($child_event['id'],$game['draw_id_1'])['user1_belonging'].'・'.get_player_name($child_event['id'],$game['draw_id_1'])['user2_belonging'].'</div>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                    echo '<div class="vs">VS</div>';
                                                    echo '<div class="wrap">';
                                                        echo '<div class="draw_number">'.$game['draw_id_2'].'</div>';
                                                        echo '<div class="wrap_2">';
                                                            echo '<div class="player">'.get_player_name($child_event['id'],$game['draw_id_2'])['user1_name'].'・'.get_player_name($child_event['id'],$game['draw_id_2'])['user2_name'].'</div>';
                                                            echo '<div class="belonging">'.get_player_name($child_event['id'],$game['draw_id_2'])['user1_belonging'].'・'.get_player_name($child_event['id'],$game['draw_id_2'])['user2_belonging'].'</div>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                echo '</div>';
                                                echo '<form id="game_id-is-'.$game['game_id'].'" action="" method="post">';
                                                    echo '<input type="hidden" name="selector-value" class="selector-value" value="">';
                                                    echo '<input type="hidden" name="game_id" id "game_id" value="'.$game['game_id'].'">';
                                                    echo '<input type="submit" id="submit-'.$game['game_id'].'" style="display:none;">';
                                                echo '</form>';
                                            echo '</div>';
                                        }
                                    }
                                }
                            }
                        }
                        //コンソレーションの試合を表示する
                        $consolation = get_consolation();
                        if($consolation != null){
                            foreach($consolation as $game){
                                //コンソレーションの場合、選手番号はentry_listテーブルのidを使う
                                if(get_consolation_player_name($game['draw_id_1'])['user2_name']==null){
                                    //シングルスだと判断する
                                    echo '<div class="game_card event-consolation" id="game-'.$game['game_id'].'" onclick="selectSuggest(\''.$game['game_id'].'\');">';
                                        echo '<div class="event_section">';
                                            echo '<div class="child_event">コンソレーション</div>';
                                        echo '</div>';
                                        echo '<div class="player_section" style="display:block">';
                                            echo '<div class="wrap">';  
                                                echo '<div class="draw_number">'.$game['draw_id_1'].'</div>';
                                                echo '<div class="wrap_2">';
                                                    echo '<div class="player">'.get_consolation_player_name($game['draw_id_1'])['user1_name'].'</div>';
                                                    echo '<div class="belonging">'.get_consolation_player_name($game['draw_id_1'])['user1_belonging'].'</div>';
                                                echo '</div>';
                                            echo '</div>';
                                            echo '<div class="vs">VS</div>';
                                            echo '<div class="wrap">';
                                                echo '<div class="draw_number">'.$game['draw_id_2'].'</div>';
                                                echo '<div class="wrap_2">';
                                                    echo '<div class="player">'.get_consolation_player_name($game['draw_id_2'])['user1_name'].'</div>';
                                                    echo '<div class="belonging">'.get_consolation_player_name($game['draw_id_2'])['user1_belonging'].'</div>';
                                                echo '</div>';
                                            echo '</div>';
                                        echo '</div>';
                                        echo '<form id="game_id-is-'.$game['game_id'].'" action="" method="post">';
                                            echo '<input type="hidden" name="selector-value" class="selector-value" value="">';
                                            echo '<input type="hidden" name="game_id" id "game_id" value="'.$game['game_id'].'">';
                                            echo '<input type="submit" id="submit-'.$game['game_id'].'" style="display:none;">';
                                        echo '</form>';
                                    echo '</div>';
                                }else{
                                    //ダブルスだと判断する
                                    echo '<div class="game_card event-consolation" id="game-'.$game['game_id'].'" onclick="selectSuggest(\''.$game['game_id'].'\');">';
                                        echo '<div class="event_section">';
                                            echo '<div class="child_event">コンソレーション</div>';
                                        echo '</div>';
                                        echo '<div class="player_section" style="display:block">';
                                            echo '<div class="wrap">';  
                                                echo '<div class="draw_number">'.$game['draw_id_1'].'</div>';
                                                echo '<div class="wrap_2">';
                                                    echo '<div class="player">'.get_consolation_player_name($game['draw_id_1'])['user1_name'].'・'.get_consolation_player_name($game['draw_id_1'])['user2_name'].'</div>';
                                                    echo '<div class="belonging">'.get_consolation_player_name($game['draw_id_1'])['user1_belonging'].'・'.get_consolation_player_name($game['draw_id_1'])['user2_belonging'].'</div>';
                                                echo '</div>';
                                            echo '</div>';
                                            echo '<div class="vs">VS</div>';
                                            echo '<div class="wrap">';
                                                echo '<div class="draw_number">'.$game['draw_id_2'].'</div>';
                                                echo '<div class="wrap_2">';
                                                    echo '<div class="player">'.get_consolation_player_name($game['draw_id_2'])['user1_name'].'・'.get_consolation_player_name($game['draw_id_2'])['user2_name'].'</div>';
                                                    echo '<div class="belonging">'.get_consolation_player_name($game['draw_id_2'])['user1_belonging'].'・'.get_consolation_player_name($game['draw_id_2'])['user2_belonging'].'</div>';
                                                echo '</div>';
                                            echo '</div>';
                                        echo '</div>';
                                        echo '<form id="game_id-is-'.$game['game_id'].'" action="" method="post">';
                                            echo '<input type="hidden" name="selector-value" class="selector-value" value="">';
                                            echo '<input type="hidden" name="game_id" id "game_id" value="'.$game['game_id'].'">';
                                            echo '<input type="submit" id="submit-'.$game['game_id'].'" style="display:none;">';
                                        echo '</form>';
                                    echo '</div>';
                                }
                            }
                        }
                                    
                    ?>
                </div>
                <?php 
                    if(isset($_SESSION['game_id'])){
                        echo '<script>gameFocus("'.$_SESSION['game_id'].'");displaySelectAgain();</script>';
                    }else{
                        echo '<script>displayGame();</script>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<div class="addMatch" id="addMatch"><i class="fas fa-plus"></i></div>
<div class="addMatch_Modal" id="addMatch_Modal" style="display: none;">
    <div class="title">試合カード追加</div>
        <form action="/components/templates/operation_oop/addMatch.php" method="post">
            <div class="wrap">
                <div class="radio_title">カテゴリを選択</div>
                <input type="radio" name="type" id="type_1" value="1" required><label for="type_1">シングルス</label>
                <input type="radio" name="type" id="type_2" value="2"><label for="type_2">ダブルス</label>
            </div>
            <div class="wrap">
                <div class="wrap_2">
                    <div class="entry_data" id="entry_1">
                        <div class="select_player" id="player_1"><?php create_player_option(1);?></div>
                        <div class="select_player" id="player_2" style="display:none;"><?php create_player_option(2);?></div>
                    </div>
                    <div class="vs">VS</div>
                    <div class="entry_data" id="entry_2">
                        <div class="select_player" id="player_3"><?php create_player_option(3);?></div>
                        <div class="select_player" id="player_4" style="display:none;"><?php create_player_option(4);?></div>
                    </div>
                </div>
            </div>
            <div class="submit_wrap">
                <input type="hidden" name="tournament_id" value="<?=h($_GET['tournament_id']);?>">
                <input type="hidden" name="venue_id" value="<?=h($_GET['venue_id']);?>">
                <input type="hidden" name="raquty_nonce" value="<?=$nonce_id;?>">
                <button id="addMatch_Submit">カード追加</button>
            </div>
        </form>
</div>
<div id="backScreen"  style="display: none;"></div>


<?php

    function get_player($entry_id){
        global $tournament_access;
        $table_name = h($_GET['tournament_id']).'_entrylist';
        $sql = "SELECT 	* FROM $table_name WHERE id = ?";
        $stmt = $tournament_access->prepare($sql);
        $stmt->bind_param('i', $entry_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row;
        }else{
            return false;
        }
    }

    function get_draw($child_event_id) {
        //child_event_idに紐づく情報を変数に書くのできる関数
        $sql = "SELECT * FROM child_event_list WHERE id = ? AND status = ?";
        $status = 1;
        global $cms_access;
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param("ii",$child_event_id,$status);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row;
        }else{
            return null;    }
    }

    //event_idに紐づく情報を変数に書くのできる関数
    function get_event_data($event_id) {
        $sql = "SELECT * FROM event_list WHERE event_id = ?";
        global $cms_access; 
        $stmt = $cms_access->prepare($sql);
        $stmt->bind_param("i", $event_id); 
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row; 
        } else {
            return null; 
        }
    }

    //フッター呼び出し(フッター → /body → /html まで)
    require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>