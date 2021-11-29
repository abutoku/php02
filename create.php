<?php

//-----------------データ受け取り側では以下の処理を実装する----------------------------//

//必須項目の入力チェック
//データの受け取り
////DB 接続
//SQL 作成&実行
//SQL 実行後の処理

//-------------------------------------------------------------------------------------//

//POSTデータ確認
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
// exit();

if (
  !isset($_POST['name']) || $_POST['name'] == '' ||
  !isset($_POST['category']) || $_POST['category'] == ''||
  !isset($_POST['depth']) || $_POST['depth'] == ''||
  !isset($_POST['temp']) || $_POST['temp'] == ''||
  !isset($_POST['tide']) || $_POST['tide'] == ''||
  !isset($_POST['date']) || $_POST['date'] == ''
) {
  exit('ParamError'); //エラーを返す
}

// データの受け取り
$fish_name = $_POST['name'];
$category = $_POST['category'];
$depth = $_POST['depth'];
$temp = $_POST['temp'];
$tide = $_POST['tide'];
$input_date = $_POST['date'];

// var_dump($name);
// var_dump($category);
// var_dump($depth);
// var_dump($temp);
// var_dump($tide);
// var_dump($date);
// exit();

// -------------------------DB接続の必要な項目----------------------------------------//

// mysql：DB の種類（他にPostgreSQL，Oracle Database，などが存在する）
// dbname：DB の名前（今回はここをdec_todoに設定する！）
// port：接続ポート
// host：DB のホスト名
// username：DB 接続時のユーザ名
// password：DB 接続時のパスワード

// ----------------------------------------------------------------------------------//

// 各種項目設定
$dbn = 'mysql:dbname=gsacf_l06_10;charset=utf8;port=3306;host=localhost';
$user = 'root';
$pwd = '';

// DB接続
try {
  $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
  echo json_encode(["db error" => "{$e->getMessage()}"]);
  exit();
}

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．

//------------SQL（今回は INSERT 文）を実行する場合も手順--------------//

//1.SQL 文の記述．
//2.バインド変数の設定．
//3.SQL 実行．
//4.（SQL 実行に失敗した場合はエラーメッセージを出力する）

//---------------------------------------------------------------------//


$sql = 'INSERT INTO fish_table (id,fish_name,category,depth,temp,tide,input_date,created_at, updated_at) VALUES (NULL,:fish_name,:category,:depth,:temp,:tide,:input_date,now(), now())';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':fish_name', $fish_name, PDO::PARAM_STR);
$stmt->bindValue(':category', $category, PDO::PARAM_STR);
$stmt->bindValue(':depth', $depth, PDO::PARAM_STR);
$stmt->bindValue(':temp', $temp, PDO::PARAM_STR);
$stmt->bindValue(':tide', $tide, PDO::PARAM_STR);
$stmt->bindValue(':input_date', $input_date, PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
//ユーザが入力した値を SQL 文内で使用する場合には必ずバインド変数を使用すること．

// SQL実行の処理
header('Location:index.php'); //SQL が正常に実行された場合は，データ入力画面に移動
exit();
