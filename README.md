# 面接提出用プログラム
## 本プログラムについて
主に自分が普段使っている体重をグラフ化して表示するのプログラムです。  
自分が使えるところまで作ったため、バリデーションの未実装やバグも多くあリます。

## ログインページ
https://www.rm1213.work/healthcare_test/login.php

## ID/パスワード
test/test

# 操作の大まかな流れ
ログイン画面にてログインをおこないます。  
入力画面に移動しますので、最高血圧、最低血圧、脈拍数、体重、を入力し、「入力する」ボタンを押すと入力が行われます。  

＊　未入力の項目は「-1」のままで大丈夫です。  
＊　バリデーションを実装していないため、入力欄が空欄だとエラーとなりますのでご了承ください。  

ある特定の期間(例1月1日から1月31日)までの体重の推移を知りたい場合は「グラフ」タブをクリックして下さい。  

それぞれ日付を選択し「検索」を押すと、グラフが出てきます。  

＊入力されたデータは、一定期間が経過したら物理削除(truncate)を行い、データが残らないようにいたします。  
＊動作確認の際、架空の情報を入力してくださいますようお願いいたします。  

# プログラムについて

## サーバー
ConoHa VPS(1コア/30GB/メモリ512MB). 

## 使用ミドルウェア
Linux:CentOS8.   
Apache2.4.37.    
MariaDB 10.3.28.  
PHP7.4.22. 

## 説明
本プログラムは、 主に画面とデータベースとのデータのやり取り(CRUD)のみを実装したものです。  

Webフレームワークを使っておりません。  
面接担当者の皆さんが素早く確認やコードレビューができるように、主なファイルの場所を説明いたします。  

画面(フロント) : myhealthcare/health.php  など。  
DB(CRUD) : myhealthcare/common_db/sql_queries.php など。  

# プログラムのバグや未実装の箇所

## セキュリテイ
1.ログイン時のパスワードは平文となっており、暗号化されておりません。  
myhealthcare/login.php  :: 43行目以降。

2.空欄時の処理やバリデーションは実装していません。  

3.DBのデータ取得失敗時などの例外処理。  
