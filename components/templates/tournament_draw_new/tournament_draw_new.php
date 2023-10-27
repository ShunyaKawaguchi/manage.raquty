<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//セキュリティ
$nonce_id = raquty_nonce2() ;
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
    tournament_variable_settings( $tournament_data );
    $event_data = get_single_event_data( $_POST['event_id'] );
}else{
    header("Location: " . home_url('Tournament/List'));
}
if(!raquty_nonce_check()){ ?>
    <script> 
      alert('不正な遷移を検知しました。リダイレクトします。');
      window.location.href = "<?= home_url('Tournament/List') ?>";
    </script>';
<?php 
}
?>

<div class="Tournament_Draw">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="New_child_event">
            <div class="title"><button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Draw?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button>ドロー作成</div>
            <div class="about">
                <div class="about_value">
                    <p><?php echo $tournament_data['tournament_name'];?> の <?php echo $event_data['event_name'];?> にドローを追加します。</p>
                </div>
            <div class="about">
                <div class="about_value">
                    <form id="NewChildEvent" method="post">
                        <label for="child_event_name">ドロー名称</label>
                        <input type="text" name="child_event_name" id="child_event_name" placeholder="(例)シングルス1日目" required>
                        <label for="type">ドロータイプ</label>
                        <select id="type" name="type">
                            <option value="1">トーナメント</option>
                            <option value="2">総当たり</option>
                        </select>
                        <label for="capacity">参加人数</label>
                        <input type="number" name="capacity" id="capacity" required>
                        <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id;?>">
                        <input type="hidden" name="tournament_id" value="<?php echo h($_GET['tournament_id']);?>">
                        <input type="hidden" name="event_id" value="<?php echo $_POST['event_id'];?>">
                        <div class="submit_wrap">
                            <input type="submit" id="NewSubmit" name="NewChild" value="作成">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/components/templates/tournament_draw_new/tournament_draw_new.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>