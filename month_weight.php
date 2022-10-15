<?php 
ini_set("display_errors", 1);
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

//　グラフカラー
$color1 = "#cccccc";
$color2 = "#999999";

// SQLクエリを読み込む
include('common_db/sql_queries.php');

// 月次データを格納する配列
$monthly_data = array();

// 検索欄に日付が入力されているとき
if(isset($_GET['start_date']) && isset($_GET['end_date']))
{
    // SQLからの検索結果を配列に格納する
    $monthly_data = sqlQueries::get_monthly_weight_data(
         $_SESSION["user_id"]
        ,$_GET['start_date']
        ,$_GET['end_date']
    );
}

// SQLの検索結果をグラフ用の配列にまとめる
// $columns 配列に詰めたいカラム名
function get_graph_data($monthly_data,$columns)
{
    $chart_data = null;

    //体重グラフ用のデータを生成
    if(count($monthly_data) != 0)
    {    
       $chart_data = "[";
       foreach($monthly_data as $value)
       { 
           if($value[$columns] != -1)
           {
               $chart_data .='["'.$value['date'].'",'.$value[$columns].'],';
           }
       }
       $chart_data = substr($chart_data,0,-1);
       $chart_data .= "]";
    }
    else
    {
       $chart_data = "[['',0]]";
    }

    return $chart_data;
}

?>
<!DOCTYPE html>
<html lang="ja">
<?php require_once 'common_design/head.php'; ?>
<body>
<?php require_once 'common_design/container.php';?>
<div class="row marketing">
    <!--日付検索欄-->
    <div class="col-lg-12">
    <h4>日付</h4>
    <form name="iform" action="month_weight.php" method="get">	
        <table class="table">
            <?php if(!isset($_GET["start_date"])){ ?>
            <tr>
                <td>検索開始日</td><td><input type="text" id="datepicker" name="start_date" value="0000-00-00"></td>
            </tr>
            <tr>
               <td>検索終了日</td><td><input type="text" id="datepicker2" name="end_date" value="0000-00-00"></td>
            </tr>
            <?php }else{ ?>
            <tr>
               <td>検索開始日</td><td><input type="text" id="datepicker" name="start_date" value="<?php echo $_GET["start_date"];?>"></td>
            </tr>
            <tr>
               <td>検索終了日</td><td><input type="text" id="datepicker2" name="end_date" value="<?php echo $_GET["end_date"];?>"></td>
            </tr>
            <?php } ?>

            <tr>
               <td colspan="2" style="text-align:center;"><input type="submit" value="検索" class="submit_button btn btn-success"></td>
            </tr>
        </table>
    </form>
    </div>
      
      <!--体重のグラフ-->  
      <div class="col-lg-12">
          <h4>体重</h4>
          <div id="jqPlot-weight"></div>
          <table class="table right">
            <tr>
              <td>日付</td>
              <td><span class="circle2"></span><b>体重</b></td>
            </tr>
           <?php foreach($monthly_data as $value){ ?>
           <?php if($value['weight'] != -1){ ?>
           <tr class="graph">
                <td><?php echo $value['date'];?></td>
                <td><b><?php echo $value['weight'];?></b></td>
           </tr>
           <?php } ?>           
        <?php }?>
        </table>
      </div>
      
      <!--血圧のグラフ-->
      <div class="col-lg-12">
          <h4>血圧</h4>
          <div id="jqPlot-blood"></div>
          <table class="table right">
           <thead>
            <tr>
              <td>日付</td>
              <td><span class="circle2"></span><b>最大血圧</b></td>
              <td><span class="circle1"></span><b>最低血圧</b></td>
              <td>差</td>
            </tr>
           </thead>
           <?php foreach($monthly_data as $value){ ?>
           <?php if($value['max_blood_pressure'] != -1 && $value['min_blood_pressure'] != -1){ ?>
           <tr class="graph">
                <td><?php echo $value['date'];?></td>
                <td><b><?php echo $value['max_blood_pressure'];?></b></td>
                <td><b><?php echo $value['min_blood_pressure'];?></b></td>
                <td><?php echo $value['max_blood_pressure']-$value['min_blood_pressure'];?></td>
           </tr>
           <?php }?>
        <?php }?>
        </table>
      </div>
      
      <!--脈拍のグラフ-->  
      <div class="col-lg-12">
          <h4>脈拍</h4>
          <div id="jqPlot-pulse"></div>
          <table class="table right">
            <tr>
              <td>日付</td>
              <td><span class="circle2"></span><b>脈拍</b></td>
            </tr>
           <?php foreach($monthly_data as $value){ ?>
           <?php if($value['palse_late'] != -1){ ?>
           <tr class="graph">
                <td><?php echo $value['date'];?></td>
                <td><b><?php echo $value['palse_late'];?></b></td>
           </tr>
           <?php } ?>
        <?php } ?>
        </table>
      </div>
      
