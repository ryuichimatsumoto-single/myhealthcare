<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
header("Content-type:text/html;charset=UTF-8");
include('common_db/sql_queries.php');

/*************************************/
/*   各種必要な情報群(POST情報)	     　*/
/*************************************/
session_start();

//セッションが無い場合、誤登録を防ぐために強制ログアウトする
if(!isset($_SESSION["user_id"]))
{
    // ログイン画面へ遷移させる
    header("location: logout.php");
}

$user_id = $_SESSION["user_id"];
$user_health_id = (isset($_POST["user_health_id"])) ? $_POST["user_health_id"]:0;
$date = '"'.$_POST['date'].'"';
$max_blood_pressure = $_POST['max_blood_pressure'];
$min_blood_pressure = $_POST['min_blood_pressure'];
$palse_late = $_POST['palse_late'];
$weight = $_POST['weight'];
$temp = $_POST['temp'];

//データー初入力時
if(!isset($_POST["user_health_id"]))
{
    $suucess_flg = sqlQueries::insert_health(
         $user_id
        ,$date
        ,$max_blood_pressure
        ,$min_blood_pressure
        ,$palse_late,$weight
        ,$temp
    );
}
//データーを更新する時
else
{
    $suucess_flg = sqlQueries::update_health(
         $user_id
        ,$user_health_id
        ,$date
        ,$max_blood_pressure
        ,$min_blood_pressure
        ,$palse_late
        ,$weight
        ,$temp
    );
}

header("HTTP/1.1 301 Moved Permanently");
header("Location: health.php");

?>
