<?php
function check_group_topics_existance($topics_id) {
    $sql = "SELECT * FROM group_topics WHERE post_id = ?";
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
            header("Location: " . home_url('Topics'));
        }
    } else {
        return false;
    }    
}
