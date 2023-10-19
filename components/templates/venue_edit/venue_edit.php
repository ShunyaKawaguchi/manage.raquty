<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php');
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['venue_id'])){
    $venue_data = check_venue_existance( $_GET['venue_id'] );
}else{
    header("Location: " . home_url('Tournament/Venue'));
}
?>

<div class="Venue">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/Venue'); ?>'">&lt;&lt;</button>会場管理</div>
    <div class="main_contents">
        <div class="Update_venue">
            <div class="sub_title">会場編集</div>
                <div class="wrap">
                    <div class="block">
                        <form method="post" id="Update_Venue_Form">
                            <div class="half">
                                <div class="wrapping">
                                    <label for="venue_name">会場名称</label><br>
                                    <input type="text" name="venue_name" id="venue_name" value="<?php echo h($venue_data['venue_name']) ?>" placeholder="正式名称で入力してください" required>
                                </div>
                                <div class="wrapping">
                                    <label for="venue_address">所在地</label><br>
                                    <input type="text" name="venue_address" id="venue_address" value="<?php echo h($venue_data['venue_address']) ?>" placeholder="例）〒000-0000 ◯◯県◯◯市◯◯町0番地" required>
                                </div>
                                <div class="wrapping">
                                    <label for="venue_map">Google Map埋め込み</label><br>
                                    <textarea name="venue_map" id="venue_map" placeholder="<iframe〜から始まるコードを貼り付けてください" required><?php echo d($venue_data['venue_map']) ?></textarea>
                                    <div id="venue_notice"></div>
                                </div>
                            </div>
                            <div class="half">
                                <div id="preview"></div>
                            </div>
                            <div class="court_setting">
                                <div class="wrapping">
                                    <div class="about">コート設定</div>
                                    <div class="title">コート面数設定</div>
                                    <label for="number_of_court">コート面数&nbsp;:&nbsp;</label>
                                    <input type="number" name="number_of_court" id="number_of_court" value="<?php echo $venue_data['court_number'] ?>">面
                                    <div class="title">コート名称設定</div>
                                    <div class="court_name">
                                        <?php court_name_setting($venue_data['court_name']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="all">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="hidden" name="template_id" value="<?php echo $_GET['venue_id']; ?>">
                                <?php if($_SESSION['level']===1):?>
                                    <input type="submit" value="更新" id="Update">
                                    <input type="submit" value="削除" id="Delete">
                                <?php endif ?>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>

<script src="/components/templates/venue_edit/venue_edit.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>