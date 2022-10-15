<?php
echo <<<EOD
      <div class="footer">
        <ul class="nav">
          <li><a href="health.php">入力</a></li>
          <li><a href="month_weight.php?start_date={$fromTime}&end_date={$toTime}">グラフ</a></li>
          <li><a href="logout.php">ログアウト</a></li>
        </ul>
      </div>
EOD;
?>