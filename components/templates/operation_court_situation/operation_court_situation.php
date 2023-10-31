<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- jQueryを読み込む -->
    <style>
        body {
            background-color: lightyellow;
            /* 背景色を淡い黄色に指定 */
        }

        .header {
            width: 100%;
            height: 4rem;
            position: fixed;
            z-index: 99;
            background: white;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            font-family: Audiowide;
        }

        .header_img {
            height: 4rem;
        }

        .court-title,
        .TBD {
            font-family: Audiowide;
            font-size: 1.2rem;
            line-height: normal;
        }

        .TBD {
            position: absolute;
            top: calc(50% - 0.5em);
            text-align: center;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .TBD b {
        }

        .namearea {
            text-align: center;
            align-items: center;
            font-size: 1.0rem;
            line-height: 1.1;
            margin: 0 0;
        }
    </style>

    <!--<script>
        // ※現在の日時を取得する関数　オートリロードと共存不可のため放置
        function getCurrentDateTime() {
            // Dateオブジェクトを作成
            var now = new Date();
            // 年月日を取得
            var year = now.getFullYear();
            var month = now.getMonth() + 1;
            var date = now.getDate();
            // 時分秒を取得
            var hour = now.getHours();
            var minute = now.getMinutes();
            var second = now.getSeconds();
            // 2桁になるように0を追加
            month = ("0" + month).slice(-2);
            date = ("0" + date).slice(-2);
            hour = ("0" + hour).slice(-2);
            minute = ("0" + minute).slice(-2);
            second = ("0" + second).slice(-2);
            // 日付と時刻の文字列を作成
            var datetime = year + "-" + month + "-" + date + " " + hour + ":" + minute;
            // time要素にdatetime属性とテキストを設定
            var timeElement = document.getElementById("current");
            timeElement.setAttribute("datetime", datetime);
            timeElement.textContent = datetime;
        }
        // ページが読み込まれたら関数を実行
        window.onload = getCurrentDateTime;
        // 1秒ごとに関数を実行
        setInterval(getCurrentDateTime, 1000);
    </script>-->

    <script>
        $(function () {
            // 3秒ごとにサーバーからデータを取得してmain要素に反映する関数
            function updateMain() {
                $.ajax({
                    url: "/Operation/Court_situation", // データを取得するURL
                    type: "GET", // GETメソッドで通信
                    dataType: "html", // HTML形式でデータを受け取る
                    success: function (data) { // 通信成功時の処理
                        $("main").html(data); // main要素にデータを反映する
                    },
                    error: function () { // 通信失敗時の処理
                        $("main").html("エラーが発生しました"); // main要素にエラーメッセージを表示する。F12で確認してね
                    }
                });
            }

            // 最初に一回実行する
            updateMain();

            // 3秒ごとに繰り返し実行する
            setInterval(updateMain, 3000);
        });
    </script>
</head>

<body>

    <header>
        <div class="header" style="display: flex; align-items: center;">
            <a href="https://raquty.com/">
                <img src="/files/material/raquty.png" class="header_img">
            </a>
            <h1 style="margin-left: 5rem;">IWTO 2023</h1>
            <!--<p style="margin-left: 5rem;">
                <time id="current"></time>
            </p>-->
        </div>
    </header>

    <main>


        <?php
        function get_user_data($draw_id)
        { //ここは試合ごとに変更できるように書き換え必要
            $sql = "SELECT * FROM 1_entrylist WHERE draw_id = ?";
            global $tournament_access;
            $stmt = $tournament_access->prepare($sql);
            $stmt->bind_param("i", $draw_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $court_row = $result->fetch_assoc();
            $stmt->close();
            return $court_row;
        }


        //※※データベース接続の問題注意
        // コートの数を10に設定
        $court_num = 10;

        // データベースからコートの位置情報を取得する代わりに、川内のコートの座標を示した配列を直打ちで作成
        // $positionData = "SELECT * FROM 1_court_position";
        // $position_side = $tournament_access->query($positionData);
        $position_side = array(
            array("id" => 2, "court_num" => 1, "tate" => 0, "yoko" => 0),
            array("id" => 3, "court_num" => 2, "tate" => 2, "yoko" => 0),
            array("id" => 4, "court_num" => 3, "tate" => 0, "yoko" => 4),
            array("id" => 5, "court_num" => 4, "tate" => 2, "yoko" => 4),
            array("id" => 6, "court_num" => 5, "tate" => 0, "yoko" => 8),
            array("id" => 7, "court_num" => 6, "tate" => 2, "yoko" => 8),
            array("id" => 8, "court_num" => 7, "tate" => 5, "yoko" => 2),
            array("id" => 9, "court_num" => 8, "tate" => 7, "yoko" => 2),
            array("id" => 10, "court_num" => 9, "tate" => 5, "yoko" => 6),
            array("id" => 11, "court_num" => 10, "tate" => 7, "yoko" => 6)
        );

        // コートの位置情報を court_num をキーとする連想配列に格納
        $courtPosition = array();
        // foreach ループで配列を走査
        foreach ($position_side as $position_row) {
            $courtPosition[$position_row['court_num']] = $position_row;
        }
        //$courtPosition[]['tate']の最大値をarrayから取得する
        //※※この仕組みはコートの面数や配置によってはよくないので注意
        $tate_max = max(array_column($courtPosition, 'tate'));
        //'yoko'もおなじく
        $yoko_max = max(array_column($courtPosition, 'yoko'));

        //court要素のサイズを決める
        // 画面の幅の100%をコートの数で割る
        $yoko_kijyun = 80 / ($yoko_max + 4);
        $yoko_size = $yoko_kijyun * 4;
        // 画面の高さの100%をコートの数で割る
        $tate_kijyun = 80 / ($tate_max + 2); // コメントアウト
        $tate_size = $tate_kijyun * 2;
        //$tate_kijyun = $yoko_kijyun / 2; // 横幅の半分に設定
        


        // style タグでスタイルを定義
        echo '<style>';
        echo '.court_status {';
        echo '  border: 3px solid white;';
        echo '  padding: 0;';
        echo '  margin: 0;'; // マージンを0に設定
        echo "  width: $yoko_size%;"; // 幅をパーセント単位で設定
        echo "  height: $tate_size%;"; // 高さをパーセント単位で設定 
        echo '  text-align: center;'; // 文字を中央揃えに設定
        echo '  position: absolute;'; // 絶対配置に設定
        echo "background-color:  #82f74d";
        echo '}';
        echo '</style>';

        // forループでコートを表示
        for ($i = 1; $i <= $court_num; $i++) {
            // コートのHTML要素を開始し、class と style を設定
            echo '<div class="court_status" style="';
            // コートの位置情報を取得
            $court_position = $courtPosition[$i];
            $tate = $court_position['tate'];
            $yoko = $court_position['yoko'];
            // top と left のスタイルを計算して設定し、% を付ける
            $top = ($tate) * $tate_kijyun + 10; // 高さとパディング分ずらす
            $left = ($yoko) * $yoko_kijyun + 10; // 幅とパディング分ずらす
            echo "top: {$top}%; ";
            echo "left: {$left}%;";
            // コートのHTML要素を閉じる
            echo '">';

            echo '<div class="court-title">';
            echo $i;
            echo '</div>';

            // プリペアドステートメントを準備
            $stmt = $tournament_access->prepare("SELECT * FROM 1_game_index WHERE court_num = ? AND status = 1");
            // パラメータをバインド
            $stmt->bind_param("i", $i);
            // ステートメントを実行
            $stmt->execute();
            // 結果セットを取得
            $info = $stmt->get_result();
            if ($info->num_rows > 0) {
                while ($court_row = $info->fetch_assoc()) {
                    // HTMLモードに切り替え
                    ?>
                    <div class="court-player court-player<?php echo $i; ?>" data-player-number="<?php echo $i; ?>"
                        style="line-height: 0.3;">
                        <div class="court-round">
                            <p>
                                <?php echo $court_row['category'] . $court_row['round']; ?>
                            </p>
                        </div>
                        <?php
                        // get_user_data関数の結果を変数にキャッシュ
                        $first_player1 = get_user_data($court_row['draw_id_1']);
                        $first_player2 = get_user_data($court_row['draw_id_2']);
                        ?>
                        <div>
                            <div class="namearea">
                                <?php echo '[' . $court_row['draw_id_1'] . ']　<b>' . $first_player1['user1_name'] . '</b>　(' . $first_player1['user1_belonging'] . ')'; ?>
                            </div>
                        </div>
                        <p>VS</p>
                        <div>
                            <div class="namearea">
                                <?php echo '[' . $court_row['draw_id_2'] . ']　<b>' . $first_player2['user1_name'] . '</b>　(' . $first_player2['user1_belonging'] . ')'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="back back<?php echo $i; ?>" data-player-number="<?php echo $i; ?>" style="line-height: 0.5;">
                        <?php
                    // PHPモードに戻る
                }
            } else {
                // コートのプレイヤー情報を表示しない場合は、To Be Determined を表示
                ?>
                    <div class="court-player court-player<?php echo $i; ?>" data-player-number="<?php echo $i; ?>"
                        style="line-height: 0.5;">
                        <div class="TBD"> <b>To Be Determined</b>
                        </div>
                    </div>
                    <?php
            }

            //ここに緊急用の表示に対応できる機能を作りたい
            echo '</div>'; // コートのHTML要素を閉じる
            echo '</div>';

        }
        ?>
    </main>
</body>

<?php
// データベース接続を閉じる
//$tournament_access->close();
?>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__) . '/../../common/structure/footer/footer.php');
?>

</html>