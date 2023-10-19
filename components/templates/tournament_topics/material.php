<?php 
function create_topics_list(){ 
    $sql = "SELECT * FROM tournament_topics WHERE tournament_id = ? ORDER BY post_date DESC";
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $tournament_id = h($_GET['tournament_id']);
    $stmt->bind_param("i", $tournament_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { ?>
        <div class="wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>タイトル</th>
                        <th>投稿日時</th>
                        <th>状況</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    

    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['post_id'] ?></td>
                        <td><?php if($row['post_status']=='publish'){echo '<a href="https://raquty.com/Tournament/Topics/Article?topics_id='.$row['post_id'].'">'.$row['post_title'].'</a>';}else{echo $row['post_title'];} ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['post_date'])); ?></td>
                        <td><?php if($row['post_status']=='publish'){echo '公開中';}else{echo '未公開';} ?></td>
                        <td>
                            <form action="<?php echo home_url('Tournament/View/Topics/Edit') ?>" method="get">
                                <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'] ?>">
                                <input type="hidden" name="topics_id" value="<?PHP echo $row['post_id'] ?>">
                                <input type="submit" value="詳細">
                            </form>
                        </td>
                    </tr>
    

    <?php endwhile; ?>
        </tbody>
            </table>
        </div>
<?php
     } else {
            echo "<p style='font-size:14px;margin:30px;'>トピックスが登録されていません。</p>";
    }
}