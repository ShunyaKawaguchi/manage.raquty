<?php
//データベース認証
require_once(dirname(__FILE__).'/connect-databese.php');
//共通Function(PHP)を読み込み
require_once(dirname(__FILE__).'/../components/common/global-function.php');
//ユーザーidがDB上にあるか確認（アカウント削除時対策）
require_once(dirname(__FILE__).'/have_user_id.php');
//投稿表示関数を読み込み
require_once(dirname(__FILE__).'/load-functions.php');
//投稿の有無を確認
require_once(dirname(__FILE__).'/post-existance-confirmation.php');
// headタグ内呼び出し
require_once(dirname(__FILE__) .'/../components/common/structure/head/head.php') ;
//テンプレートを取得して投稿を表示
require_once(dirname(__FILE__) . '/../components/templates/' . $template . '/' . $template . '.php');
//データベース接続解除
require_once(dirname(__FILE__).'/disconnect-database.php');
?>