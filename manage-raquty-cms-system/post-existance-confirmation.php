<?php
//リクエストURLを編集
$originalUri = $_SERVER['REQUEST_URI'];
//パラメーターを削除
$uriWithoutParams = strtok($originalUri, '?');
//末尾にスラッシュがついていた場合削除
$cleanedUri = preg_replace('/\/$/', '', $uriWithoutParams);
//トップページだけ特例
if($uriWithoutParams=='/'){
    $cleanedUri = '/'; 
}

//リクエストURLに該当するページがあるか検索
$sql = "SELECT post_id ,template, post_status FROM post_info WHERE permalink = ?";
$stmt = $manage_access->prepare($sql);
//リクエストしたURLを検索する
$stmt->bind_param("s", $cleanedUri); 
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row) {
    if($row['post_status']=='publish'){
        if($row['template']=='login'){
            if(!is_login()){
                $post_data = get_post_data( $row['post_id'] );
                $template = $post_data['template'];
            }else{
                header("Location: " . home_url(''));
            }
        }else{
            if(is_login()){
                $post_data = get_post_data( $row['post_id'] );
                $template = $post_data['template'];
            }else{
                header("Location: " . home_url('Login_Authorization'));
            }
        }
        
    }else{
        //post_id = 2 は Not Found Page　の投稿番号（公開していない→存在していないページ）
        $post_data = get_post_data( 2 );
        $template = $post_data['template'];
    }
}else{
    //post_id = 2 は Not Found Page　の投稿番号（公開していない→存在していないページ）
    $post_data = get_post_data( 2 );
    $template = $post_data['template'];
}
$stmt->close();
?>