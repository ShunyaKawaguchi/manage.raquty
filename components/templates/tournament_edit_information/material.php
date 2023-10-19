<?php 
//日付設定
function get_date_select(){?>
    <div id="calendar">
        <div class="header">
            <button id="prevMonth">&lt;&lt;</button>
            <h1 id="currentMonth"></h1>
            <button id="nextMonth">&gt;&gt;</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
            </thead>
            <tbody id="calendarBody">
            </tbody>
        </table>
    </div>

<?php }

//会場テンプレート選択
function get_venue_data($group_id) {
    $checked_venues = get_checked_venue($_GET['tournament_id']);
    
    if (!empty($checked_venues)) {
        global $organizations_access;
        
        foreach ($checked_venues as $checked_venue) {
            $sql = "SELECT venue_name FROM venues WHERE template_id = ?";
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
    $stmt->bind_param("i", $group_id);
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
function get_checked_venue($tournament_id) {
    $sql = "SELECT template_id FROM venues WHERE tournament_id = ?";
    global $cms_access;
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $templateIds = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $templateIds[] = $row['template_id'];
        }
    }

    $stmt->close();
    return $templateIds;
}

function get_region_select($region){

$prefectures = [
    "","北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県",
    "茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "東京都", "神奈川県",
    "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県", "岐阜県",
    "静岡県", "愛知県", "三重県", "滋賀県", "京都府", "大阪府", "兵庫県",
    "奈良県", "和歌山県", "鳥取県", "島根県", "岡山県", "広島県", "山口県",
    "徳島県", "香川県", "愛媛県", "高知県", "福岡県", "佐賀県", "長崎県",
    "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"
];

echo '<select name="prefecture">';

foreach ($prefectures as $prefecture) {
    $selected = ($prefecture == $region) ? 'selected="selected"' : '';
    echo '<option value="' . $prefecture . '" ' . $selected . '>' . $prefecture . '</option>';
}

echo '</select>';
}

?>