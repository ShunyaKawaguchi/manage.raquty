<?php
//呼び出し
require_once(dirname(__FILE__).'/../../common/structure/sidebar/sidebar.php') ;
//必要機能呼び出し
require_once(dirname(__FILE__).'/material.php') ;
//アラート
alert('operation_err');
?>

<div class="Tournament_List">
    <div class="page_title"><button style="margin-right:20px;" onclick="window.location.href='<?php echo home_url('Tournament'); ?>'">&lt;&lt;</button>大会一覧</div>
    <div class="main_contents">
        <!-- <div class="search_tournament">
            <div class="sub_title">大会検索</div>

        </div> -->
        <div class="tournament_list_value">
            <div class="sub_title">大会一覧</div>
            <div class="wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>大会名称</th>
                            <th>期間</th>
                            <th>受付状況</th>
                            <th>公開状況</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php get_group_tournament_list( $_SESSION['group_id'] ) ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
//フッター呼び出し(フッター → /body → /html まで)
require_once(dirname(__FILE__).'/../../common/structure/footer/footer.php') ;
?>