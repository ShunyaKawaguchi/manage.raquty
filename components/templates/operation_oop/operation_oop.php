<?php 
    // check_venue_existance($venue_id, $tournament_id);
    //大会データ読み込み
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
    //アラート
    alert('OOP_Notice');

    //oopメインコンテンツ読み込み
    require_once(dirname(__FILE__).'/oop_material.php') ;
?>
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
            <?php create_court(h($_GET['venue_id']));?>
            </div>
        </div>  
        <div class="suggest">
            <!-- ここに対戦カード情報を表示する -->
        </div>
    </div>
</div>


<?php
    
    function get_player($entry_id){
        global $tournament_access;
        $table_name = h($_GET['tournamnet_id']).'_entrylist';
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