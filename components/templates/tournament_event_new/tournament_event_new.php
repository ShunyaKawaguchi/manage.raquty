<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
}else{
    header("Location: " . home_url('Tournament/List'));
}
?>

<div class="Event_New">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="Event_information">
            <div class="title_wrap">
                <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Event?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">新規種目</div>
            </div>
            <div class="wrap">
                <form method="post" id="NewEvenet_Form">
                    <div class="block">
                        <div class="about">
                            <label for="event_name">種目名称</label>
                        </div>
                        <div class="about_value">
                            <input type="text" name="event_name" id="event_name" required>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="type">種別</label>
                        </div>
                        <div class="about_value">
                            <select name="type" id="type" required>
                                <option value="シングルス">シングルス</option>
                                <option value="ダブルス">ダブルス</option>
                            </select>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="gender">性別</label>
                        </div>
                        <div class="about_value">
                            <select name="gender" id="gender" required>
                                <option value="none">指定なし</option>
                                <option value="男子">男子</option>
                                <option value="女子">女子</option>
                            </select>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="capacity">定員</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="capacity" id="capacity" required><span>人</span>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="target">対象</label>
                        </div>
                        <div class="about_value">
                            <textarea name="target" id="target" required></textarea>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="min-age">最低年齢</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="min-age" id="min-age" required><span>歳</span>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="max-age">最高年齢</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="max-age" id="max-age" required><span>歳</span>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="feee">料金</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="fee" id="fee" required><span>円</span>
                        </div>
                    </div>
                    <div class="block">
                        <?php if($_SESSION['level'] === 1):?>
                            <div class="submit_wrap">
                                <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="submit" value="登録" id="Create_New_Event">
                            </div>
                        <?php endif;?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/components/templates/tournament_event_new/tournament_event_new.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>