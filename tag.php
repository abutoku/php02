<?php

include('functions.php'); //関数を使うためfunctions.phpをinclude

// var_dump($_GET);
// exit();

$tag_name = $_GET['value'];

// var_dump($tag_name);
// exit();

// DB接続
$pdo = connect_to_db();

$sql = 'SELECT * FROM fish_table WHERE tag = :tag_name ORDER BY input_date DESC';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetchAll()関数でデータ自体を取得する．

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




