<div class="court-player court-player1" data-player-number="1">
    <?php
    // データの取得
    $count = 1;
    $gameData = "SELECT * FROM 1_game_index";
    $entryData = "SELECT * FROM 1_entrylist";
    $resultData = "SELECT * FROM 1_result";
    $info_side = $tournament_access->query($gameData);
    $entry_side = $tournament_access->query($entryData);

    // プレイヤー情報を格納する連想配列を初期化
    $playerData = array();

    while ($entry_row = $entry_side->fetch_assoc()) {
        $playerData[$entry_row['draw_id']] = $entry_row;
        // プレイヤーデータを連想配列に格納
    }

    while ($info_row = $info_side->fetch_assoc()) {
        if ($info_row['status'] == -1) {
            $player1_id = $info_row['draw_id_1'];
            $player2_id = $info_row['draw_id_2'];
            $game_id = $info_row['game_id'];

            // プレイヤーデータを取得
            $player1_data = $playerData[$player1_id];
            $player2_data = $playerData[$player2_id];

            $player1_name = $player1_data['user1_name'];
            $player2_name = $player2_data['user1_name'];
            ?>

            <div class="suggest" onclick="showFrontText(<?php echo $count; ?>);">
                <div class="court-round">
                    <p>候補
                        <?php echo $count; ?>
                    </p>
                </div>
                <div>
                    <div class="dragged-place">
                        <?php echo $player1_name; ?>
                    </div>
                </div>
                <p style="text-align: center;">VS</p>
                <div>
                    <div class="dragged-place">
                        <?php echo $player2_name; ?>
                    </div>
                </div>

                <div class="front-text" id="front-text-<?php echo $count; ?>" style="display: none; text-align: center;">
                    <br>本試合を<br>
                    <form action="/components/templates/operation_oop/suggest_add.php" method="post">
                        <select name="oop">
                            <option value="" selected>選択</option>
                            <?php
                            //ここコート数変更に対応してない注意
                            for ($i = 1; $i <= 10; $i++) {
                                echo "<option value=\"$i\">$i番コート</option>";
                            }
                            ?>
                        </select>
                        番コートに<br>
                        <input type="hidden" name="add" value="<?php echo $game_id; ?>">
                        <input type="submit" value="追加">
                    </form>
                    <button class="close-button" type="button"
                        onclick="event.stopPropagation(); hideFrontText(<?php echo $count; ?>); clearSuggest(<?php echo $count; ?>);">戻る</button>
                </div>



            </div>
            <hr>
            <?php
            $count++;
        }
    }
    ?>

    <!-- ここにCSSに移行する？-->
    <style>
        .front-text {
            position: absolute;
            z-index: 2;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.8);
            /* 白い背景で少し透明 */
            font-weight: bold;
            color: black;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .add-button {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            cursor: pointer;
        }

        .suggest {
            position: relative;
            /* 子要素のposition:absoluteを正しく動作させるため */
        }
    </style>

    <!-- ここにJSに移行する？-->
    <script>
        function showFrontText(count) {
            // 引数で渡されたカウントに対応するfront-textの要素を取得
            var frontText = document.getElementById("front-text-" + count);
            // front-textの要素のdisplayスタイルをblockに変更して表示する
            frontText.style.display = "block";
        }

        function hideFrontText(count) {
            // 引数で渡されたカウントに対応するfront-textの要素を取得
            var frontText = document.getElementById("front-text-" + count);
            // front-textの要素のdisplayスタイルをnoneに変更して隠す
            frontText.style.display = "none";
        }


    </script>
</div>