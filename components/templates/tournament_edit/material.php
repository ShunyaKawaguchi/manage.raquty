<?php 
//日付設定
function foreach_date($dates) {
    if ($dates !== null) { 
        $tournament_date_array = explode(",", $dates);
        $count = count($tournament_date_array);

        foreach ($tournament_date_array as $index => $date) {
            echo $date;

            if ($index < ($count - 1)) {
                echo ' , ';
            }
        }
    }else{
        echo '未設定';
    }
}

//種目情報出力
function event_list_output($tournament_id) {
    global $cms_access;

    // SQLクエリを準備
    $sql = "SELECT * FROM event_list WHERE tournament_id = ?";
    $stmt = $cms_access->prepare($sql);
    if (!$stmt) {
        die("データベースエラー: " . $cms_access->error);
    }

    // パラメータをバインド
    $stmt->bind_param("i", $tournament_id);

    // クエリを実行
    if (!$stmt->execute()) {
        die("クエリ実行エラー: " . $stmt->error);
    }

    // 結果を取得
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="block">
                <div class="about"><?php echo h($row['event_name']); ?></div>
                <div class="about_value">
                    <div class="mini_title">種別</div>
                    <div class="mini_value"><?php echo h($row['type']); ?></div>
                </div>
                <div class="about_value">
                    <div class="mini_title">性別</div>
                    <div class="mini_value"><?php echo h($row['gender']); ?></div>
                </div>
                <div class="about_value">
                    <div class="mini_title">定員</div>
                    <div class="mini_value"><?php echo h($row['capacity']); ?>人</div>
                </div>
                <div class="about_value">
                    <div class="mini_title">料金</div>
                    <div class="mini_value"><?php echo h($row['fee']); ?>円</div>
                </div>
                <div class="about_value_1">
                    <div class="mini_title">年齢</div>
                    <div class="mini_value"><?php echo h($row['min-age']); ?>歳から<?php echo h($row['max-age']); ?>歳</div>
                </div>
                <div class="about_value_2">
                    <div class="mini_title">対象</div>
                    <div class="mini_value" style="white-space: pre-line;"><?php echo h($row['target']); ?></div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p>未設定</p>';
    }

    // ステートメントをクローズ
    $stmt->close();
}

//登録ずみの会場情報取得
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

//会場をカンマ区切りで出力
function foreach_venues($tournament_id) {
    $tournament_venues = get_checked_venue($tournament_id);
    
    if (empty($tournament_venues)) {
        echo '未設定';
    } else {
        global $organizations_access;
        $count = count($tournament_venues);

        foreach ($tournament_venues as $key => $checked_venue) {
            $sql = "SELECT venue_name FROM venues WHERE template_id = ?";
            $stmt = $organizations_access->prepare($sql);
            $stmt->bind_param("i", $checked_venue);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                echo $row['venue_name'];

                if ($key < $count - 1) {
                    echo '&nbsp;,&nbsp;';
                }
            }
        }
    }
}

function publishing_settings($tournament_data){
    check_tournament_information($tournament_data);
    if(!check_first_entry($tournament_data['tournament_id'])){
        tournament_delete();
    }

}

function check_tournament_information($tournament_data) {
    $err = array(); // エラーメッセージを格納する配列
    $err_count = 0;

    // 大会情報が入力されているか確認
    if (!hasNullOrEmpty($tournament_data)) {
        $result_basic_info = false;
    } else {
        $result_basic_info = true;
    }
    if (!$result_basic_info) {
        $err[] = '・基本情報を全て登録してください。';
        $err_count++;
    }

    // 種目が登録されているか確認
    if (!check_event_register($tournament_data['tournament_id'])) {
        $result_event = false;
    } else {
        $result_event = true;
    }
    if (!$result_event) {
        $err[] = '・種目を登録してください。';
        $err_count++;
    }

    // 大会要綱が登録されているか確認
    if (!get_document_path_tf($tournament_data['tournament_id'], 'outline')) {
        $result_outline = false;
    } else {
        $result_outline = true;
    }
    if (!$result_outline) {
        $err[] = '・大会要綱を登録してください。';
        $err_count++;
    }

    // エラーメッセージを表示
    if ($err_count > 0) {
        echo '
                <div class="block">
                <div class="about">大会公開までの必要事項</div>
                <div class="about_value"><p>';
        foreach ($err as $single_err) {
            echo $single_err . '<br>';
        }
        echo '</p></div></div>';
    }else{
        publish_tournament_publish($tournament_data);
    }
}


// 配列内に null または空白が含まれているかチェック
function hasNullOrEmpty($array) {
    foreach ($array as $value) {
        if ($value === null || $value === '') {
            return false; // null または空白が含まれている場合は false を返す
        }
    }
    return true; // null または空白が含まれていない場合は true を返す
}

function check_event_register($tournament_id){
    $sql = "SELECT * FROM event_list WHERE tournament_id = ?";
    global $cms_access; 
    $stmt = $cms_access->prepare($sql);
    $stmt->bind_param("i", $tournament_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $row_count = $result->num_rows;
    $stmt->close();

    return $row_count > 0; // 行数が1以上ならtrue、0ならfalseを返す
}

function tournament_delete(){ ?>
    <div class="block">
        <div class="about">大会削除</div>
        <div class="about_value">
            <p>大会削除は大会に最初のエントリーがされるまで可能です。<br>
                エントリー受付後の削除はできません。<br>
                大会を削除する場合は<a href="<?php echo home_url('Tournament/View/Delete?tournament_id=').$_GET['tournament_id'] ?>">こちら</a>から</p>
        </div>
    </div>
<?php }

function publish_tournament_publish($tournament_data){
    if(!check_first_entry($tournament_data['tournament_id'])){ ?>
    <div class="block">
        <div class="about">公開状況</div>
        <?php if($tournament_data['post_status']=='publish'){$publish_status = "公開中";}else{$publish_status = "未公開";} ?>
        <div class="about_value">
            <p>現在、大会は<?php echo $publish_status ?>です。公開状況を変更するには以下のボタンを押してください。</p>
            <form method="post" id="Change_Status">
                <?php $nonce_id = raquty_nonce(); ?>
                <input type="hidden" name="raquty_nonce" value="<?php echo $nonce_id ?>">
                <input type="hidden" name="tournament_id" value="<?php echo h($_GET['tournament_id']) ?>">
                <?php if($publish_status == "公開中"):?>
                    <input type="hidden" name="after_status" value="draft">
                    <input type="submit" name="change_status" value="大会を非公開にする" id="Change">
                <?php else: ?>
                    <input type="hidden" name="after_status" value="publish">
                    <input type="submit" name="change_status" value="大会を公開にする" id="Change">
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php
    }else{
        echo '<p style="padding:5px 30px;font-size:14px;">現在、大会公開中です。最初のエントリーが発生したため、大会の削除はできません。</p>';
    }

}


