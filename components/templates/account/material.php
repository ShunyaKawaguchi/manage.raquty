<?php
//ユーザー一覧テーブルの要素を作成
function get_group_user_info( $group_id ){

    $sql = 'SELECT user_id ,user_name , user_mail , level FROM group_users WHERE group_id = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()):
            if($row['level']==1){$level = '管理アカウント';}else{$level = '通常アカウント';}?>
            <tr>
                <td><?php 
                    if($_SESSION['level']==1):
                        if($row['user_id']!=$_SESSION['user_id']):?>
                        <input type="radio" value="<?php echo $row['user_id'] ?>" name="user_checked_id">
                        <?php endif;endif;?></td>
                <td><?php echo $row['user_name'] ?></td>
                <td><?php echo $row['user_mail'] ?></td>
                <td><?php echo $level ?></td>
            </tr>
    <?php endwhile;
    }
}

//ユーザーデータの更新
function update_user_info($user_id) {
    $user_datas = get_user_info($_SESSION['user_id']);
    $user_mail = $user_datas['user_mail'];
    $newUserName = h($_POST['new_user_name']);
    $newUserMail = h($_POST['new_user_mail']);

    if ($user_mail != $newUserMail) {
        if (check_mail($newUserMail, $_SESSION['group_id'])) {
            $sql = 'UPDATE group_users SET user_name = ?, user_mail = ? WHERE user_id = ?';
            $message = 'アカウント情報を更新しました。';
        } else {
            echo "<script>alert( '更新するメールアドレスは、既に同一団体内で登録されています。' );
                const newLocation = homeUrl('Account');
                window.location.href = newLocation;
                </script>";
            return;
        }
    } else {
        $sql = 'UPDATE group_users SET user_name = ? ,user_mail = ? WHERE user_id = ?';
        $message = 'アカウント情報を更新しました。';
    }

    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('ssi', $newUserName,$newUserMail, $user_id);

    if ($stmt->execute()) {
        //ログ追加
        $log_before = $user_mail.'/'.$_SESSION['user_name'];
        $log_after = $newUserMail.'/'.$newUserName;
        add_log('update_user_info' , null, null , $log_before, $log_after);
        // セッションに保存している名前を更新
        $_SESSION['user_name'] = $newUserName;
        echo "<script>alert('$message');
                const newLocation = homeUrl('Account');
                window.location.href = newLocation;
                </script>";
    } else {
        echo "<script>alert( 'アカウント情報の更新に失敗しました。' );
                const newLocation = homeUrl('Account');
                window.location.href = newLocation;
                </script>";
    }
}


//メールアドレス重複チェック（同じ団体内で同じメールアドレスを使っていないかチェックする）
function check_mail($mail,$group_id) {
    $query = "SELECT * FROM group_users WHERE user_mail = ? AND group_id = ?";
    global $organizations_access; 
    $stmt = $organizations_access->prepare($query);
    $stmt->bind_param("si", $mail, $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();

    if ($result->num_rows > 0) {
        return false;
    }

    return true;
}

//新規ユーザー追加
function add_new_user(){
    $newUserName = h($_POST['add_user_name']);
    $newUserMail = h($_POST['add_user_mail']);
    $newUserLevel = h($_POST['new_uesr_level']);
    $first_password =  generateRandomString();
    $password_hash = password_hash($first_password , PASSWORD_DEFAULT);


    if (check_mail($newUserMail, $_SESSION['group_id'])) {
        $sql = 'INSERT INTO group_users (user_name, user_mail, level, group_id , user_password) VALUES (?, ?, ?, ? ,?)';
        global $organizations_access;
        $stmt = $organizations_access->prepare($sql);

        $stmt->bind_param('ssiis', $newUserName, $newUserMail, $newUserLevel, $_SESSION['group_id'], $password_hash);

        if ($stmt->execute()) {
            //ログ追加
            $log_after = $newUserMail.'/'.$newUserName.'/権限:'.$newUserLevel.'/初期PW:'.$password_hash;
            add_log('create_user_info' , null, null , null, $log_after);

            //リダイレクト
            echo "<script>
                    const newUserName = '" . $newUserName . "';
                    const firstPassword = '" . $first_password . "';
                    const message = newUserName + 'さんのユーザー登録が完了しました。初期パスワードは「' + firstPassword + '」です。初期パスワードは登録したメールアドレスにも送信しています。';
                    alert(message);
                    const newLocation = homeUrl('Account');
                    window.location.href = newLocation;
                </script>";
    }

        $stmt->close();

    } else {
        echo "<script>alert( '入力されたメールアドレスは、既に同一団体内で登録されています。' );
            const newLocation = homeUrl('Account');
            window.location.href = newLocation;
            </script>";
        return;
    }
}

function edit_user() {
    global $organizations_access;

    $changeOptions = h($_POST['Change_Options']);
    $user_id = h($_POST['user_checked_id']);

    switch ($changeOptions) {
        case 'GU':
            update_user_level($user_id, 1, '管理アカウント');
            break;
        case 'GD':
            update_user_level($user_id, 2, '通常アカウント');
            break;
        case 'DE':
            delete_user($user_id);
            break;
        default:
            // 未定義の変更オプション
            echo "<script>alert('無効な操作が行われました。');</script>";
    }
}

function update_user_level($user_id, $newUserLevel, $message) {
    global $organizations_access;
    $user_level = get_user_info( $user_id );
    $log_before = '権限:'.$user_level['level'];

    $sql = 'UPDATE group_users SET level = ? WHERE user_id = ?';
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('ii', $newUserLevel, $user_id);

    if ($stmt->execute()) {
        //ログ追加
        $log_after = '権限:'.$newUserLevel;
        add_log('change_user_level' , null, null , $log_before , $log_after);
        //リダイレクト
        echo "<script>
        const Pre_Message = '" . $message . "';
        const Message = '選択したユーザーを「' + Pre_Message + '」に変更しました。';
        alert(Message);
        const newLocation = homeUrl('Account');
        window.location.href = newLocation;
    </script>";
    }
}

function delete_user($user_id) {
    global $organizations_access;

    $sql = 'DELETE FROM group_users WHERE user_id = ?';
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        //ログ追加
        $log_before = 'user_id:'.$user_id;
        add_log('delete_user_info' , null, null , $log_before , null);
        //リダイレクト
        echo "<script>alert('選択したユーザーを削除しました。誤ってユーザーを削除した場合は、新規でユーザー登録をしてください。');
        const newLocation = homeUrl('Account');
        window.location.href = newLocation;
        </script>";
    }
}

