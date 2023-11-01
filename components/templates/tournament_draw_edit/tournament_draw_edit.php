<?php
//呼び出し
// require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
require_once(dirname(__FILE__).'/round_robin_material.php') ;
//セキュリティ
$nonce_id2 = raquty_nonce2() ;
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
    tournament_variable_settings( $tournament_data );
    $child_event_data = check_child_event_existance($tournament_data['tournament_id'],$_GET['child_event_id']);
    $event_data = get_single_event_data( $child_event_data['event_id'] );
}else{
    header("Location: " . home_url('Tournament/List'));
}
if(!raquty_nonce_check()){
    if($_SESSION['auth']!==$_GET['auth']){ ?>
    <script> 
      alert('不正な遷移を検知しました。リダイレクトします。');
      window.location.href = "<?= home_url('Tournament/View/Draw?tournament_id=').$_GET['tournament_id']; ?>";
    </script>';
<?php 
    }
}
//アラートを追加
alert('new_child_event');
alert('insert_data_draw');
alert('change_status');
?>

<div class="Tournament_Draw">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?= home_url('Tournament/View/Draw?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name'])?> (<?php echo $child_event_data['child_event_name']?>) </div>
    <div class="main_contents">
        <div class="player_list">
            <div class="title">未決定選手リスト</div>
            <div class="List" id="List">
                <?php $dont_ids = getEntryIdsByChildEvent($child_event_data['id'], $tournament_data['tournament_id']);?>
                <?php import_player_data($child_event_data['event_id'],$child_event_data['capacity'],$nonce_id2,$dont_ids); ?>
            </div>
        </div>
        <?php 
            if($child_event_data['event_type'] == 1){
                echo '<div class="tournament" id="tournament">';
                    generate_tournament($child_event_data['capacity'],$event_data['type'],$nonce_id2);
                echo '</div>';
            }elseif( $child_event_data['event_type'] == 2){
                echo '<div class="RoundRobin" id="RoundRobin">';
                    generate_RoundRobin($child_event_data['capacity'],$event_data['type'],$nonce_id2);
                echo '</div>';
            }
        ?>
        <form id="PublishStatus" method="post" action="/components/templates/tournament_draw_edit/update_status.php">
            <input type="hidden" name="child_event_id" value="<?php echo $child_event_data['id'];?>">
            <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'];?>">
            <input type="hidden" name="event_id" value="<?php echo $child_event_data['event_id'];?>">
            <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id2; ?>">
            <input type="hidden" id="status" name="status" value="0">
            <?php if($child_event_data['status'] === 0){
                echo '<button class="button delete" id="delete" value="9999">ドロー削除</button>';
            } ?>
            <div class="publish">
                <?php 
                if($child_event_data['status'] == 0){
                    echo '<button class="button publish" id="publish" value="1">ドロー公開</button>';
                } elseif($child_event_data['status'] == 1){
                    echo '<button class="button publish" id="draft" value="0">ドロー非公開</button>';
                }
                ?>
            </div>
            <div id="passwordInputContainer" style="display: none;">
                <label for="password" style="font-family: Audiowide;">raquty Adminパスワード:</label>
                <input type="password" id="password" name="user_password">
                <button id="confirmPassword">確認</button>
            </div>
            <div id="back"  style="display: none;"></div>
        </form>
    </div>
</div>

<script src="/components/templates/tournament_draw_edit/tournament_draw_edit.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>