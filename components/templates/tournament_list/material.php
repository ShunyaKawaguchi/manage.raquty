<?php
//大会一覧取得関数
function get_group_tournament_list($group_id) {
    global $cms_access;

    $sql = 'SELECT * FROM tournament WHERE group_id = ?';
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tournament_id = $row['tournament_id'];
            $tournament_name = $row['tournament_name'];
            $post_status = ($row['post_status'] == 'publish') ? '公開' : '未公開';
            $date_range = '未設定';

            if (!empty($row['date'])) {
                $tournament_date_array = explode(",", $row['date']);
                $date_timestamps = array_map('strtotime', $tournament_date_array);

                if (count($date_timestamps) === 1) {
                    $date_range = date("Y年n月j日", $date_timestamps[0]);
                } else {
                    $first_date = date("Y年n月j日", min($date_timestamps));
                    $last_date = date("Y年n月j日", max($date_timestamps));
                    $date_range = $first_date . "〜" . $last_date;
                }
            }

            $now = date('Y-m-d');
            $entry_status = "--";

            if ($post_status == '公開') {
                if ($row['entry_start'] > $now) {
                    $entry_status = '受付前';
                } elseif ($row['entry_start'] <= $now && $row['entry_end'] >= $now) {
                    $entry_status = '受付中';
                } else {
                    $entry_status = '締切';
                }
            }

            echo '<tr>';
            echo '<td>' . $tournament_id . '</td>';
            echo '<td>';
            if ($post_status == '公開') {
                echo '<a href="https://raquty.com/Tournament/About?tournament_id=' . $tournament_id . '" target="_blank">' . $tournament_name . '</a>';
            } else {
                echo $tournament_name;
            }
            echo '</td>';
            echo '<td>' . $date_range . '</td>';
            echo '<td>' . $entry_status . '</td>';
            echo '<td>' . $post_status . '</td>';
            echo '<td>';
            echo '<form action="' . home_url('Tournament/View') . '" method="GET">';
            echo '<input type="hidden" name="tournament_id" value="' . $tournament_id . '">';
            echo '<input type="submit" value="詳細">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>まだ大会が登録されていません。</tr>';
    }
}

?>