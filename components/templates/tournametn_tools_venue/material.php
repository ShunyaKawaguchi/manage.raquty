<?php 
function create_options_date($tournament_data){

    $dates = explode(',', $tournament_data['date']);
    sort($dates);

    $options = '<option value=""></option>';
    foreach ($dates as $date) {
        $formattedDate = date('Y-m-d', strtotime($date));

        $options .= '<option value="' . $formattedDate . '">' . $formattedDate . '</option>';
    }

    ?>
    <select name="tournament_date">
        <?php echo $options; ?>
    </select>

<?php }

function create_options_event($tournament_data){
    global $cms_access;

    // SQLクエリの実行
    $sql = "SELECT * FROM event_list WHERE tournament_id = ?";
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_data['tournament_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<select name="tournament_event">';
        echo '<option value=""></option>';

        while ($row = $result->fetch_assoc()) {
            $event_id = $row['event_id'];
            $event_name = $row['event_name'];
            echo '<option value="' . $event_id . '">' . $event_name . '</option>';
        }

        echo '</select>';
    }

    $stmt->close();
}

//会場テンプレート選択
function get_venue_data_v2() {
    $checked_venues = get_checked_venue_v2(h($_GET['tournament_id']),$_POST['tournament_event'],$_POST['tournament_date']);
    
    if (!empty($checked_venues)) {
        global $organizations_access;
        
        foreach ($checked_venues as $checked_venue) {
            $sql = "SELECT * FROM venues WHERE template_id = ?";
            $stmt = $organizations_access->prepare($sql);
            $stmt->bind_param("i", $checked_venue);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
        ?>
                <div class="venue_option"><input type="checkbox" name="venue[]" value="<?php echo $checked_venue; ?>" checked>
                <?php echo $row['venue_name']; ?></div>
        <?php
            }
        }
    }
    
    $sql = "SELECT template_id, venue_name FROM venues WHERE group_id = ?";
    global $organizations_access;
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $_SESSION['group_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='font-size:14px;margin:10px;'>会場テンプレートが登録されていません。</p>";
        $stmt->close();
    } else {
        while ($row = $result->fetch_assoc()) {
            $isChecked = in_array($row['template_id'], $checked_venues);
            
            if (!$isChecked) {
        ?>
                <div class="venue_option"><input type="checkbox" name="venue[]" value="<?php echo $row['template_id']; ?>">
                <?php echo $row['venue_name']; ?></div>
        <?php
            }
        }
        $stmt->close();
    }
}



//チェック済み会場
function get_checked_venue_v2($tournament_id,$event_id,$date) {
    $table_name = $tournament_id."_venues";
    $sql = "SELECT venue_id FROM $table_name WHERE event_date = ? AND event_id = ?";
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("si",$date,$event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $templateIds = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $templateIds[] = $row['venue_id'];
        }
    }

    $stmt->close();
    return $templateIds;
}
