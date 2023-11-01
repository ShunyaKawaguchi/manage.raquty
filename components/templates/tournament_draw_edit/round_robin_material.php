<?php 
function generate_RoundRobin($num,$type,$nonce_id){ 
    $draw_num = 1; 
    $draw_num_2 = 1;    
?>
    <table>
        <thead>
            <tr>
                <th class="Non"></th>
                <?php for($i= 1;$i<=$num;$i++){ ?>
                <?php
                    $player_data = getParticipants($draw_num,$_GET['tournament_id'],$_GET['child_event_id']);
                    echo '<th class="player_section_wrap"><div class="player_section"><span style="width:20px;text-align:center;">'.$draw_num.'</span>';
                    if(!empty($player_data)){;?>
                    <form action='/components/templates/tournament_draw_edit/draw_delete.php' method='post'>
                        <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'];?>">
                        <input type="hidden" name="child_event_id" value="<?php echo $_GET['child_event_id'];?>">
                        <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id;?>">
                        <input type="hidden" name="draw_num" value="<?php echo $draw_num;?>">
                        <input type="submit" value="<<">
                    </form>
                
                    <?php if($type == 'シングルス'):?>
                        <div class="player_data">
                            <?php echo $player_data['user1_name'];?> ( <?php echo $player_data['user1_belonging'];?> )
                        </div>
                        <?php elseif($type == 'ダブルス'):?>
                        <div class="player_data">
                            <?php echo $player_data['user1_name'];?> ( <?php echo $player_data['user1_belonging'];?> )<br>
                            <?php echo $player_data['user2_name'];?> ( <?php echo $player_data['user2_belonging'];?> )
                        </div>
                        <?php endif;
                    }
                    echo '</div></th>';
                    ?>
                <?php $draw_num++; ?>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php for($j= 1;$j<=$num;$j++){ ?>
                <?php
                    $player_data = getParticipants($draw_num_2,$_GET['tournament_id'],$_GET['child_event_id']);
                    echo '<th class="player_section_wrap"><div class="player_section"><span style="width:20px;text-align:center;">'.$draw_num_2.'</span>';
                    if(!empty($player_data)){;?>
                    <form action='/components/templates/tournament_draw_edit/draw_delete.php' method='post'>
                        <input type="hidden" name="tournament_id" value="<?php echo $_GET['tournament_id'];?>">
                        <input type="hidden" name="child_event_id" value="<?php echo $_GET['child_event_id'];?>">
                        <input type="hidden" name="raquty_nonce2" value="<?php echo $nonce_id;?>">
                        <input type="hidden" name="draw_num" value="<?php echo $draw_num_2;?>">
                        <input type="submit" value="<<">
                    </form>
                
                    <?php if($type == 'シングルス'):?>
                        <div class="player_data">
                            <?php echo $player_data['user1_name'];?> ( <?php echo $player_data['user1_belonging'];?> )
                        </div>
                        <?php elseif($type == 'ダブルス'):?>
                        <div class="player_data">
                            <?php echo $player_data['user1_name'];?> ( <?php echo $player_data['user1_belonging'];?> )<br>
                            <?php echo $player_data['user2_name'];?> ( <?php echo $player_data['user2_belonging'];?> )
                        </div>
                        <?php endif;
                    }
                    echo '</div></td>';
                    ?>
                    <?php for($k= 1;$k<=$num;$k++){ ?>
                        <?php if($j == $k):?>
                            <td class="Non"></td>
                        <?php else:?>
                            <td></td>
                        <?php endif;?>
                    <?php } ?>
                </tr>
                <?php $draw_num_2++; ?>
            <?php } ?>
        </tbody>
    </table>

<?php }