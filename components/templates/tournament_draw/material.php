<?php 
function create_event_view($tournament_id ,$nonce_id){
    global $cms_access;
    $sql = 'SELECT event_id,event_name FROM event_list WHERE tournament_id = ?';
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;margin:30px;'>まだ、種目が登録されていません。</p>";
    } else { 
        while ($row = $result->fetch_assoc()) :?>
            <div class="Event">
            <div class="about"><?php echo $row['event_name']; ?>
                <?php if($_SESSION['level']===1):?>
                    <form action="<?php echo home_url('Tournament/View/Draw/New?tournament_id=').$_GET['tournament_id'];?>" id="New" method="post">
                        <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id;?>">
                        <input type="hidden" name="event_id" value="<?php echo $row['event_id'];?>">
                        <input type="submit" style="margin-left:20px;" value="ドロー作成&nbsp;&gt;&gt;">
                    </form>
                <?php endif;?></div>
                <?php  get_child_event( $row['event_id'] ,$nonce_id); ?>
            </div>
<?php   endwhile; 
    }
}

function get_child_event( $event_id ,$nonce_id){
    global $cms_access;
    $sql = 'SELECT * FROM child_event_list WHERE event_id = ?';
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;margin:30px;'>まだ、ドローが作成されていません。</p>";
    } else { ?>
        <div class="about_value">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ドロー名称</th>
                        <th>公開状況</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
<?php    while ($row = $result->fetch_assoc()) :
            if($row['status']==0){
                $status = '未公開';
            }else{
                $status = '公開中';
            }
?>   
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['child_event_name']; ?></td>
                        <td><?php echo $status; ?></td>
                        <td>
                            <form action="<?php echo home_url('Tournament/View/Draw/Edit?tournament_id=').$_GET['tournament_id'].'&child_event_id='.$row['id'];?>" method="post">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id;?>">
                                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                <input type="hidden" name="event_id" value="<?php echo $event_id ?>">
                                <input type="submit" value="詳細">
                            </form>
                        </td>
                    </tr> 
<?php   endwhile;  ?>
                </tbody>
            </table>
        </div>
<?php
    }
}