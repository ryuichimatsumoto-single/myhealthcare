<?php
$fromTime = date('Y-m-d', strtotime('-2 week', time()));
$toTime = date('Y-m-d');

echo <<<EOD
        <div class="container">
        <ul class="nav nav-pills pull-right">
          <li><a href="health.php">入力</a></li>
          <li><a href="month_weight.php?start_date={$fromTime}&end_date={$toTime}">グラフ</a></li>
          <li><a href="logout.php">ログアウト</a></li>
        </ul>
        </div>
EOD;


?>
