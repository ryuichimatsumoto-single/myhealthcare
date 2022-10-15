<?php 
ini_set("display_errors", On);
error_reporting(E_ALL);

//セッション開始
session_cache_limiter('nocache');
session_start();

//セッションが無い場合
if(!isset($_SESSION["user_id"]))
{
    // ログイン画面へ遷移させる
    header("location: login.php");
}

// 体重の最新20件を取得する
include('common_db/sql_queries.php');
$mysql_data = sqlQueries::get_latest_weight_data($_SESSION["user_id"]);

// 未入力の場合は-(ハイフン)を追加する
function outData($data)
{
    
    if($data == -1 || $data == "" || $data == null)
    {
        return "-";
    }
    
    return $data;
    
}

// 未入力の場合は-(ハイフン)を追加する
function outDataBold($data)
{
    
    if($data == -1 || $data == "" || $data == null)
    {
            return "-";
    }
    else 
    {
        return "<b>".$data."</b>";
    }    
    
}

?>
<!DOCTYPE html>
<html lang="ja">
<?php require_once 'common_design/head.php';?>
  <body>
  <?php require_once 'common_design/container.php';?>
  <div class="row marketing">
        <div class="col-lg-12">
        <h4>入力</h4>
        <form id="paidForm" name="iform" action="health_insert.php" method="POST">	
	<?php /*単に新規項目を入力するとき*/ ?>
	<?php if(!isset($_POST["user_health_id"])){ ?>
        <table class="table">
            <tr>
               <td>日時</td><td><input type="text" id="form_date" class="form_text" name="date" value="<?php echo date("Y-m-d");?>"></td>
            </tr>
            <tr>
               <td>最大血圧(mmHg)</td><td><input type="number" class="form_text" name="max_blood_pressure" maxlength="3" value="-1" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')"></td>
            </tr>
            <tr>
                <td>最小血圧(mmHg)</td><td><input type="number" class="form_text" name="min_blood_pressure" maxlength="3" value="-1" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')"></td>
            </tr>
            <tr>
                <td>脈拍数</td><td><input type="number" class="form_text" name="palse_late" maxlength="3" value="-1" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')"></td>
            </tr>
            <tr>
                <td>体重(Kg)</td><td><input type="number" class="form_text" name="weight" value="-1"></td>
            </tr>
            <tr>
                <td>体温(C°)</td><td><input type="number" class="form_text" name="temp" value="-1"></td>
            </tr>
            <tr>
               <td>送信</td><td><input type="button" value="保存する" onclick="formSubmit();" class="submit_button btn btn-success form_text"</td>
            </tr>         
        </table>

	<?php /*伝票内容を修正するとき*/ ?>
	<?php }else{ ?>
	<input type ="hidden" name="user_health_id" value="<?php echo $_POST["user_health_id"];?>">
        <table class="table">
            <tr>
               <td>記録番号</td><td><?php echo $_POST["user_health_id"];?></td>
            </tr>
            <tr>
               <td>日時</td><td><input type="text" id="form_date" class="form_text" name="date" value="<?php echo $_POST["date"];?>"></td>
            </tr>
            <tr>
               <td>最大血圧(mmHg)</td><td><input type="number" class="form_text" name="max_blood_pressure" maxlength="3" value="<?php echo $_POST["max_blood_pressure"];?>" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')"></td>
            </tr>
            <tr>
                <td>最小血圧(mmHg)</td><td><input type="number" class="form_text" name="min_blood_pressure" maxlength="3" value="<?php echo $_POST["min_blood_pressure"];?>" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')"></td>
            </tr>
            <tr>
                <td>脈拍数</td><td><input type="number" class="form_text" name="palse_late" maxlength="3" value="<?php echo $_POST["palse_late"];?>" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')"></td>
            </tr>
            <tr>
                <td>体重(Kg)</td><td><input type="number" class="form_text" name="weight" value="<?php echo $_POST["weight"];?>" onKeyUp="this.value=this.value.replace(/[^0-9.]+/,'')"></td>
            </tr>
            <tr>
                <td>体温(C°)</td><td><input type="number" class="form_text" name="temp" value="<?php echo $_POST["temp"];?>"  onKeyUp="this.value=this.value.replace(/[^0-9.]+/,'')"></td>
            </tr>
            <tr>
               <td>送信</td><td><input type="button" value="保存する" onclick="formSubmit();" class="submit_button btn btn-success"></td>
            </tr>
        </table>
	<?php } ?>

	</form>
       </div>
        <div class="col-lg-12">
          <h4>クイック修正</h4>
        <table class="table">
            <thead>
            <tr>
             <th class="mobile_disable">番号</th>
             <th>日時</th>
             <th class="right">最大血圧(mmHg)</th>
             <th class="right">最小血圧(mmHg)</th>
             <th class="mobile_disable right">脈拍数</th>
             <th class="right">体重(Kg)</th>
             <th class="right">体温(C°)</th>
             <th class="mobile_disable right">修正</th>
             <th  class="mobile_disable right">削除</th>
            </tr>

            <!-- 一覧表を生成する -->
            <?php foreach($mysql_data as $value){ ?>
            <tr class="graph">
               <td class="mobile_disable right"><?php echo $value['user_health_id']; ?></td>
               <td class="right"><?php echo date("n月j日",strtotime($value['date']));?></td>
               <td class="right"><?php echo outData($value['max_blood_pressure']); ?></td>
               <td class="right"><?php echo outData($value['min_blood_pressure']); ?></td>
               <td class="mobile_disable right"><?php echo outData($value['palse_late']); ?></td>
               <td class="right"><?php echo outDataBold($value['weight']); ?></td>
               <td class="right"><?php echo outData($value['temp']); ?></td>
               <td class="mobile_disable right">
                    <form id="updateForm" name="iform" action="health.php" method="POST">	
                        <input type ="hidden" name="user_health_id" value="<?php echo $value['user_health_id'];?>">
                        <input type ="hidden" name="date" value="<?php echo $value['date'];?>">
                        <input type ="hidden" name="max_blood_pressure" value="<?php echo $value['max_blood_pressure'];?>">
                        <input type ="hidden" name="min_blood_pressure" value="<?php echo $value['min_blood_pressure'];?>">
                        <input type ="hidden" name="palse_late" value="<?php echo $value['palse_late'];?>">
                        <input type ="hidden" name="weight" value="<?php echo $value['weight'];?>">
                        <input type ="hidden" name="temp" value="<?php echo $value['temp'];?>">
                        <input type ="submit" class="btn btn-warning" value="修正">
                    </form>
               </td>

               <td class="mobile_disable right">
                    <form action="health_delete.php" method="post">
                         <input type="hidden" name="user_health_id" value='<?php echo $value['user_health_id'];?>'>
                         <input type="submit" name="delete_button" class="delete_button btn btn-danger" value="削除" >
                    </form>
               </td>
            </tr>	
        <?php } ?>
    </table>
        </div>
      </div>

    </div> <!-- /container -->

 <script>
  $(function() {
    $( "#form_date" ).datepicker({dateFormat: 'yy-mm-dd'});
  });
  
  /*submit(Enter)の防止*/           
  $(function() 
  {
    $(document).on("keypress", "input:not(.allow_submit)", function(event) 
    {
        return event.which !== 13;
    });
  });

  /*未入力項目の確認*/
  /*コピー前のアプリに合った機能なため、とりあえず残しておく*/
  function formSubmit()
  {

	/*日付欄に不正な文字が入力された時*/
	var str = document.getElementById("form_date").value;

	/*yyyy-mm-dd hh:mm:ss形式の正規表現(練習)*/
	var validateRegPattern = /[0-9][0-9]{3}-[0-1][0-9]-[0-3][0-9]/;

	if(!str.match(validateRegPattern))
	{
		//空欄のアナウンスを流す
		alert("yyyy-mm-dd hh:mm:ss形式にて日付を入力して下さい");		

		//フォーカスを移す
		document.getElementById("form_date").focus();

		//処理を抜ける
		return false;
	}	

	//全部バリデーションが済んだらDBへデータを投げる
	$("#paidForm").submit();

  }  
  </script>
  <link href="common_css/health.css" rel="stylesheet">
  </body>
</html>
