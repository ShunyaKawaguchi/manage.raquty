<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
?>

<div class="Group_Topics">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url(); ?>'">&lt;&lt;</button>団体記事</div>
    <div class="main_contents">
                <div class="Topics_information">
                    <form method="post" id="New_Topics_Form">
                        <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                        <div class="title_wrap">トピックス一覧<?php if($_SESSION['level']===1):?><input type="submit" style="margin-left:20px;" id="New_Topics" value="新規作成&nbsp;&gt;&gt;"><?php endif;?></div>
                    </form>
                    <?php create_group_topics_list(); ?>
                </div>
    </div>
</div>

<script src="/components/templates/group_topics/group_topics.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>