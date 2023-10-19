<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
?>

<div class="Venue_List">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament'); ?>'">&lt;&lt;</button>会場管理</div>
    <div class="main_contents">
        <div class="venue_listing">
            <div class="sub_title">会場一覧<?php if($_SESSION['level']===1):?><button style="margin-left:20px;" onclick="window.location.href='<?php echo home_url('Tournament/Venue/New'); ?>'">会場追加&nbsp;&gt;&gt;</button><?php endif;?></div>
            <div class="wrap">
                <div class="block">
                    <?php get_venue_table( $_SESSION['group_id'] ); ?>
                </div>
            </div>
        </div>
    </div>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>