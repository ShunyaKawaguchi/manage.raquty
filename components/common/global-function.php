<?php
//投稿データを配列で返す
function get_post_data( $post_id ){
    $sql = "SELECT * FROM post_info WHERE post_id = ?";
    global $manage_access; 
    $stmt = $manage_access->prepare($sql);
    $stmt->bind_param("i", $post_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row; //post_dataを配列で返す
}

//home_url() 関数
function home_url( $dir = '') {
    $format = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
    return $format . $_SERVER['HTTP_HOST'] . '/' . ltrim($dir, '/');
}


// サニタイズ
function h($str){return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

//逆サニタイズ
function d($str){
    return html_entity_decode($str, ENT_QUOTES, "UTF-8");
}

//nonce関数
function raquty_nonce(){

    // ランダムな文字列を生成して nonce_id としてセッションに保管
    $nonce_id = generateRandomString();
    $_SESSION['nonce_id'] = $nonce_id;

    return $nonce_id;
}

//raquty_nonceを複数回利用したいとき
function raquty_nonce2(){

    // ランダムな文字列を生成して nonce_id としてセッションに保管
    $nonce_id = generateRandomString();
    $_SESSION['nonce_id2'] = $nonce_id;

    return $nonce_id;
}

//raquty_nonceを複数回利用したいとき
function raquty_nonce3(){

    // ランダムな文字列を生成して nonce_id としてセッションに保管
    $nonce_id = generateRandomString();
    $_SESSION['nonce_id3'] = $nonce_id;

    return $nonce_id;
}

// ランダムな英数10文字の文字列を作成
function generateRandomString($length = 10) {
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}

//ログイン判定関数
function is_login(){
    if(!empty($_SESSION['user_id'])){
        $login_status = true;
    }else{
        $login_status = false;
    }
    return $login_status;
}

//ログイン関数
function raquty_user_login($request_url){
    //ログイン失敗メッセージを初期化
    unset($_SESSION['login_error_message']);
    //ログインフォームから情報を受け取る
    $user_mail = h($_POST['user_email']);
    $user_password = h($_POST['user_password']);

    $sql = 'SELECT 	user_id , group_id , user_name , user_mail , level ,user_password FROM group_users WHERE user_mail = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('s', $user_mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password_hash = $row['user_password'];
        if (password_verify($user_password, $stored_password_hash)) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['group_id'] = $row['group_id'];
            $group_data = get_group_data( $row['group_id'] );
            $_SESSION['group_name'] = $group_data['organization_name'];
            $_SESSION['level'] = $row['level'];
            unset($group_data);
            header("Location: " . $request_url);
        } else {
            $_SESSION['login_error_message'] = 1;
            header("Location: " . home_url('Login_Authorization'));
        }
    } else {
        $_SESSION['login_error_message'] = 1;
        header("Location: " . home_url('Login_Authorization'));
    }

    $stmt->close();
}

//加盟団体取得
function get_group_data( $group_id ){
    $sql = "SELECT * FROM organizations WHERE organization_id = ?";
    global $organizations_access; 
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $group_id ); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row; 
}

//ユーザー情報取得
function get_user_info( $user_id ){
    $sql = 'SELECT 	user_id , group_id , user_name , user_mail , level  FROM group_users WHERE user_id = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $stmt->close();

    return $row; 
}

//簡易認証（パスワード）
function raquty_password_authentication() {
    $user_password = h($_POST['user_password']);

    $sql = 'SELECT user_password FROM group_users WHERE user_id = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password_hash = $row['user_password'];
        if (password_verify($user_password, $stored_password_hash)) {
            $stmt->close(); 
            return true;
        } else {
            $stmt->close(); 
            return false;
        }
    } else {
        $stmt->close(); 
        return false;
    }
}

//大会表示権利確認
function check_tournament_existance( $tournament_id ){
    $sql = "SELECT * FROM tournament WHERE tournament_id = ?";
    global $cms_access; 
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id ); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if($row['group_id']===$_SESSION['group_id']){
        return $row; 
    }else{
        header("Location: " . home_url('Tournament/List'));
    }
}

//大会情報の変数設定
function tournament_variable_settings( $tournament_data ){

}

//種目
function get_single_event_data( $event_id ){
    $sql = "SELECT * FROM event_list WHERE event_id = ?";
    global $cms_access; 
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $event_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row; 
}

//選手情報取得
function get_entry_player_info($tournament_id, $id) {
    global $tournament_access; 
    $table_name = $tournament_id . '_entrylist';

    $table_name = $tournament_access->real_escape_string($table_name);

    $sql = "SELECT * FROM $table_name WHERE id = ?";
    $stmt = $tournament_access->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row;
        } else {
            return null;
        }
    } else {
        return null;
    }
}

