<?php
    // データベースに$_GET['tournament_id']_game_indexが存在するか確認
    initialProcessing();
    //ヘッダー呼び出し
    // require_once(dirname(__FILE__).'/../../common/structure/header/header.php') ;
    // require_once(dirname(__FILE__).'/../../common/structure/menu_bar/menu_bar.php') ;
    if($is_start_tournament){
        $query = "SELECT * FROM venues WHERE template_id = ".$venue."";
        global $organizations_access;
        $stmt = $organizations_access -> prepare($query);
        if($stmt ===  false){
            die("プリペアードステートメントの準備に失敗しました。");
        }
        if($stmt->execute() === false){
            die("クエリの実行に失敗しました。");
        }
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $venue_name = $row['venue_name'];
        }else{
            $venue_name = $venue;
        }
    }else{
        echo '<div style="margin-left:5vw;margin-right:5vw;"><strong style="color:red;">注）まだ大会の開催期間ではありません。<br>大会の運営が開催されたタイミングで閲覧できるようになります。</strong></div>';
    }
    if(!isset($_GET['event_id'])){
        echo '<script>window.location.href = "'.home_url('OOP/Incorrect_access').'";</script>';
    }
?>
<div class="main">
    <div style="display: block;"><a class="history-back" href="<?php echo home_url('Operation').'?'.http_build_query($_GET); ?>">&nbsp;&lt;&lt;&nbsp;&nbsp;大会当日トップへ戻る</a></div>
    <div class="tournament-name"><?php echo getTournamentInformation($tournament_id); ?></div>
    <div class="content">
        <div class="event-selector">
            <!-- イベントをここで選択する -->
            <?php 
                // foreach ($court_list as $court) {
                //     if($court == $_GET['court_num']){
                //         echo '<a class="court selected" href="'.home_url('OOP/OOP').'?'.$newQuery.'&court_num='.$court.'">'.$court.'</a>';
                //     }else{
                //         echo '<a class="court" href="'.home_url('OOP/OOP').'?'.$newQuery.'&court_num='.$court.'">'.$court.'</a>';
                //     }   
                // }
                // 除外したいGETパラメータの名前
                $excludeParam = 'event_id';

                // 現在のGETパラメータを取得
                $currentParams = $_GET;

                // 除外したいGETパラメータが存在すれば削除
                if (isset($currentParams[$excludeParam])) {
                    unset($currentParams[$excludeParam]);
                }

                // 新しいGETパラメータを生成
                $newQuery = http_build_query($currentParams);

                $event_list = getTournamentEvent($tournament_id);
                if($event_list != 'error'){
                    foreach($event_list as $event){
                        if($event['event_id']==$_GET['event_id']){
                            echo '<a class="event selected" href="'.home_url('Operation/Tournament').'?'.$newQuery.'&event_id='.$event['event_id'].'">'.$event['event_name'].'</a>';
                        }else{
                            echo '<a class="event" href="'.home_url('Operation/Tournament').'?'.$newQuery.'&event_id='.$event['event_id'].'">'.$event['event_name'].'</a>';
                        }
                    }
                }else{
                    echo '<div class="event">'.$event_list.'</div>';
                }
            ?>
        </div>
        <div class="last-update"></div>
        <div class="canvas-container"><canvas id="tournament"></canvas></div>
    </div>
</div>
<script>
    // JavaScript code to dynamically create and insert a link element

    // Create a new link element
    var newLink = document.createElement('script');
    newLink.src = '/components/templates/operation_tournament/jquery-3.7.1.min.js';
    // Insert the new link element into the head of the document
    document.head.appendChild(newLink);
</script>
<script src="/components/templates/operation_tournament/fetchData.js"></script>
<script src="/components/templates/operation_tournament/operation_tournament.js"></script>

<?php
    //フッター呼び出し(フッター → /body → /html まで)
    require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php');
?>