</div> 


    <?php /*datepicker関連の設定 */ ?>
    <script>
    $(function() 
    {
      $( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
      $( "#datepicker2" ).datepicker({dateFormat: 'yy-mm-dd'});
    });
    </script>   

    <?php /*グラフ描画関連の設定 */ ?>
    <script>
      /*体重のグラフ*/
      $(document).ready(function()
      {
          // グラフに描画するデータ(配列)を変数に格納
          weight_data = <?php echo get_graph_data($monthly_data,"weight");?>;

          //  体重のグラグ
          graph_weight = $.jqplot( 'jqPlot-weight', [weight_data],
          {
              seriesColors: ["<?php echo $color2;?>"],
              axes:
              {
                  xaxis:
                  {
                      renderer: jQuery . jqplot . DateAxisRenderer,
                      min: '<?php echo $_GET['start_date'];?>',
                      max: '<?php echo $_GET['end_date'];?>',
                  }
              },
              grid: 
              {
                  // グラフを囲む枠線の太さ、0で消える
                  borderWidth: 1,
                  // 背景色を透明に
                  background: 'transparent',
                  // 影もいらない
                  shadow: true,
              }
          });

          //  血圧のグラフ
          maxPressure_data = <?php echo get_graph_data($monthly_data,"max_blood_pressure");?>;//最大血圧
          minPressure_data = <?php echo get_graph_data($monthly_data,"min_blood_pressure");?>;//最低血圧

          graph_blood = $.jqplot( 'jqPlot-blood', [minPressure_data,maxPressure_data],
          {
              seriesColors: ["<?php echo $color1;?>","<?php echo $color2;?>"],
              axes:{
                  xaxis:{
                      renderer: jQuery . jqplot . DateAxisRenderer,
                      min: '<?php echo $_GET['start_date'];?>',
                      max: '<?php echo $_GET['end_date'];?>',
                  }
              },
              grid: 
              {
                  // グラフを囲む枠線の太さ、0で消える
                  borderWidth: 1,
                  // 背景色を透明に
                  background: 'transparent',
                  // 影もいらない
                  shadow: true,
              }
          });

          //  脈拍のグラフ
          palse_data = <?php echo get_graph_data($monthly_data,"palse_late");?>;
          graph_pulse = $.jqplot( 'jqPlot-pulse', [palse_data],
          {
              seriesColors: ["<?php echo $color2;?>"],
              axes:{
                  xaxis:{
                      renderer: jQuery . jqplot . DateAxisRenderer,
                      min: '<?php echo $_GET['start_date'];?>',
                      max: '<?php echo $_GET['end_date'];?>',
                  }
              },
              grid: 
              {
                  // グラフを囲む枠線の太さ、0で消える
                  borderWidth: 1,
                  // 背景色を透明に
                  background: 'transparent',
                  // 影もいらない
                  shadow: true,
              }
          });

          // ウインドウをリサイズしたとき
          window.onresize = function(event) 
          {
              graph_weight.replot();
              graph_blood.replot();
              graph_pulse.replot();
          }
  });
  </script>
  <link href="common_css/month_weight.css" rel="stylesheet">
  </body>
</html>