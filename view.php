<?php

include('functions.php');//関数を使うためfunctions.phpをinclude


// var_dump($_POST);
// exit();

$name_search = $_POST['name_search'];
$category_search = $_POST['category_search'];

// var_dump($category_search);
// var_dump($name_search);
// exit();

// DB接続
$pdo = connect_to_db();//データベース接続の関数、$pdoに受け取る
      // 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


//$statusには実行結果が入るが，この時点ではまだデータ自体の取得はできていない点に注意
function trysql ($x){
  try {
    $status = $x->execute();
  } catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
  }
  // SQL実行の処理
  return  $x->fetchAll(PDO::FETCH_ASSOC); //fetchAll()関数でデータ自体を取得する．
}

//エラーを返すための変数
$error_massage = "";
// SQL作成&実行
if ($category_search != null && $name_search != null){
  $sql = 'SELECT * FROM fish_table WHERE category = :category_search AND fish_name LIKE :name_search ';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':category_search', $category_search, PDO::PARAM_STR);
  $stmt->bindValue(':name_search', '%'. $name_search . '%', PDO::PARAM_STR);
  //SQL実行の処理の関数
  $result = trysql($stmt);
} else if($category_search != null){
    $sql = 'SELECT * FROM fish_table WHERE category = :category_search';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':category_search', $category_search, PDO::PARAM_STR);
    //SQL実行の処理の関数
    $result = trysql($stmt);
    } else if ($name_search != null) {
      $sql = 'SELECT * FROM fish_table WHERE fish_name LIKE :name_search ';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':name_search', '%' . $name_search . '%', PDO::PARAM_STR);
      //SQL実行の処理の関数
      $result = trysql($stmt);
        }else {
          $error_massage = 'エラー';
        };


    // //取得したデータを確認
    // echo '<pre>';
    // var_dump($result);
    // echo '</pre>';
    // exit();

    function tide_check($x)
    {
      switch ($x) {
        case "oosio":
          return "大潮";
          break;
        case "nakasio":
          return "中潮";;
          break;
        case "kosio":
          return "小潮";
          break;
        case "nagasio":
          return "長潮";
          break;
        case "wakasio":
          return "若潮";
          break;
      }
    }

    //繰り返し処理を用いて，取得したデータから HTML タグを生成する．
    $output = "";
    foreach ($result as $record) {
    $tide = tide_check($record['tide']);

    $output .= "
    <div class=\"output_contents\">
      <div id=\"output{$record['id']}\" class =\"test\">
        <div>{$record['fish_name']}  {$record['input_date']}</div>
        <div class=\"infomation\">
          <div>{$record['category']}</div>
          <div>水深{$record['depth']}ｍ</div>
          <div>水温{$record['temp']}℃</div>
          <div>潮:{$tide}</div>
        </div>
      </div>
    <div>
  ";
    }

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FISH DATA</title>

  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <div id="wrapper">

      <h1>検索結果</h1>
    
      <div id="output_area">
        <?= $output ?>
        <?= $error_massage ?>
      </div>
      
    </div>


  <!-- jquery読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <script>
    $('.infomation').hide();

    $('body').on('click', '.test', function() {
      if ($(this).children('.infomation').hasClass('show')) {
        $(this).children('.infomation').slideUp();
        $(this).children('.infomation').removeClass('show');
      } else {
        $(this).children('.infomation').slideDown();
        $(this).children('.infomation').addClass('show');
      }
    });
  </script>

</body>

</html>