<?php 
    if(isset($_SESSION['user_id'])){
        $sql = 'SELECT 	user_id  FROM group_users WHERE user_id = ?';
        global $organizations_access;
        $stmt = $organizations_access->prepare($sql);
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            //ユーザーIDが存在すればOK！
        }else{
            //ユーザーIDが存在しない場合→管理アカウントによりログイン中にアカウントを削除されたときなど
            unset($_SESSION['user_id']); 
            unset($_SESSION['group_name']);
            unset($_SESSION['user_name']);
            unset($_SESSION['level']);
            session_destroy();
            header("Location: " . home_url('Login_Authorization'));
        }
    }
?>