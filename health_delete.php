<?php
header("Content-type:text/html;charset=UTF-8");
include('common_db/sql_queries.php');

session_start();

//セッションが無い場合、誤登録を防ぐために強制ログアウトする
if(!isset($_SESSION["user_id"]))
{
    // ログイン画面へ遷移させる
    header("location: logout.php");
}

$user_id = $_SESSION['user_id'];
$user_health_id = $_POST['user_health_id'];

/*このタイミングでMySQLを動かす！*/
$data_delete = sqlQueries::delete_health($user_id,$user_health_id);

header("HTTP/1.1 301 Moved Permanently");
header("Location: health.php");
?>