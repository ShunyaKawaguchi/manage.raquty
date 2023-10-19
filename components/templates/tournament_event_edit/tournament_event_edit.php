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
//種目データ取得
if(!empty($_POST['event_id'])){
    $event_id = h($_POST['event_id']);
    $_SESSION['edit_event_id'] = h($_POST['event_id']);
}elseif(!empty($_SESSION['edit_event_id'])){
    $event_id = $_SESSION['edit_event_id'];
}else{
    header("Location: " . home_url('Tournament/List'));
}

$event_data = get_single_event_data( h($event_id) );
?>

<div class="Event_Edit">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <div class="Event_information">
            <div class="title_wrap">
                <button style="margin-left:10px;margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Event?tournament_id=').$_GET['tournament_id']; ?>'">&lt;&lt;</button><div class="sub_title">種目情報</div>
            </div>
            <div class="wrap">
                <form method="post" id="Evenet_Form">
                    <div class="block">
                        <div class="about">
                            <label for="event_name">種目名称</label>
                        </div>
                        <div class="about_value">
                            <input type="text" name="event_name" id="event_name" value="<?php echo $event_data['event_name'] ?>" required>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="type">種別</label>
                        </div>
                        <div class="about_value">
                            <select name="type" id="type" required>
                                <option value="シングルス" <?php if($event_data['type']=='シングルス'){echo 'selected';}?>>シングルス</option>
                                <option value="ダブルス" <?php if($event_data['type']=='ダブルス'){echo 'selected';}?>>ダブルス</option>
                            </select>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="gender">性別</label>
                        </div>
                        <div class="about_value">
                            <select name="gender" id="gender" required>
                                <option value="none" <?php if($event_data['gender']=='none'){echo 'selected';}?>>指定なし</option>
                                <option value="男子" <?php if($event_data['gender']=='男子'){echo 'selected';}?>>男子</option>
                                <option value="女子" <?php if($event_data['gender']=='女子'){echo 'selected';}?>>女子</option>
                            </select>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="capacity">定員</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="capacity" id="capacity" value="<?php echo $event_data['capacity'] ?>" required><span>人</span>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="target">対象</label>
                        </div>
                        <div class="about_value">
                            <textarea type="text" name="target" id="target" required><?php echo $event_data['target'] ?></textarea>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="min-age">最低年齢</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="min-age" id="min-age" value="<?php echo $event_data['min-age'] ?>" required><span>歳</span>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="max-age">最高年齢</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="max-age" id="max-age" value="<?php echo $event_data['max-age'] ?>" required><span>歳</span>
                        </div>
                    </div>
                    <div class="block">
                        <div class="about">
                            <label for="feee">料金</label>
                        </div>
                        <div class="about_value">
                            <input type="number" name="fee" id="fee" value="<?php echo $event_data['fee'] ?>" required><span>円</span>
                        </div>
                    </div>
                    <div class="block">
                        <?php if($_SESSION['level'] === 1):?>
                            <div class="submit_wrap">
                                <?php $log_before = $event_data['event_name'].'/'.$event_data['type'].'/'.$event_data['gender'].'/'.$event_data['capacity'].'人/'.$event_data['target'].'/'.$event_data['min-age'].'歳から'.$event_data['max-age'].'歳まで/'.$event_data['fee'].'円'; ?>
                                <input type="hidden" name="log_before" value="<?php echo $log_before ?>">
                                <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                <input type="submit" value="更新" id="Update_data">
                                <input type="submit" value="削除" id="Delete_data">
                            </div>
                        <?php endif;?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<form id="delete">
    <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
    <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
    <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
</form>

<script src="/components/templates/tournament_event_edit/tournament_event_edit.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>