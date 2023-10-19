<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
?>

<div class="Tournament_menu">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url(); ?>'">&lt;&lt;</button>大会管理メニュー</div>
    <div class="menu">
        <?php if($_SESSION['level']===1):?>
            <a href="#" new-tournament-register="new-tournament-register" class="single_menu">
                <div class="title">新規大会</div>
                <p>raqutyに掲載する大会を新規で作成します。</p>
            </a>
        <?php endif;?>
        <a href="<?php echo home_url('Tournament/List') ?>" class="single_menu">
            <div class="title">大会一覧</div>
            <p>raqutyに掲載中の大会を管理します。</p>
        </a>
        <a href="<?php echo home_url('Tournament') ?>" class="single_menu">
            <div class="title">アーカイブ</div>
            <p>終了済みの大会を管理します。</p>
        </a>
        <a href="<?php echo home_url('Tournament/Venue') ?>" class="single_menu">
            <div class="title">会場管理</div>
            <p>大会会場を登録・管理します。</p>
        </a>
    </div>

</div>

<script src="/components/templates/tournament_menu/tournament_menu.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>