//ユーザーIDから選手情報を返す
function get_player_data($id) {
    global $user_access;
    $sql = "SELECT * FROM raquty_users WHERE user_id = ?";
    $stmt = $user_access->prepare($sql);

    if (!$stmt) {
        return null; 
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row; 
    } else {
        return null; 
    }
}

//player_idから選手情報を返す
function get_player_data_by_playerID($player_id) {
    global $user_access;
    $sql = "SELECT * FROM raquty_users WHERE player_id = ?";
    $stmt = $user_access->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row; 
    } else {
        return false; 
    }
}

//大会の公開情報を取得(パラメーター利用時のみ有効)
function get_tournamnet_status(){
    $sql = "SELECT * FROM tournament WHERE tournament_id = ?";
    global $cms_access; 
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $_GET['tournament_id']); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    //他の関数で行の存在が確認されてから呼び起こされることを前提に以下の処理を行う
    if($row['post_status']=='publish'){
        return true;
    }else{
        return false;
    }
    
}

//大会資料のパスを取得
function get_document_path($tournament_id , $document_type = ''){
    $sql = 'SELECT 	* FROM document_path WHERE document_type = ? AND tournament_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param('si', $document_type , $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        echo $row['document_path'];
    }else{
        echo '<p>まだアップロードしていません。</p>';
    }
}

function get_document_path_tf($tournament_id , $document_type = ''){
    $sql = 'SELECT 	* FROM document_path WHERE document_type = ? AND tournament_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param('si', $document_type , $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return true;
    }else{
        return false;
    }
}

function get_document_path_return($tournament_id , $document_type = ''){
    $sql = 'SELECT 	* FROM document_path WHERE document_type = ? AND tournament_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param('si', $document_type , $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row;
    }else{
        return false;
    }
}

function check_first_entry($tournament_id){
    $table_name = $tournament_id."_entrylist";
    $sql = "SELECT COUNT(id) AS row_count FROM $table_name WHERE draw_id != 9999 OR draw_id IS NULL OR draw_id = ''";
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        $row_count = $row['row_count']; 
        
        return $row_count > 0; 
    } else {
        
        return false;
    }
}

//ログ挿入関数
function add_log($log_action , $tournament_id, $event_id , $log_before, $log_after) {
    global $log_access;
    $table_name = $_SESSION['group_id'].'_group_history';
    $insertQuery = "INSERT INTO $table_name (user_id, log_action,tournament_id , event_id, log_before, log_after) VALUES (?, ? , ? , ?, ?, ?)";
    $insertStmt = $log_access->prepare($insertQuery);

    if ($insertStmt) {
        $insertStmt->bind_param("isiiss", $_SESSION['user_id'], $log_action, $tournament_id , $event_id, $log_before, $log_after);
        if ($insertStmt->execute()) {
            $insertStmt->close(); 
            return true;
        } else {
            $insertStmt->close(); 
            return false;
        }
    } else {
        return false;
    }
}

function check_tournament_topics_existance($topics_id) {
    $sql = "SELECT * FROM tournament_topics WHERE post_id = ?";
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $topics_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if($row['post_author']===$_SESSION['group_id']){
            return $row; 
        }else{
            header("Location: " . home_url('Tournament/List'));
        }
    } else {
        return false;
    }    
}

function alert($alert_name){
    if(!empty($_SESSION["$alert_name"])){ ?>
        <script> 
            alert("<?php echo $_SESSION["$alert_name"];?>");
        </script>
    <?php
        unset($_SESSION["$alert_name"]);
        }
}

function raquty_nonce_check($version = ''){
    $nonce_number = 'raquty_nonce'.$version;
    $session_nonce_number = 'nonce_id'.$version;
    if(isset($_SESSION[$session_nonce_number]) && isset($_POST[$nonce_number])){
        if($_SESSION[$session_nonce_number] == $_POST[$nonce_number]){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function check_child_event_existance($tournament_id, $child_event_id) {
    $sql = "SELECT * FROM child_event_list WHERE id = ? AND tournament_id = ?";
    global $cms_access; 
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("ii", $child_event_id, $tournament_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        //該当のchild_eventが加盟団体のものかどうかは、check_tournament_existance()で検証済
        return $row; 
    } else {
        $_SESSION['draw']='該当のドローが見当たりません。';
        header("Location: " . home_url('Tournament/View/Draw?tournament_id=') . $_GET['tournament_id']);
        exit; 
    }
}
