<?php
//Operation関係のページならglobal-cssを上書きするcssを読み込む
//必ずglobal-csが読み込まれた後に関数を発動する

function overwrite_global_css($template) {
    if($template == 'operation' || $template == 'operation_oop'|| $template == 'operation_tournament'|| $template == 'operation_court_situation' || $template == 'tournament_draw_edit'){
        echo '<link rel="stylesheet" href="/components/common/overwrite_global-css.min.css">';
    }
}