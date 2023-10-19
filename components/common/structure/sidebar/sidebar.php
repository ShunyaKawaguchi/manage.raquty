<div class="sidebar">
    <div class="top">
        <div class="title">
            <div class="raquty">raquty</div>
            <div class="admin">Admin</div>
        </div>
        <div class="message">
            <div class="welcome">ようこそ、</div>
            <div class="name"><?php echo $_SESSION['user_name'] ?></div>
            <div class="group">（&nbsp;<?php echo $_SESSION['group_name'] ?>&nbsp;）</div>
            <div class="san">さん！</div>
        </div>
        <div data-logout-action="raquty_user_logout" class="logout">ログアウト&#8594;</div>
    </div>
    <div class="top_smartphone">
        <div class="title">raquty&nbsp;Admin</div>
        <div data-logout-action="raquty_user_logout" class="logout">ログアウト&#8594;</div>
    </div>
    <?php if($post_data['post_id']===1):?>
        <div class="notice">
            <div class="title">raquty&nbsp;からのお知らせ</div>
            <div class="message">
                <p>現在、お知らせはありません。</p>
            </div>
        </div>
    <?php else:?>
        <div class="menu">
            <div class="title">MENU</div>
            <ul>
                <li><a href="<?php echo home_url() ?>">トップ</a></li>
                <li><a href="<?php echo home_url('Tournament') ?>">大会管理</a></li>
                <li><a href="<?php echo home_url('Topics') ?>">団体記事</a></li>
                <li><a href="<?php echo home_url('Account') ?>">アカウント</a></li>
            </ul>
        </div>
    <?php endif?>
</div>

<div class="main">