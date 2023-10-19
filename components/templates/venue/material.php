<?php 
function get_group_venue_list($group_id) {
    $sql = 'SELECT * FROM venues WHERE group_id = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row['template_id'] ?></td>
            <td><?php echo $row['venue_name'] ?></td>
            <td><?php echo $row['venue_address'] ?></td>
            <td>
                <form action="<?php echo home_url('Tournament/Venue/View') ?>" method="GET">
                    <input type="hidden" name="venue_id" value="<?php echo $row['template_id'] ?>">
                    <input type="submit" value="詳細">
                </form>
            </td>
        </tr>
        <?php
    }
}

function get_venue_table( $group_id ){
    $sql = 'SELECT * FROM venues WHERE group_id = ?';
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;'>まだ、会場が登録されていません。</p>";
    } else {

    ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>会場名称</th>
                <th>所在地</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php get_group_venue_list( $_SESSION['group_id'] ) ?>
        </tbody>
    </table>

<?php 
    }
}
?>