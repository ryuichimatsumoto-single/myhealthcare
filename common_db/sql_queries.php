<?php

        class sqlQueries
	{
                /* MySQL接続情報 */
                /*練習/本番の切り替えが無いのでここに定義*/
                public static $url = "localhost";
                public static $user = "root";
                public static $pass = "123ryu1w";
                public static $db = "healthcare";
                public static $tableName = "trn_health";
            
		/*
    		* 最新20件分のデータを取得する
		* @user_id　ユーザーのID	
		*/
		public static function get_latest_weight_data($user_id)
		{
                    //(1):データーベースに接続(UNIX/Linuxの「mysql -u ユーザー名 -p　パスワード」に対応)    
                    $link = @mysqli_connect(self::$url,self::$user,self::$pass) or die("MySQLへの接続に失敗しました。");

                    //(2):データーベースの選択(MySQLの「use データーベース名」に対応)    
                    $sdb = mysqli_select_db($link,self::$db) or die("データベースの選択に失敗しました。");

                    //(3):SQL文をここで生成
                    $sql = "SELECT * FROM trn_health WHERE user_id = {$user_id} and final_flg = 0 order by id desc limit 20";

                    //(4):(3)で生成したSQL文をここで実行し、結果を配列に詰め込む
                    $result = mysqli_query($link,$sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
                    $data = array();//結果を詰め込む為の配列を定義
                    $i=0;//配列の添字

                    //(6):検索結果を1件ずつ配列に詰め込む、
                    //詰め込み終了と同時にポインタと添字を1ずつ進める。
                    //fetch_arrayはポインタなのでwhile文で対応
                    while($rows = mysqli_fetch_array($result))
                    {
                        $data[$i] = $rows;
                        $i++;
                    }

                    return $data;//最終的な結果を画面表示部分に渡す
		}                

		/*
		*DBの結果を配列で出力(件数指定版)
		*@引数　tblname:テーブル名	
		*/
		public static function get_monthly_weight_data($user_id,$start_date,$end_date)
		{
                    //(1):データーベースに接続(UNIX/Linuxの「mysql -u ユーザー名 -p　パスワード」に対応)    
                    $link = @mysqli_connect(self::$url,self::$user,self::$pass) or die("MySQLへの接続に失敗しました。");

                    //(2):データーベースの選択(MySQLの「use データーベース名」に対応)    
                    $sdb = mysqli_select_db($link,self::$db) or die("データベースの選択に失敗しました。");

                    //(4):SQL文をここで生成
                    $sql = "SELECT ";
                    $sql .= "    * ";
                    $sql .= "FROM ";
                    $sql .= self::$tableName." ";
                    $sql .= "WHERE ";
                    $sql .= "user_id = ".$user_id;
                    $sql .= " AND date >= "."'".$start_date."'";
                    $sql .= " AND date <= "."'".$end_date."'";
                    $sql .= " AND final_flg = 0 ";
                    $sql .= " order by date desc ";
                    
                    //(5):(4)で生成したSQL文をここで実行し、結果を配列に詰め込む
                    $result = mysqli_query($link,$sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
                    $data = array();//結果を詰め込む為の配列を定義
                    $i=0;//配列の添字

                    //(6):検索結果を1件ずつ配列に詰め込む、
                    //詰め込み終了と同時にポインタと添字を1ずつ進める。
                    //fetch_arrayはポインタなのでwhile文で対応
                    while($rows = mysqli_fetch_array($result))
                    {
                        $data[$i] = $rows;
                        $i++;
                    }

                    return $data;//最終的な結果を画面表示部分に渡す
		}                
                
                public static function insert_health($user_id,$date,$max_blood_pressure,$min_blood_pressure,$palse_late,$weight,$temp)
                {
                        $tblname = "trn_health";
                        //(1):データーベースに接続(UNIX/Linuxの「mysql -u ユーザー名 -p　パスワード」に対応)    
                        $link = @mysqli_connect(self::$url,self::$user,self::$pass) or die("MySQLへの接続に失敗しました。");

                        //(2):データーベースの選択(MySQLの「use データーベース名」に対応)    
                        $sdb = mysqli_select_db($link,self::$db) or die("データベースの選択に失敗しました。");
                        
                        // 新しい伝票番号を取得
                        $sql = "";
                        $sql = $sql."SELECT ";
                        $sql = $sql."      IFNULL((max(user_health_id)+1),1) as max_id ";
                        $sql = $sql."FROM ";
                        $sql = $sql.$tblname." ";
                        $sql = $sql."WHERE user_id = {$user_id}";

                        $result = mysqli_query($link,$sql) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
                        $row = mysqli_fetch_array($result);

                        $user_health_id = $row["max_id"];

                        // データーを入れなおす
                        $insert = "INSERT INTO " .self::$tableName." "; 
			$insert .="(user_id,user_health_id,final_flg,date,max_blood_pressure,min_blood_pressure,palse_late,weight,temp) VALUES (";
			$insert .=$user_id;
			$insert .=	" , ";
			$insert .=$user_health_id;
			$insert .=	" , ";
			$insert .=0;
			$insert .=	" , ";
			$insert .=$date;
			$insert .=	" , ";
                        $insert .=$max_blood_pressure;
			$insert .=	" , ";
			$insert .=$min_blood_pressure;
			$insert .=	" , ";
			$insert .= $palse_late;
			$insert .=	" , ";
			$insert .=$weight;
			$insert .=	" , ";
			$insert .=$temp;
                        $insert .=	")";
  			$result = mysqli_query($link,$insert) or die("空欄の可能性があります。<br />お手数ですがもう一度入力して下さい。<br />SQL:".$insert);
			return $result;		
		}			
                
                /*
		*入力データを論理削除
		*@引数　tblname:テーブル名	
		*/
		public static function delete_health($user_id,$user_health_id)
		{
                        //(1):データーベースに接続(UNIX/Linuxの「mysql -u ユーザー名 -p　パスワード」に対応)    
                        $link = @mysqli_connect(self::$url,self::$user,self::$pass) or die("MySQLへの接続に失敗しました。");
                        //(2):データーベースの選択(MySQLの「use データーベース名」に対応)    
                        $sdb = mysqli_select_db($link,self::$db) or die("データベースの選択に失敗しました。");

			//sql文
                        /*フラグ1にして伝票を論理削除する*/
                        $update = "";
                        $update = $update."UPDATE ".self::$tableName." "; 
			$update = $update." SET  final_flg = 1 ";
			$update = $update."WHERE user_id = ".$user_id;
			$update = $update." AND  user_health_id = ".$user_health_id;
                        $result = mysqli_query($link,$update) or die("SQL:".$update); 
                        
		//return $insert;		
		}                
                
                /*データを更新する*/
                public static function update_health($user_id,$user_health_id,$date,$max_blood_pressure,$min_blood_pressure,$palse_late,$weight,$temp)
                {
                        
                        //今までの伝票を削除する
                        self::delete_health($user_id,$user_health_id);
                                
                        //新たに伝票を起こして更新
                        self::insert_health($user_id,$date,$max_blood_pressure,$min_blood_pressure,$palse_late,$weight,$temp);
                        
		}			
                		

	}
?>