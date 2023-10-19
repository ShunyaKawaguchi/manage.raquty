<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
}else{
    header("Location: " . home_url('Tournament/List'));
}
?>

<div class="Tournament_edit_information">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
        <div class="main_contents">
            <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
            <div class="basic_information">
                <div class="title_wrap">
                    <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">基本情報</div>
                </div>
                <div class="wrap">
                    <form method="post" id='Update_Form'>
                        <div class="block">
                            <div class="about">大会名称</div>
                            <div class="about_value">
                                <input type="text" name="tournament_name" value="<?php echo $tournament_data['tournament_name'] ?>" required>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">参加対象</div>
                            <div class="about_value">
                                <textarea  name="target" required><?php echo !empty($tournament_data['target']) ? $tournament_data['target'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">大会日程</div>
                            <div class="about_value">
                                <input type="hidden" name="date" id="date" value="<?php echo !empty($tournament_data['date']) ? $tournament_data['date'] : ''; ?>" readonly>
                                <?php get_date_select(); ?>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">エントリー開始</div>
                            <div class="about_value"><input type="date" name="entry_start" value="<?php echo !empty($tournament_data['entry_start']) ? date("Y-m-d", strtotime($tournament_data['entry_start'])) : ''; ?>"></div>
                        </div>
                        <div class="block">
                            <div class="about">エントリー締切</div>
                            <div class="about_value"><input type="date" name="entry_end" value="<?php echo !empty($tournament_data['entry_end']) ? date("Y-m-d", strtotime($tournament_data['entry_end'])) : ''; ?>"></div>
                        </div>
                        <div class="block">
                            <div class="about">ドロー発表</div>
                            <div class="about_value"><input type="date" name="draw_open" value="<?php echo !empty($tournament_data['draw_open']) ? date("Y-m-d", strtotime($tournament_data['draw_open'])) : ''; ?>"></div>
                        </div>
                        <div class="block">
                            <div class="about">会場</div>
                            <div class="about_value">
                                <div class="venues_wrap">
                                    <?php get_venue_data( $_SESSION['group_id'] ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">地域</div>
                            <div class="about_value">
                                <?php get_region_select($tournament_data['region']); ?>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">大会運営より</div>
                            <div class="about_value"><textarea name="comment"><?php echo !empty($tournament_data['comment']) ? d($tournament_data['comment']) : ''; ?></textarea></div>
                        </div>
                        <?php if($_SESSION['level']==1):?>
                            <div class="block">
                                <div class="submit_wrap">
                                    <?php  //ログ用の準備
                                    $venues = get_checked_venue(h($_GET['tournament_id']));
                                    $venue = '';
                                    $first = true;
                                    foreach($venues as $single_venue) {
                                        if ($first) {
                                            $venue = $single_venue;
                                            $first = false;
                                        } else {
                                            $venue = $venue . ',' . $single_venue;
                                        }
                                    }
                                    $log_before = $tournament_data['tournament_name'].'/date:'.$tournament_data['date'].'/'.$tournament_data['target'].'/entry_start:'.$tournament_data['entry_start'].'/entry_end:'.$tournament_data['entry_end'].'/draw_open:'.$tournament_data['draw_open'].'/'.$tournament_data['comment'].'/'.$tournament_data['region'].'/venues:'.$venue;?>
                                    <input type="hidden" name="log_before" value="<?php echo $log_before ?>">
                                    <input type='hidden' name='tournament_id' value='<?php echo $_GET['tournament_id'] ?>'>
                                    <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                    <input type='submit' value='更新' id='Update'>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
</div>

<script src="/components/templates/tournament_edit_information/tournament_edit_information.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>