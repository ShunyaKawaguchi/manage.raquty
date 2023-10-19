<?php 
function get_tournament_venue($tournament_data){
    if(empty($tournament_data['date'])){
        echo '<p style="font-size:14px;">大会日程が設定されていません。</p>';
        return false;
    }
    $dates = explode(',', $tournament_data['date']);
    
    $sorted_dates = array_map(function($date) {
        return date_create_from_format('Y/m/d', $date);
    }, $dates);
    
    usort($sorted_dates, function($a, $b) {
        return $a <=> $b;
    });
    
    $sql = "SELECT * FROM event_list WHERE tournament_id = ?";
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_data['tournament_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="about">'. $row['event_name'] . '</div>';
            
            foreach($sorted_dates as $dateObj){
                $single_date = $dateObj->format('Y/m/d');
                echo '<div class="about_date">'. $single_date . '</div>';
                echo '<div class="about_venue">';
                $venue_name = get_tournament_venue_value($single_date , $row['event_id']);
                if(empty($venue_name)){
                    echo '登録していません。';
                }else{
                    echo $venue_name;
                }
                echo '</div>';
            }
        }

        $stmt->close();
    }
}


function get_tournament_venue_value($date, $event_id){
    $tournament_id = h($_GET['tournament_id']);
    $table_name = $tournament_id . '_venues';
    $sql = "SELECT * FROM $table_name WHERE event_date = ? AND event_id = ?";
    global $tournament_access;
    $stmt = $tournament_access->prepare($sql);
    $stmt->bind_param("si", $date, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = ''; 

    if ($result->num_rows > 0) {
        $first = true; 

        while ($row = $result->fetch_assoc()) {
            $venue_name = get_venue_template($row['venue_id']);
            
            if (!$first) {
                $output .= ', ';
            } else {
                $first = false;
            }

            $output .= $venue_name;
        }

        $stmt->close();
    }

    return $output;
}


function get_venue_template($venue_id){
    global $organizations_access;

    $sql = "SELECT venue_name FROM venues WHERE template_id = ?";
    $stmt = $organizations_access->prepare($sql);
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $stmt->close(); 

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['venue_name'];
    }

    return false;
}
