<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//セキュリティ対策のためnonce利用
$nonce_id = raquty_nonce();
//トピックスの存在＆編集資格確認
if(!empty($_GET['topics_id'])){
    $topics_data = check_group_topics_existance( h($_GET['topics_id']) );
    if($topics_data === false){
        header("Location: " . home_url('Topics'));
    }
}else{
    header("Location: " . home_url('Topics'));
}
?>

<div class="Group_Topics_Edit">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Topics'); ?>'">&lt;&lt;</button>団体記事</div>
    <div class="main_contents">
        <div class="Topics_information">
            <div class="title_wrap"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Topics') ?>'">&lt;&lt;</button>トピックス編集</div>
            <form method="post" id="Topics_Edit">
                <div class="wrap">
                    <div class="form_wrap">
                        <label for="post_title">タイトル</label>
                        <input type="text" name="post_title" id="post_title" value="<?php echo $topics_data['post_title'] ?>">
                    </div>
                    <div class="form_wrap">
                        <label for="editor">本文</label>
                        <textarea name="post_content" id="editor"><?php echo $topics_data['post_content'] ?></textarea>
                    </div>
                </div>
                <?php if($_SESSION['level']===1):?>
                    <div class="wrap">
                        <div class="form_wrap">
                            <div class="sub_title">コンテンツの変更を保存する</div>
                            <div class="submit_wrap">
                                <input type="hidden" name="topics_id" value="<?PHP echo $_GET['topics_id'] ?>">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="submit" id="Update" value="保存">
                            </div>
                            <div class="sub_title">公開設定</div>
                            <div class="submit_wrap">
                                <select name="Publishing_Settings">
                                    <option value="draft">非公開</option>
                                    <option value="publish" <?php if ($topics_data['post_status'] === "publish") { echo "selected"; } ?>>公開</option>
                                </select>
                                <div class="open_time">公開日時</div>
                                <input type="datetime-local" name="post_date" value="<?php echo $topics_data['post_date'] ?>" max="<?php echo date('Y-m-d\TH:i'); ?>">
                                <input type="submit" id="Update_Status" value="保存">
                            </div>
                            <div class="sub_title">投稿を削除する</div>
                            <div class="submit_wrap">
                                <label for='user_password'>raquty&nbsp;Admin&nbsp;パスワード</label>
                                <input type="password" name="user_password">
                                <?php if(isset($_SESSION['delete_group_topics_pw_error'])):?>
                                    <div style="font-size: 12px; color: red;">パスワードが認証できませんでした。</div>
                                <?php unset($_SESSION['delete_group_topics_pw_error']);
                                        endif;?>

                                <input type="submit" id="Delete" value="削除">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script src="/components/templates/group_topics_edit/group_topics_edit.min.js"></script>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>