<?php
//サイドバー呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//団体情報呼び出し
$group_data = get_group_data( $_SESSION['group_id'] );
//ユーザー情報更新
if(isset($_POST['update_user_info'])){
    update_user_info( $_SESSION['user_id'] );
}
//ユーザー追加
if(isset($_POST['add_user'])){
    add_new_user();
}
//ユーザー権限編集・ユーザー削除
if(isset($_POST['edit_user'])){
    if(!empty($_POST['Change_Options']) && !empty($_POST['user_checked_id'])){
        edit_user();
    }
}
?>

<div class="Account">
    <div class="page_title">アカウント</div>
    <div class="group_info">
        <div class="title">公開情報</div>
        <div class="info">
            <div class="name">団体名称：<?php echo $group_data['organization_name'] ?></div>
            <div class="name">団体所在地：<?php if(!empty($group_data['address'])){echo $group_data['address'];}else{echo '登録なし';}; ?></div>
            <div class="name">電話番号：<?php if(!empty($group_data['phone'])){echo $group_data['phone'];}else{echo '登録なし';}; ?></div>
            <div class="name">メールアドレス：<?php if(!empty($group_data['mail'])){echo $group_data['mail'];}else{echo '登録なし';}; ?></div>
        </div>
        <div class="notice">※公開情報に変更がある場合は、raquty営業担当までご連絡ください。</div>
    </div>
    <div class="user">
        <div class="title">ユーザー管理</div>
        <div class="your_account">
            <div class="sub_title">このアカウント</div>
            <form action="" method="post">
                <div class="detail">
                    <?php $user_info = get_user_info( $_SESSION['user_id']); ?>
                    <div class="value">ユーザー名：<br><input type='text' name='new_user_name' value='<?php echo $user_info['user_name'] ?>' required></div>
                    <div class="value">メールアドレス：<br><input type='email' name='new_user_mail' value='<?php echo $user_info['user_mail'] ?>' required></div>
                </div>
                <input type='submit' name='update_user_info' value='変更'>
            </form>
        </div>
        <div class="user_list">
            <div class="sub_title">アカウント一覧</div>
            <form action="" method="post">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>ユーザー名</th>
                            <th>メールアドレス</th>
                            <th>権限</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php get_group_user_info( $_SESSION['group_id'] ) ?>
                    </tbody>
                </table>
                <?php if($_SESSION['level']==1):?>
                        <div class="change_user">
                            <span>選択したユーザーを</span>
                            <select name='Change_Options'>
                                <option>選択してください</option>
                                <option value='GU'>管理者アカウントにする</option>
                                <option value='GD'>通常アカウントにする</option>
                                <option value='DE'>削除する</option>
                            </select>
                            <input type='submit' name='edit_user' value='変更'>
                        </div>
                <?php endif;?>
            </form>
        </div>
        <?php if($_SESSION['level']==1):?>
            <div class="New_user">
                <div class="sub_title">新規ユーザー</div>
                <form action="" method="post">
                    <div class="format">
                        <div class="value">ユーザー名：<br><input type="text" name="add_user_name" required></div>
                        <div class="value">メールアドレス：<br><input type="email" name="add_user_mail" required></div>
                        <div class="value">権限：<br>
                            <select name="new_uesr_level">
                                <option value="0">選択してください</option>
                                <option value="0">通常アカウント</option>
                                <option value="1">管理アカウント</option>
                            </select>
                        </div>
                    </div>
                    <input type="submit" name='add_user' value="登録">
                </form>
            </div>
        <?php endif;?>
    </div>
        
</div>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>