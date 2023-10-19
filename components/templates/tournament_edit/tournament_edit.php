<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//パラメーターで送られた大会IDが存在し、かつログイン中のグループが主催しているものなのか確認
if(!empty($_GET['tournament_id'])){
    $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
    tournament_variable_settings( $tournament_data );
}else{
    header("Location: " . home_url('Tournament/List'));
}
//アラートを追加
alert('change_tournament_status');
?>

<div class="Tournament_View">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/List'); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?></div>
    <div class="main_contents">
        <?php require_once(dirname(__FILE__).'/../../common/structure/tournament_edit_menu/tournament_edit_menu.php') ;?>
        <?php if($_SESSION['level'] === 1):?>
            <div class="Publishing_Settings">
                <div class="title_wrap">
                    <div class="sub_title">公開設定</div>
                </div>
                <div class="wrap">
                    <?php publishing_settings( $tournament_data ); ?>
                </div>
            </div>
        <?php endif;?>
        <div class="basic_information">
            <div class="title_wrap">
                <div class="sub_title">基本情報</div><button style="margin-left:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Information?tournament_id=').$_GET['tournament_id']; ?>'">編集</button>
            </div>
            <div class="wrap">
                <div class="block">
                    <div class="about">参加対象</div>
                    <div class="about_value"><p style="white-space: pre-line;"><?php echo !empty($tournament_data['target']) ? $tournament_data['target'] : '未設定'; ?></p></div>
                </div>
                <div class="block">
                    <div class="about">大会日程</div>
                    <div class="about_value"><p><?php foreach_date( $tournament_data['date'] ) ?></p></div>
                </div>
                <div class="block">
                    <div class="about">エントリー開始</div>
                    <div class="about_value"><p><?php echo !empty($tournament_data['entry_start']) ? $tournament_data['entry_start'] : '未設定'; ?></p></div>
                </div>
                <div class="block">
                    <div class="about">エントリー締切</div>
                    <div class="about_value"><p><?php echo !empty($tournament_data['entry_end']) ? $tournament_data['entry_end'] : '未設定'; ?></p></div>
                </div>
                <div class="block">
                    <div class="about">ドロー発表</div>
                    <div class="about_value"><p><?php echo !empty($tournament_data['draw_open']) ? $tournament_data['draw_open'] : '未設定'; ?></p></div>
                </div>
                <div class="block">
                    <div class="about">会場</div>
                    <div class="about_value"><p><?php foreach_venues($_GET['tournament_id']); ?></p></div>
                </div>
                <div class="block">
                    <div class="about">地域</div>
                    <div class="about_value"><p><?php echo !empty($tournament_data['region']) ? $tournament_data['region'] : '未設定'; ?></p></div>
                </div>
                <div class="block">
                    <div class="about">大会運営より</div>
                    <div class="about_value"><p style="white-space: pre-line;"><?php echo !empty($tournament_data['comment']) ? $tournament_data['comment'] : '未設定'; ?></p></div>
                </div>
            </div>
        </div>
        <div class="event_information">
            <div class="title_wrap">
                <div class="sub_title">種目情報</div><button style="margin-left:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Event?tournament_id=').$_GET['tournament_id']; ?>'">編集</button>
            </div>
            <div class="wrap">
                <?php event_list_output( $tournament_data['tournament_id'] ) ?>
            </div>
        </div>
        <div class="Documents">
            <div class="title_wrap">
                <div class="sub_title">大会資料</div><button style="margin-left:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Document?tournament_id=').$_GET['tournament_id']; ?>'">編集</button>
            </div>
            <div class="wrap">
                <div class="block">
                    <div class="about">大会要綱</div>
                    <div class="about_value"><p>
                        <?php $outline_data = get_document_path_return(h($_GET['tournament_id']), 'outline') ?>
                        <?php if(!$outline_data): ?>まだアップロードしていません。<?php else: ?><a href="<?php echo home_url($outline_data['document_path']) ?>" target="brank">大会要綱</a><?php endif; ?></p>
                    </div>
                    <div class="about">日程表</div>
                    <div class="about_value"><p>
                        <?php $timetable_data = get_document_path_return(h($_GET['tournament_id']), 'timetable') ?>
                        <?php if(!$timetable_data): ?>まだアップロードしていません。<?php else: ?><a href="<?php echo home_url($timetable_data['document_path']) ?>" target="brank">日程表</a><?php endif;?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/components/templates/tournament_edit/tournament_edit.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>