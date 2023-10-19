<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//セキュリティ対策のためnonce2利用
$nonce_id2 = raquty_nonce2();
//不正な遷移対策
if ($_SESSION['nonce_id'] !== $_POST['raquty_nonce']) {
    header("Location: " . home_url('Tournament/List'));
}
// パラメーターで送られた大会IDが存在し、ユーザーレベルが1でイベントIDも送信された場合
if (!empty($_GET['tournament_id']) && $_SESSION['level'] === 1 && !empty($_POST['event_id'])) {
    $tournament_data = check_tournament_existance(h($_GET['tournament_id']));
    $event_data = get_single_event_data(h($_POST['event_id']));

    if (!$tournament_data || !$event_data) {
        header("Location: " . home_url('Tournament/List'));
        exit; // リダイレクト後に終了
    }
} else {
    header("Location: " . home_url('Tournament/List'));
    exit; // リダイレクト後に終了
}

$err_flag = '';
$err_flag_2 = '';
$player1_name = '';
$player1_belonging = '';
$player2_name = '';
$player2_belonging = '';
$player1_id = '';
$player2_id = '';

if (isset($_POST['search_player'])) {
    $player1_id = isset($_POST['player1_id']) ? h($_POST['player1_id']) : '';
    $player2_id = isset($_POST['player2_id']) ? h($_POST['player2_id']) : '';

    if ($player1_id !== '') {
        $player1_data = get_player_data_by_playerID($player1_id);

        if ($player1_data === false) {
            $err_flag = ($_POST['search_player'] === 'player1') ? 1 : '';
        } else {
            $player1_name = h($player1_data['user_name']);
            $player1_belonging = h($player1_data['user_belonging']);
        }
    }

    if ($player2_id !== '') {
        $player2_data = get_player_data_by_playerID($player2_id);

        if ($player2_data === false) {
            $err_flag_2 = ($_POST['search_player'] === 'player2') ? 1 : '';
        } else {
            $player2_name = h($player2_data['user_name']);
            $player2_belonging = h($player2_data['user_belonging']);
        }
    }
}
?>

<div class="Entry_Edit">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
        <div class="main_contents">
            <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
            <div class="Entry_information">
                <div class="title_wrap">
                    <form action="<?php echo home_url('Tournament/View/Entry?tournament_id=').$_GET['tournament_id']?>" method="post">
                        <div class="sub_title"><input type='submit' style="margin-right:20px;" id="Entry_Back" value="&lt;&lt;">エントリー追加</div>
                    </form>
                </div>
                <form action="" method="post" id="Insert_Form">
                    <input type="hidden" name="tournament_id" value="<?php echo h($_GET['tournament_id']) ?>">
                    <input type="hidden" name="event_id" value="<?php echo $_POST['event_id'] ?>">
                    <input type="hidden" name="raquty_nonce" value="<?php echo $_POST['raquty_nonce'] ?>">
                    <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id2 ?>">

                    <div class="wrap">
                        <div class="player_title">選手1</div>
                        <div class="block">
                            <div class="about">
                                <label for="player1_name">選手ID（選手IDがない選手は空欄のまま「追加」を押してください。</label>
                            </div>
                            <div class="about_value">
                                    <input type="text" name="player1_id" id="player1_id" value="<?php echo $player1_id ?>">
                                    <button name="search_player" value='player1'>&nbsp;&gt;&gt;</button>
                                    <?php if($err_flag === 1):?><div class="err">該当選手が見つかりませんでした。</div><?php endif;?>
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">
                                <label for="player1_name">選手名</label>
                            </div>
                            <div class="about_value">
                                <input type="text" name="player1_name" id="player1_name"  class='required' value="<?php echo $player1_name?>">
                            </div>
                        </div>
                        <div class="block">
                            <div class="about">
                                <label for="player1_belonging">所属</label>
                            </div>
                            <div class="about_value">
                                <input type="text" name="player1_belonging" id="player1_belonging" class='required' value="<?php echo $player1_belonging?>">
                            </div>
                        </div>
                    </div>
                    <?php if($event_data['type']=='ダブルス'):?>
                        <div class="wrap">
                            <div class="player_title">選手2</div>
                            <div class="block">
                                <div class="about">
                                    <label for="player1_name">選手ID（選手IDがない選手は空欄のまま「追加」を押してください。</label>
                                </div>
                                <div class="about_value">
                                    <input type="text" name="player2_id" id="player2_id" value="<?php echo $player2_id ?>">
                                    <button name="search_player" value='player2'>&nbsp;&gt;&gt;</button>
                                    <?php if($err_flag_2 === 1):?><div class="err">該当選手が見つかりませんでした。</div><?php endif;?>
                                </div>
                            </div>
                            <div class="block">
                                <div class="about">
                                    <label for="player2_name">選手名</label>
                                </div>
                                <div class="about_value">
                                    <input type="text" name="player2_name" id="player2_name" class='required' value="<?php echo $player2_name?>">
                                </div>
                            </div>
                            <div class="block">
                                <div class="about">
                                    <label for="player2_belonging">所属</label>
                                </div>
                                <div class="about_value">
                                    <input type="text" name="player2_belonging" id="player2_belonging" class='required' value="<?php echo $player2_belonging?>">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="wrap" style="border: none;">
                        <div class="block" style="margin:0;">
                            <div class="submit_wrap">
                                <input type='submit' value='追加' id="Insert_data">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>

<script src="/components/templates/tournament_entry_new/tournament_entry_new.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>