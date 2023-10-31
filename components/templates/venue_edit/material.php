<?php
//会場が存在するか、リクエストされた会場IDはgroup_idのテンプレートか
function check_venue_existance2( $venue_id ){
    $sql = "SELECT * FROM venues WHERE template_id = ?";
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $venue_id ); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if($row['group_id']===$_SESSION['group_id']){
        return $row; 
    }else{
        header("Location: " . home_url('Tournament/Venue'));
    }
}

//コート名称設定
function court_name_setting($venue_name){
    $court_names = explode(",", $venue_name);
    $count = 1; // コート番号の初期値 ?>
    <div class="single_court">
    <?php  foreach($court_names as $singlr_court_name): ?>
        <div class="court_num">コート<?php echo $count; ?>&nbsp;:&nbsp;<input type="text" name="court_name[]" value="<?php echo $singlr_court_name ?>"></div>
<?php $count++; // コート番号を増加
            endforeach; ?>
    </div>
<?php 
}
