<?php 
ini_set("display_errors", On);
error_reporting(E_ALL);
//ログインしているかどうかのセッションを追加する
session_cache_limiter('nocache');
session_cache_expire(10);
session_start();
include('common_db/sql_queries.php');

/**/
$error_msg = "";
$error_msg_name = "";
$error_msg_password = "";
$error_flg = 0;

if(!empty($_POST))
{        
        if($_POST["name"] == "" || $_POST["name"] == null)
        {
            $error_msg_name = "名前を入力してください"; 
            $error_flg = 1;
        }    
        
        if($_POST["password"] == "" || $_POST["password"] == null)
        {
            $error_msg_password = "パスワードを入力してください"; 
            $error_flg = 1;
        }        
}        

if(!empty($_POST))
{
    if($error_flg == 0)
    {
            //(1):データーベースに接続(UNIX/Linuxの「mysql -u ユーザー名 -p　パスワード」に対応)    
            $link = @mysqli_connect(sqlQueries::$url,sqlQueries::$user,sqlQueries::$pass) or die("MySQLへの接続に失敗しました。");

            //(2):データーベースの選択(MySQLの「use データーベース名」に対応)    
            $sdb = mysqli_select_db($link,sqlQueries::$db) or die("データベースの選択に失敗しました。");

            //文字列をエスケープ処理する
            $name = mysqli_real_escape_string($link,$_POST["name"]."");
            $password = mysqli_real_escape_string($link,$_POST["password"]."");

            //パスワードに合致するユーザー数を調べる
            $sql = "";
            $sql = $sql. "SELECT ";
            $sql = $sql. "      count(*) as cnt ";
            $sql = $sql. "FROM ";        
            $sql = $sql. "      users ";
            $sql = $sql. "WHERE name = \"".$_POST["name"]."\"";
            $sql = $sql. " AND  password = \"".$_POST["password"]."\"";

            //SQLを実行する
            $result = mysqli_query($link,$sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

            //データを1件取得する
            $row = mysqli_fetch_array($result);

            //パスワードに合致しないユーザー又はパスワードの場合
            if($row["cnt"] == 0)
            {
                $error_msg = "メールアドレスまたはパスワードが違うか、<br />存在しないアカウントです。";
                $error_msg_name = "";
                $error_msg_password = "";            
            }
            //パスワードに合致するユーザーの場合
            else
            {
                //パスワードに合致するユーザー数を調べる
                $sql = "";
                $sql = $sql. "SELECT ";
                $sql = $sql. "      * ";
                $sql = $sql. "FROM ";        
                $sql = $sql. "      users ";
                $sql = $sql. "WHERE name = \"".$_POST["name"]."\"";
                $sql = $sql. " AND  password = \"".$_POST["password"]."\"";

                // SQLを実行する
                $result = mysqli_query($link,$sql) or die("クエリの実行に失敗しました。<br />SQL:".$sql);

                // データを1件取得する
                $row = mysqli_fetch_array($result);

                // ユーザー情報を入れ込む
                $_SESSION["user_id"] = $row["id"];

                // paid.phpへ遷移させる
                header("location: health.php");
            }   
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">    
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
    <!-- Tabs Titles -->

    <!-- Icon -->
    <div class="fadeIn first">
      <h2>ログイン</h2>
    </div>

    <!-- Login Form -->
    <?php /*ログイン前の場合*/ ?>
    <?php if(!isset($_POST)){ ?>
    <form method="post" action="login.php">
      <input type="text" id="login" class="fadeIn second" name="name" placeholder="メールアドレス">
      <input type="password" id="password" class="fadeIn third" name="password" placeholder="パスワード">
      <input type="submit" class="fadeIn fourth" value="ログイン">
    </form>
    <?php /*ログイン後の場合*/ ?>
    <?php }else{ ?>
    <form method="post" action="login.php">
      <?php if($error_msg != "" && $error_msg != null){ ?>
          <p style="color:red;"><?php echo $error_msg;?></p>  
      <?php } ?>        
      <input type="text" id="login" class="fadeIn second" name="name" placeholder="メールアドレス">
      <?php if($error_msg_name != "" && $error_msg_name != null){ ?>
          <p style="color:red;"><?php echo $error_msg_name;?></p>  
      <?php } ?>        
      <input type="password" id="password" class="fadeIn third" name="password" placeholder="パスワード">
      <?php if($error_msg_password != "" && $error_msg_password != null){ ?>
          <p style="color:red;"><?php echo $error_msg_password;?></p>  
      <?php } ?>        
      <input type="submit" class="fadeIn fourth" value="ログイン">
    </form>
    <?php } ?>
  </div>
</div>
    <link href="common_css/login.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  </body>
</html>
