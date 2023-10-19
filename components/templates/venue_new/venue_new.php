<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//権限確認
if($_SESSION['level']!==1){
    header("Location: " . home_url('Tournament/Venue'));
}
?>

<div class="Venue">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/Venue'); ?>'">&lt;&lt;</button>会場管理</div>
    <div class="main_contents">
        <div class="Add_venue">
            <div class="sub_title">会場追加</div>
                <div class="wrap">
                    <div class="block">
                        <form method="post" id="Add_Venue_Form">
                            <div class="half">
                                <div class="wrapping">
                                    <label for="venue_name">会場名称</label><br>
                                    <input type="text" name="venue_name" id="venue_name" placeholder="正式名称で入力してください" required>
                                </div>
                                <div class="wrapping">
                                    <label for="venue_address">所在地</label><br>
                                    <input type="text" name="venue_address" id="venue_address" placeholder="例）〒000-0000 ◯◯県◯◯市◯◯町0番地" required>
                                </div>
                                <div class="wrapping">
                                    <label for="venue_map">Google Map埋め込み</label><br>
                                    <textarea name="venue_map" id="venue_map" placeholder="<iframe〜から始まるコードを貼り付けてください" required></textarea>
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
                                    <input type="number" name="number_of_court" id="number_of_court">面
                                </div>
                            </div>
                            <div class="all">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="submit" value="登録" id="Insert">
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>

<script src="/components/templates/venue_new/venue_new.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>