<?php 
function create_event_list($tournament_id, $nonce_id) {
    $sql = 'SELECT event_id,event_name FROM event_list WHERE tournament_id = ?';
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;margin:30px;'>まだ、種目が登録されていません。</p>";
    } else { ?>
        <div class="Entry_List">
                <table class="Event_Listing">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>種目名称</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
        <?php
        while ($row = $result->fetch_assoc()):
        ?>
                    <tr>
                        <td><?php echo $row['event_id'] ?></td>
                        <td><?php echo $row['event_name'] ?></td>
                        <td>
                            <form method="post" action="<?php echo home_url('Tournament/View/Event/Edit?tournament_id='.$_GET['tournament_id']) ?>">
                                <input type='hidden' name='event_id' value="<?php echo $row['event_id'] ?>">
                                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                                <input type="submit" value="詳細">
                            </form>
                        </td>
                    </tr>
        <?php endwhile;?>

        </tbody>
            </table>
        </div>
    
    <?php
    }
}

?>