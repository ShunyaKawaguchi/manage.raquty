  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <link rel=”icon” href=“/favicon.ico”>
</head>

<?php 
    if(isset($_GET['venue_id'])){
        $venue_id = $_GET['venue_id'];
    }else{
        $venue_id = $_GET['venue_id'] = '';
    }
    if(isset($_GET['tournament_id'])){
        $tournament_id = $_GET['tournament_id'];
    }else{
        $tournament_id = '';
    }
    check_venue_existance($venue_id, $tournament_id);
?>

<?php
  function get_user_data($draw_id)
  {
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

  //大会データ読み込み
  $tournament_data = check_tournament_existance( h($_GET['tournament_id']) );
  ?>

<body>
  <header>
  <a href="#" class="slide_button"></a>
    <input id="r_sidebar" class="r_sidebar_button" type="checkbox">
    <label class="r_sidebar_label" for="r_sidebar"></label>
  </header>
  <div class="page_title">
    <div class="title_wrap">
    <button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament/View/Operation?tournament_id=').h($_GET['tournament_id']); ?>'">&lt;&lt;</button><?php echo h($tournament_data['tournament_name']) ?>
    </div>
    
  </div>
  <main>
  
    <div id="main__contents" class="main">
      <div class="main__inner">
        <ul class="main__inner__before">
          <li class="court">
            <div class="court-title">
              <h3>1</h3>
            </div>
            <?php
            $query = "SELECT * FROM 1_game_index WHERE court_num = 1 AND status = 1";
            $info = $tournament_access->query($query);
            if ($info->num_rows > 0) {
              while ($court1_row = $info->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court1_row['round'] . '</p>';
                echo '</div>';
                $c1_first_player1 = get_user_data($court1_row['draw_id_1']);
                $c1_first_player2 = get_user_data($court1_row['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c1_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c1_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="1" data-player-number="1" player-name1="' . $c1_first_player1['user1_name'] . '" player-name2="' . $c1_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="1" data-player-number="1" player-belonging1="' . $c1_first_player1['user1_belonging'] . '" player-name1="' . $c1_first_player1['user1_name'] . '" player-belonging2="' . $c1_first_player2['user1_belonging'] . '" player-name2="' . $c1_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="1" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="1" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query2 = "SELECT * FROM 1_game_index WHERE court_num = 1 AND status = 2";
            $info2 = $tournament_access->query($query2);
            if ($info2->num_rows > 0) {
              while ($court1_row2 = $info2->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court1_row2['round'] . '</p>';
                echo '</div>';
                $c1_second_player1 = get_user_data($court1_row2['draw_id_1']);
                $c1_second_player2 = get_user_data($court1_row2['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c1_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c1_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="1" data-player-number="2" player-name1="' . $c1_second_player1['user1_name'] . '" player-name2="' . $c1_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="1" data-player-number="2" player-belonging1="' . $c1_second_player1['user1_belonging'] . '" player-name1="' . $c1_second_player1['user1_name'] . '" player-belonging2="' . $c1_second_player2['user1_belonging'] . '" player-name2="' . $c1_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="1" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="1" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query3 = "SELECT * FROM 1_game_index WHERE court_num = 1 AND status = 3";
            $info3 = $tournament_access->query($query3);
            if ($info3->num_rows > 0) {
              while ($court1_row3 = $info3->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court1_row3['round'] . '</p>';
                echo '</div>';
                $c1_third_player1 = get_user_data($court1_row3['draw_id_1']);
                $c1_third_player2 = get_user_data($court1_row3['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c1_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c1_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="1" data-player-number="3" player-name1="' . $c1_third_player1['user1_name'] . '" player-name2="' . $c1_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="1" data-player-number="3" player-belonging1="' . $c1_third_player1['user1_belonging'] . '" player-name1="' . $c1_third_player1['user1_name'] . '" player-belonging2="' . $c1_third_player2['user1_belonging'] . '" player-name2="' . $c1_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="1" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="1" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court">
            <div class="court-title">
              <h3>2</h3>
            </div>
            <?php
            $query4 = "SELECT * FROM 1_game_index WHERE court_num = 2 AND status = 1";
            $info4 = $tournament_access->query($query4);
            if ($info4->num_rows > 0) {
              while ($court2_row4 = $info4->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court2_row4['round'] . '</p>';
                echo '</div>';
                $c2_first_player1 = get_user_data($court2_row4['draw_id_1']);
                $c2_first_player2 = get_user_data($court2_row4['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c2_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c2_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="2" data-player-number="1" player-name1="' . $c2_first_player1['user1_name'] . '" player-name2="' . $c2_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="2" data-player-number="1" player-belonging1="' . $c2_first_player1['user1_belonging'] . '" player-name1="' . $c2_first_player1['user1_name'] . '" player-belonging2="' . $c2_first_player2['user1_belonging'] . '" player-name2="' . $c2_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="2" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="2" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query5 = "SELECT * FROM 1_game_index WHERE court_num = 2 AND status = 2";
            $info5 = $tournament_access->query($query5);
            if ($info5->num_rows > 0) {
              while ($court2_row5 = $info5->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court2_row5['round'] . '</p>';
                echo '</div>';
                $c2_second_player1 = get_user_data($court2_row5['draw_id_1']);
                $c2_second_player2 = get_user_data($court2_row5['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c2_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c2_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="2" data-player-number="2" player-name1="' . $c2_second_player1['user1_name'] . '" player-name2="' . $c2_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="2" data-player-number="2" player-belonging1="' . $c2_second_player1['user1_belonging'] . '" player-name1="' . $c2_second_player1['user1_name'] . '" player-belonging2="' . $c2_second_player2['user1_belonging'] . '" player-name2="' . $c2_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="2" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="2" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query6 = "SELECT * FROM 1_game_index WHERE court_num = 2 AND status = 3";
            $info6 = $tournament_access->query($query6);
            if ($info6->num_rows > 0) {
              while ($court2_row6 = $info6->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court2_row6['round'] . '</p>';
                echo '</div>';
                $c2_third_player1 = get_user_data($court2_row6['draw_id_1']);
                $c2_third_player2 = get_user_data($court2_row6['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c2_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c2_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="2" data-player-number="3" player-name1="' . $c2_third_player1['user1_name'] . '" player-name2="' . $c2_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="2" data-player-number="3" player-belonging1="' . $c2_third_player1['user1_belonging'] . '" player-name1="' . $c2_third_player1['user1_name'] . '" player-belonging2="' . $c2_third_player2['user1_belonging'] . '" player-name2="' . $c2_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="2" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="2" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court">
            <div class="court-title">
              <h3>3</h3>
            </div>
            <?php
            $query7 = "SELECT * FROM 1_game_index WHERE court_num = 3 AND status = 1";
            $info7 = $tournament_access->query($query7);
            if ($info7->num_rows > 0) {
              while ($court3_row7 = $info7->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court3_row7['round'] . '</p>';
                echo '</div>';
                $c3_first_player1 = get_user_data($court3_row7['draw_id_1']);
                $c3_first_player2 = get_user_data($court3_row7['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c3_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c3_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="3" data-player-number="1" player-name1="' . $c3_first_player1['user1_name'] . '" player-name2="' . $c3_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="3" data-player-number="1" player-belonging1="' . $c3_first_player1['user1_belonging'] . '" player-name1="' . $c3_first_player1['user1_name'] . '" player-belonging2="' . $c3_first_player2['user1_belonging'] . '" player-name2="' . $c3_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="3" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="3" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query8 = "SELECT * FROM 1_game_index WHERE court_num = 3 AND status = 2";
            $info8 = $tournament_access->query($query8);
            if ($info8->num_rows > 0) {
              while ($court3_row8 = $info8->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court3_row8['round'] . '</p>';
                echo '</div>';
                $c3_second_player1 = get_user_data($court3_row8['draw_id_1']);
                $c3_second_player2 = get_user_data($court3_row8['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c3_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c3_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="3" data-player-number="2" player-name1="' . $c3_second_player1['user1_name'] . '" player-name2="' . $c3_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="3" data-player-number="2" player-belonging1="' . $c3_second_player1['user1_belonging'] . '" player-name1="' . $c3_second_player1['user1_name'] . '" player-belonging2="' . $c3_second_player2['user1_belonging'] . '" player-name2="' . $c3_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="3" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="3" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query9 = "SELECT * FROM 1_game_index WHERE court_num = 3 AND status = 3";
            $info9 = $tournament_access->query($query9);
            if ($info9->num_rows > 0) {
              while ($court3_row9 = $info9->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court3_row9['round'] . '</p>';
                echo '</div>';
                $c3_third_player1 = get_user_data($court3_row9['draw_id_1']);
                $c3_third_player2 = get_user_data($court3_row9['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c3_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c3_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="3" data-player-number="3" player-name1="' . $c3_third_player1['user1_name'] . '" player-name2="' . $c3_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="3" data-player-number="3" player-belonging1="' . $c3_third_player1['user1_belonging'] . '" player-name1="' . $c3_third_player1['user1_name'] . '" player-belonging2="' . $c3_third_player2['user1_belonging'] . '" player-name2="' . $c3_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="3" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="3" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court">
            <div class="court-title">
              <h3>4</h3>
            </div>
            <?php
            $query10 = "SELECT * FROM 1_game_index WHERE court_num = 4 AND status = 1";
            $info10 = $tournament_access->query($query10);
            if ($info10->num_rows > 0) {
              while ($court4_row10 = $info10->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court4_row10['round'] . '</p>';
                echo '</div>';
                $c4_first_player1 = get_user_data($court4_row10['draw_id_1']);
                $c4_first_player2 = get_user_data($court4_row10['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c4_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c4_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="4" data-player-number="1" player-name1="' . $c4_first_player1['user1_name'] . '" player-name2="' . $c4_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="4" data-player-number="1" player-belonging1="' . $c4_first_player1['user1_belonging'] . '" player-name1="' . $c4_first_player1['user1_name'] . '" player-belonging2="' . $c4_first_player2['user1_belonging'] . '" player-name2="' . $c4_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="4" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="4" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query11 = "SELECT * FROM 1_game_index WHERE court_num = 4 AND status = 2";
            $info11 = $tournament_access->query($query11);
            if ($info11->num_rows > 0) {
              while ($court4_row11 = $info11->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court4_row11['round'] . '</p>';
                echo '</div>';
                $c4_second_player1 = get_user_data($court4_row11['draw_id_1']);
                $c4_second_player2 = get_user_data($court4_row11['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c4_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c4_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="4" data-player-number="2" player-name1="' . $c4_second_player1['user1_name'] . '" player-name2="' . $c4_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="4" data-player-number="2" player-belonging1="' . $c4_second_player1['user1_belonging'] . '" player-name1="' . $c4_second_player1['user1_name'] . '" player-belonging2="' . $c4_second_player2['user1_belonging'] . '" player-name2="' . $c4_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="4" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="4" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query12 = "SELECT * FROM 1_game_index WHERE court_num = 4 AND status = 3";
            $info12 = $tournament_access->query($query12);
            if ($info12->num_rows > 0) {
              while ($court4_row12 = $info12->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court4_row12['round'] . '</p>';
                echo '</div>';
                $c4_third_player1 = get_user_data($court4_row12['draw_id_1']);
                $c4_third_player2 = get_user_data($court4_row12['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c4_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c4_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="4" data-player-number="3" player-name1="' . $c4_third_player1['user1_name'] . '" player-name2="' . $c4_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="4" data-player-number="3" player-belonging1="' . $c4_third_player1['user1_belonging'] . '" player-name1="' . $c4_third_player1['user1_name'] . '" player-belonging2="' . $c4_third_player2['user1_belonging'] . '" player-name2="' . $c4_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="4" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="4" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court-right">
            <div class="court-title">
              <h3>5</h3>
            </div>
            <?php
            $query13 = "SELECT * FROM 1_game_index WHERE court_num = 5 AND status = 1";
            $info13 = $tournament_access->query($query13);
            if ($info13->num_rows > 0) {
              while ($court5_row13 = $info13->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court5_row13['round'] . '</p>';
                echo '</div>';
                $c5_first_player1 = get_user_data($court5_row13['draw_id_1']);
                $c5_first_player2 = get_user_data($court5_row13['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c5_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c5_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="5" data-player-number="1" player-name1="' . $c5_first_player1['user1_name'] . '" player-name2="' . $c5_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="5" data-player-number="1" player-belonging1="' . $c5_first_player1['user1_belonging'] . '" player-name1="' . $c5_first_player1['user1_name'] . '" player-belonging2="' . $c5_first_player2['user1_belonging'] . '" player-name2="' . $c5_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="5" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="5" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query14 = "SELECT * FROM 1_game_index WHERE court_num = 5 AND status = 2";
            $info14 = $tournament_access->query($query14);
            if ($info14->num_rows > 0) {
              while ($court5_row14 = $info14->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court5_row14['round'] . '</p>';
                echo '</div>';
                $c5_second_player1 = get_user_data($court5_row14['draw_id_1']);
                $c5_second_player2 = get_user_data($court5_row14['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c5_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c5_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="5" data-player-number="2" player-name1="' . $c5_second_player1['user1_name'] . '" player-name2="' . $c5_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="5" data-player-number="2" player-belonging1="' . $c5_second_player1['user1_belonging'] . '" player-name1="' . $c5_second_player1['user1_name'] . '" player-belonging2="' . $c5_second_player2['user1_belonging'] . '" player-name2="' . $c5_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="5" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="5" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query15 = "SELECT * FROM 1_game_index WHERE court_num = 5 AND status = 3";
            $info15 = $tournament_access->query($query15);
            if ($info15->num_rows > 0) {
              while ($court5_row15 = $info15->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court5_row15['round'] . '</p>';
                echo '</div>';
                $c5_third_player1 = get_user_data($court5_row15['draw_id_1']);
                $c5_third_player2 = get_user_data($court5_row15['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c5_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c5_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="5" data-player-number="3" player-name1="' . $c5_third_player1['user1_name'] . '" player-name2="' . $c5_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="5" data-player-number="3" player-belonging1="' . $c5_third_player1['user1_belonging'] . '" player-name1="' . $c5_third_player1['user1_name'] . '" player-belonging2="' . $c5_third_player2['user1_belonging'] . '" player-name2="' . $c5_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="5" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="5" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>
        </ul>

        <ul class="main__inner__after">
          <li class="court">
            <div class="court-title">
              <h3>6</h3>
            </div>
            <?php
            $query16 = "SELECT * FROM 1_game_index WHERE court_num = 6 AND status = 1";
            $info16 = $tournament_access->query($query16);
            if ($info16->num_rows > 0) {
              while ($court6_row16 = $info16->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court6_row16['round'] . '</p>';
                echo '</div>';
                $c6_first_player1 = get_user_data($court6_row16['draw_id_1']);
                $c6_first_player2 = get_user_data($court6_row16['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c6_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c6_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="6" data-player-number="1" player-name1="' . $c6_first_player1['user1_name'] . '" player-name2="' . $c6_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="6" data-player-number="1" player-belonging1="' . $c6_first_player1['user1_belonging'] . '" player-name1="' . $c6_first_player1['user1_name'] . '" player-belonging2="' . $c6_first_player2['user1_belonging'] . '" player-name2="' . $c6_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="6" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="6" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query17 = "SELECT * FROM 1_game_index WHERE court_num = 6 AND status = 2";
            $info17 = $tournament_access->query($query17);
            if ($info17->num_rows > 0) {
              while ($court6_row17 = $info17->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court6_row17['round'] . '</p>';
                echo '</div>';
                $c6_second_player1 = get_user_data($court6_row17['draw_id_1']);
                $c6_second_player2 = get_user_data($court6_row17['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c6_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c6_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="6" data-player-number="2" player-name1="' . $c6_second_player1['user1_name'] . '" player-name2="' . $c6_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="6" data-player-number="2" player-belonging1="' . $c6_second_player1['user1_belonging'] . '" player-name1="' . $c6_second_player1['user1_name'] . '" player-belonging2="' . $c6_second_player2['user1_belonging'] . '" player-name2="' . $c6_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="6" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="6" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query18 = "SELECT * FROM 1_game_index WHERE court_num = 6 AND status = 3";
            $info18 = $tournament_access->query($query18);
            if ($info18->num_rows > 0) {
              while ($court6_row18 = $info18->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court6_row18['round'] . '</p>';
                echo '</div>';
                $c6_third_player1 = get_user_data($court6_row18['draw_id_1']);
                $c6_third_player2 = get_user_data($court6_row18['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c6_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c6_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="6" data-player-number="3" player-name1="' . $c6_third_player1['user1_name'] . '" player-name2="' . $c6_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="6" data-player-number="3" player-belonging1="' . $c6_third_player1['user1_belonging'] . '" player-name1="' . $c6_third_player1['user1_name'] . '" player-belonging2="' . $c6_third_player2['user1_belonging'] . '" player-name2="' . $c6_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="6" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="6" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court">
            <div class="court-title">
              <h3>7</h3>
            </div>
            <?php
            $query19 = "SELECT * FROM 1_game_index WHERE court_num = 7 AND status = 1";
            $info19 = $tournament_access->query($query19);
            if ($info19->num_rows > 0) {
              while ($court7_row19 = $info19->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court7_row19['round'] . '</p>';
                echo '</div>';
                $c7_first_player1 = get_user_data($court7_row19['draw_id_1']);
                $c7_first_player2 = get_user_data($court7_row19['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c7_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c7_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="7" data-player-number="1" player-name1="' . $c7_first_player1['user1_name'] . '" player-name2="' . $c7_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="7" data-player-number="1" player-belonging1="' . $c7_first_player1['user1_belonging'] . '" player-name1="' . $c7_first_player1['user1_name'] . '" player-belonging2="' . $c7_first_player2['user1_belonging'] . '" player-name2="' . $c7_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="7" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="7" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query20 = "SELECT * FROM 1_game_index WHERE court_num = 7 AND status = 2";
            $info20 = $tournament_access->query($query20);
            if ($info20->num_rows > 0) {
              while ($court7_row20 = $info20->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court7_row20['round'] . '</p>';
                echo '</div>';
                $c7_second_player1 = get_user_data($court7_row20['draw_id_1']);
                $c7_second_player2 = get_user_data($court7_row20['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c7_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c7_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="7" data-player-number="2" player-name1="' . $c7_second_player1['user1_name'] . '" player-name2="' . $c7_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="7" data-player-number="2" player-belonging1="' . $c7_second_player1['user1_belonging'] . '" player-name1="' . $c7_second_player1['user1_name'] . '" player-belonging2="' . $c7_second_player2['user1_belonging'] . '" player-name2="' . $c7_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="7" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="7" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query21 = "SELECT * FROM 1_game_index WHERE court_num = 7 AND status = 3";
            $info21 = $tournament_access->query($query21);
            if ($info21->num_rows > 0) {
              while ($court7_row21 = $info21->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court7_row21['round'] . '</p>';
                echo '</div>';
                $c7_third_player1 = get_user_data($court7_row21['draw_id_1']);
                $c7_third_player2 = get_user_data($court7_row21['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c7_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c7_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="7" data-player-number="3" player-name1="' . $c7_third_player1['user1_name'] . '" player-name2="' . $c7_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="7" data-player-number="3" player-belonging1="' . $c7_third_player1['user1_belonging'] . '" player-name1="' . $c7_third_player1['user1_name'] . '" player-belonging2="' . $c7_third_player2['user1_belonging'] . '" player-name2="' . $c7_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="7" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="7" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court">
            <div class="court-title">
              <h3>8</h3>
            </div>
            <?php
            $query22 = "SELECT * FROM 1_game_index WHERE court_num = 8 AND status = 1";
            $info22 = $tournament_access->query($query22);
            if ($info22->num_rows > 0) {
              while ($court8_row22 = $info22->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court8_row22['round'] . '</p>';
                echo '</div>';
                $c8_first_player1 = get_user_data($court8_row22['draw_id_1']);
                $c8_first_player2 = get_user_data($court8_row22['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c8_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c8_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="8" data-player-number="1" player-name1="' . $c8_first_player1['user1_name'] . '" player-name2="' . $c8_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="8" data-player-number="1" player-belonging1="' . $c8_first_player1['user1_belonging'] . '" player-name1="' . $c8_first_player1['user1_name'] . '" player-belonging2="' . $c8_first_player2['user1_belonging'] . '" player-name2="' . $c8_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="8" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="8" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query23 = "SELECT * FROM 1_game_index WHERE court_num = 8 AND status = 2";
            $info23 = $tournament_access->query($query23);
            if ($info23->num_rows > 0) {
              while ($court8_row23 = $info23->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court8_row23['round'] . '</p>';
                echo '</div>';
                $c8_second_player1 = get_user_data($court8_row23['draw_id_1']);
                $c8_second_player2 = get_user_data($court8_row23['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c8_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c8_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="8" data-player-number="2" player-name1="' . $c8_second_player1['user1_name'] . '" player-name2="' . $c8_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="8" data-player-number="2" player-belonging1="' . $c8_second_player1['user1_belonging'] . '" player-name1="' . $c8_second_player1['user1_name'] . '" player-belonging2="' . $c8_second_player2['user1_belonging'] . '" player-name2="' . $c8_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="8" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="8" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query24 = "SELECT * FROM 1_game_index WHERE court_num = 8 AND status = 3";
            $info24 = $tournament_access->query($query24);
            if ($info24->num_rows > 0) {
              while ($court8_row24 = $info24->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court8_row24['round'] . '</p>';
                echo '</div>';
                $c8_third_player1 = get_user_data($court8_row24['draw_id_1']);
                $c8_third_player2 = get_user_data($court8_row24['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c8_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c8_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="8" data-player-number="3" player-name1="' . $c8_third_player1['user1_name'] . '" player-name2="' . $c8_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="8" data-player-number="3" player-belonging1="' . $c8_third_player1['user1_belonging'] . '" player-name1="' . $c8_third_player1['user1_name'] . '" player-belonging2="' . $c8_third_player2['user1_belonging'] . '" player-name2="' . $c8_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="8" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="8" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court">
            <div class="court-title">
              <h3>9</h3>
            </div>
            <?php
            $query25 = "SELECT * FROM 1_game_index WHERE court_num = 9 AND status = 1";
            $info25 = $tournament_access->query($query25);
            if ($info25->num_rows > 0) {
              while ($court9_row25 = $info25->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court9_row25['round'] . '</p>';
                echo '</div>';
                $c9_first_player1 = get_user_data($court9_row25['draw_id_1']);
                $c9_first_player2 = get_user_data($court9_row25['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c9_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c9_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="9" data-player-number="1" player-name1="' . $c9_first_player1['user1_name'] . '" player-name2="' . $c9_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="9" data-player-number="1" player-belonging1="' . $c9_first_player1['user1_belonging'] . '" player-name1="' . $c9_first_player1['user1_name'] . '" player-belonging2="' . $c9_first_player2['user1_belonging'] . '" player-name2="' . $c9_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="9" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="9" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query26 = "SELECT * FROM 1_game_index WHERE court_num = 9 AND status = 2";
            $info26 = $tournament_access->query($query26);
            if ($info26->num_rows > 0) {
              while ($court9_row26 = $info26->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court9_row26['round'] . '</p>';
                echo '</div>';
                $c9_second_player1 = get_user_data($court9_row26['draw_id_1']);
                $c9_second_player2 = get_user_data($court9_row26['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c9_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c9_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="9" data-player-number="2" player-name1="' . $c9_second_player1['user1_name'] . '" player-name2="' . $c9_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="9" data-player-number="2" player-belonging1="' . $c9_second_player1['user1_belonging'] . '" player-name1="' . $c9_second_player1['user1_name'] . '" player-belonging2="' . $c9_second_player2['user1_belonging'] . '" player-name2="' . $c9_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="9" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="9" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query27 = "SELECT * FROM 1_game_index WHERE court_num = 9 AND status = 3";
            $info27 = $tournament_access->query($query27);
            if ($info27->num_rows > 0) {
              while ($court9_row27 = $info27->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court9_row27['round'] . '</p>';
                echo '</div>';
                $c9_third_player1 = get_user_data($court9_row27['draw_id_1']);
                $c9_third_player2 = get_user_data($court9_row27['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c9_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c9_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="9" data-player-number="3" player-name1="' . $c9_third_player1['user1_name'] . '" player-name2="' . $c9_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="9" data-player-number="3" player-belonging1="' . $c9_third_player1['user1_belonging'] . '" player-name1="' . $c9_third_player1['user1_name'] . '" player-belonging2="' . $c9_third_player2['user1_belonging'] . '" player-name2="' . $c9_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="9" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="9" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>

          <li class="court-right">
            <div class="court-title">
              <h3>10</h3>
            </div>
            <?php
            $query28 = "SELECT * FROM 1_game_index WHERE court_num = 10 AND status = 1";
            $info28 = $tournament_access->query($query28);
            if ($info28->num_rows > 0) {
              while ($court10_row28 = $info28->fetch_assoc()) {
                echo '<div class="court-player court-player1" data-player-number="1">';
                echo '<div class="court-round">';
                echo '<p>' . $court10_row28['round'] . '</p>';
                echo '</div>';
                $c10_first_player1 = get_user_data($court10_row28['draw_id_1']);
                $c10_first_player2 = get_user_data($court10_row28['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c10_first_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c10_first_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back1" data-player-number="1">';
                echo '<button class="result-button" court-num="10" data-player-number="1" player-name1="' . $c10_first_player1['user1_name'] . '" player-name2="' . $c10_first_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="10" data-player-number="1" player-belonging1="' . $c10_first_player1['user1_belonging'] . '" player-name1="' . $c10_first_player1['user1_name'] . '" player-belonging2="' . $c10_first_player2['user1_belonging'] . '" player-name2="' . $c10_first_player2['user1_name'] . '"  onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus" court-num="10" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus" court-num="10" data-player-number="1" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query29 = "SELECT * FROM 1_game_index WHERE court_num = 10 AND status = 2";
            $info29 = $tournament_access->query($query29);
            if ($info29->num_rows > 0) {
              while ($court10_row29 = $info29->fetch_assoc()) {
                echo '<div class="court-player court-player2" data-player-number="2">';
                echo '<div class="court-round">';
                echo '<p>' . $court10_row29['round'] . '</p>';
                echo '</div>';
                $c10_second_player1 = get_user_data($court10_row29['draw_id_1']);
                $c10_second_player2 = get_user_data($court10_row29['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c10_second_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c10_second_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back2" data-player-number="2">';
                echo '<button class="result-button" court-num="10" data-player-number="2" player-name1="' . $c10_second_player1['user1_name'] . '" player-name2="' . $c10_second_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="10" data-player-number="2" player-belonging1="' . $c10_second_player1['user1_belonging'] . '" player-name1="' . $c10_second_player1['user1_name'] . '" player-belonging2="' . $c10_second_player2['user1_belonging'] . '" player-name2="' . $c10_second_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus2" court-num="10" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus2" court-num="10" data-player-number="2" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>

            <?php
            $query30 = "SELECT * FROM 1_game_index WHERE court_num = 10 AND status = 3";
            $info30 = $tournament_access->query($query30);
            if ($info30->num_rows > 0) {
              while ($court10_row30 = $info30->fetch_assoc()) {
                echo '<div class="court-player court-player3" data-player-number="3">';
                echo '<div class="court-round">';
                echo '<p>' . $court10_row30['round'] . '</p>';
                echo '</div>';
                $c10_third_player1 = get_user_data($court10_row30['draw_id_1']);
                $c10_third_player2 = get_user_data($court10_row30['draw_id_2']);
                echo '<div>';
                echo '<div class="dragged-place">' . $c10_third_player1['user1_name'] . '</div>';
                echo '</div>';
                echo '<p>VS</p>';
                echo '<div>';
                echo '<div class="dragged-place">' . $c10_third_player2['user1_name'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="back back3" data-player-number="3">';
                echo '<button class="result-button" court-num="10" data-player-number="3" player-name1="' . $c10_third_player1['user1_name'] . '" player-name2="' . $c10_third_player2['user1_name'] . '" onclick="setCourtData(this)">結果入力</button>';
                echo '<button class="remove-button" court-num="10" data-player-number="3" player-belonging1="' . $c10_third_player1['user1_belonging'] . '" player-name1="' . $c10_third_player1['user1_name'] . '" player-belonging2="' . $c10_third_player2['user1_belonging'] . '" player-name2="' . $c10_third_player2['user1_name'] . '" onclick="setCourtData(this)">試合消去</button>';
                echo '</div>';
              }
            } else {
              echo '<div class="court-player court-plus3" court-num="10" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
              echo '<div class="back court-plus3" court-num="10" data-player-number="3" onclick="setCourtData(this)">';
              echo '<span class="dli-plus"></span>';
              echo '</div>';
            }
            ?>
          </li>
        </ul>
      </div>

      <!-- ここから試合追加モーダル -->
      <div id="mask" class="hidden"></div>
      <form action="/components/templates/operation_oop/addMatch.php" method="post">
        <div id="main__add" class="hidden">
          <span id="close">&times;</span>
          <div class="main__title">
            <h1>試合追加</h1>
          </div>
          <div class="main__player">
            <div class="main__player1">
              <h2>選手1</h2>
              <div class="main__name">
                <div class="add__name">選手名</div>
                <label class="main__select">
                  <select name="player__name">
                    <option disabled selected>選手名を選択してください</option>
                    <?php
                    $query1 = "SELECT * FROM 1_entrylist ORDER BY 'read' ASC";
                    $result = $tournament_access->query($query1);
                    if ($result) {
                      while ($court_row = $result->fetch_assoc()) {
                        echo '<option value="' . $court_row['draw_id'] . '">' . $court_row['user1_name'] . '</option>';
                      }
                    }
                    ?>
                  </select>
                </label>
              </div>
            </div>
            <div class="main__center">
              <label class="main__round">
                <select name="round">
                  <option value="1回戦">1回戦</option>
                  <option value="2回戦">2回戦</option>
                  <option value="3回戦">3回戦</option>
                  <option value="準々決勝">準々決勝</option>
                  <option value="準決勝">準決勝</option>
                  <option value="決勝">決勝</option>
                  <option value="コンソレ">コンソレ</option>
                </select>
              </label>
              <label class="main__category">
                <select name="category">
                  <option value="男子シングルス">男子シングルス</option>
                  <option value="女子シングルス">女子シングルス</option>
                </select>
              </label>
              <div id="vs">VS</div>
            </div>
            <div class="main__player2">
              <h2>選手2</h2>
              <div class="main__name">
                <div class="add__name">選手名</div>
                <div class="main__select">
                  <select name="player__name2">
                    <option disabled selected>選手名を選択してください</option>
                    <?php
                    $query = "SELECT * FROM 1_entrylist";
                    $result = $tournament_access->query($query);
                    while ($court_row = $result->fetch_assoc()) {
                      echo '<option value="' . $court_row['draw_id'] . '">' . $court_row['user1_name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="court-num" id="court-num-input1">
          <input type="hidden" name="data-player-number" id="data-player-number-input1">
          <div class="main__button">
            <button href="" type="submit">試合を追加</button>
          </div>
        </div>
      </form>
      <!-- ここまで試合追加モーダル -->
      <!-- エラーメッセージの表示 -->
      <script>
        window.addEventListener("load", () => {
          <?php
          if (isset($_SESSION['error'])) {
          ?>
            alert("<?php echo $_SESSION['error']; ?>");
          <?php
            unset($_SESSION['error']);
          }
          ?>
        });
      </script>

      <!-- ここから結果入力モーダル -->
      <div id="mask2" class="hidden"></div>
      <form action="/components/templates/operation_oop/addResult.php" method="post">
        <div id="result__add" class="hidden">
          <span id="close2">&times;</span>
          <div class="main__title">
            <h1>結果入力</h1>
          </div>
          <div class="result__player">
            <div class="result__player1">
              <div class="result__select1">
                <div name="player__name">
                  <div id="resultPlayer1"></div>
                </div>
              </div>
              <div class="result__select2">
                <select name="result__score1">
                  <option>スコアを選択してください</option>
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                </select>
              </div>
            </div>
            <div class="result__center">
              <div id="vs2">---</div>
              <div class="tiebreak">
                <input type="number" name="tiebreak" id="tiebreak" placeholder="tiebreak">
              </div>
            </div>
            <div class="result__player2">
              <div class="result__select1">
                <div name="player__name">
                  <div id="resultPlayer2"></div>
                </div>
              </div>
              <div class="result__select2">
                <select name="result__score2">
                  <option>スコアを選択してください</option>
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                </select>
              </div>
            </div>
          </div>
          <input type="hidden" name="court-num" id="court-num-input2">
          <input type="hidden" name="data-player-number" id="data-player-number-input2">
          <div class="result__button">
            <button href="" type="submit">入力完了</button>
          </div>
        </div>
      </form>
      <!-- ここまで結果入力モーダル -->

      <!-- ここから試合消去モーダル -->
      <div id="mask3" class="hidden"></div>
      <form action="/components/templates/operation_oop/removeMatch.php" method="post">
        <div id="main__remove" class="hidden">
          <span id="close3">&times;</span>
          <div class="main__title">
            <h1>消去前確認</h1>
          </div>
          <div class="remove__player">
            <div class="main__player1">
              <h2>選手1</h2>
              <div class="remove__belonging">
                <div class="remove__name">学校名</div>
                <label class="remove__select">
                  <div name="belonging__name">
                    <div id="removeBelonging1"></div>
                  </div>
                </label>
              </div>
              <div class="main__name">
                <div class="remove__name">選手名</div>
                <label class="remove__select">
                  <div name="player__name">
                    <div id="removePlayer1"></div>
                  </div>
                </label>
              </div>
            </div>
            <div class="remove__center">
              <div id="vs3">VS</div>
            </div>
            <div class="main__player2">
              <h2>選手2</h2>
              <div class="remove__belonging">
                <div class="remove__name">学校名</div>
                <label class="remove__select">
                  <div name="belonging__name">
                    <div id="removeBelonging2"></div>
                  </div>
                </label>
              </div>
              <div class="main__name">
                <div class="remove__name">選手名</div>
                <label class="remove__select">
                  <div name="player__name">
                    <div id="removePlayer2"></div>
                  </div>
                </label>
              </div>
            </div>
          </div>
          <input type="hidden" name="court-num" id="court-num-input3">
          <input type="hidden" name="data-player-number" id="data-player-number-input3">
          <div class="remove__button">
            <button href="" type="submit">試合を消去</button>
          </div>
        </div>
      </form>
      <!-- ここまで試合消去モーダル -->
    </div>
  </main>
  <div class="r_sidebar_menu">
      <?php
        require_once(dirname(__FILE__) . '/sidebar.php');
      ?>
  </div>
  <script src="/components/templates/operation_oop/operation_oop.min.js" onerror="handleError()"></script>
<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>