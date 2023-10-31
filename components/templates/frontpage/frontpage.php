<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
?>

<div class="frontpage">
    <div class="page_title">メインメニュー</div>
    <div class="menu">
        <a href="<?= home_url('Tournament') ?>" class="single_menu">
            <div class="title">大会管理</div>
            <ul>
                <li>新規大会登録</li>
                <li>登録済大会の管理</li>
                <li>実施済大会の管理</li>
            </ul>
        </a>
        
        <a href="<?php echo home_url('Topics') ?>" class="single_menu">
            <div class="title">団体記事</div>
            <ul>
                <li>団体ページの記事作成</li>
                <li>団体ページの記事管理</li>
            </ul>
        </a>
        <a href="" class="single_menu">
            <div class="title">会計</div>
            <ul>
                <li>売上金の管理</li>
                <li>手数料明細</li>
            </ul>
        </a>
        <a href="<?php echo home_url('Account') ?>" class="single_menu">
            <div class="title">アカウント</div>
            <ul>
                <li>団体情報管理</li>
                <li>ユーザー管理</li>
                <li>新規ユーザー登録</li>
            </ul>
        </a>

    </div>
    <?php if($post_data['post_id']===1):?>
        <div class="notice_smartphone">
            <div class="title">raquty&nbsp;からのお知らせ</div>
            <div class="message">
                <p>現在、お知らせはありません。</p>
            </div>
        </div>
    <?php endif?>
</div>
<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>