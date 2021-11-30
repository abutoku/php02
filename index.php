<?php

include('functions.php'); //関数を使うためfunctions.phpをinclude


//------------処理の流れ----------------------------------------------------//

//表示ファイル（todo_read.php）へアクセス時，DB 接続する．
//データ参照用 SQL 作成 → 実行．
//取得したデータを HTML に埋め込んで画面を表示．

//------------処理の流れ----------------------------------------------------//

// DB接続
$pdo = connect_to_db(); //データベース接続の関数、$pdoに受け取る
// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．



// SQL作成&実行

//今回は「ユーザが入力したデータ」を使用しないのでバインド変数は不要．
$sql = 'SELECT * FROM fish_table ORDER BY input_date DESC';
$stmt = $pdo->prepare($sql);

//$statusには実行結果が入るが，この時点ではまだデータ自体の取得はできていない点に注意．
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$sql_2 = 'SELECT * FROM tag_table ORDER BY created_at DESC';
$stmt_2 = $pdo->prepare($sql_2);

//$statusには実行結果が入るが，この時点ではまだデータ自体の取得はできていない点に注意．
try {
  $status_2 = $stmt_2->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// SQL実行の処理
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetchAll()関数でデータ自体を取得する．
$tag = $stmt_2->fetchAll(PDO::FETCH_ASSOC); //fetchAll()関数でデータ自体を取得する．

//取得したデータを確認
// echo '<pre>';
// var_dump($tag);
// echo '</pre>';
// exit();

//DBのtideを照合
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
  $tide = tide_check($record['tide']); //DBのtideを照合する関数を実行
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

$tag_btn = "";
foreach ($tag as $record) {
  $tag_btn .= "
    <div id=\"btn_{$record['tag_id']}\">
      <a href=tag.php?value={$record["tag_name"]}><button value=\"{$record['tag_name']}\">{$record['tag_name']}</button></a>
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

    <h1 id="top_h1">FISH DATA</h1>

    <div id="main">

      <div id="input_section">
        <!-- 登録部分 -->
        <form action="create.php" id="input_form" method="post">
          <!-- 魚の名前を入力 -->
          <div id="name_contents">
            <p>name</p>
            <input type="text" name="name" id="fish_name" required>
          </div>
          <!-- 科を入力 -->
          <select name="category" id="category" required></select>
          <!-- 水深を入力 -->
          <div id="depth_contents">
            <p>水深</p>
            <input type="number" name="depth" id="depth" min="0" max="40" value="15" required>
          </div>
          <!-- 水温を入力 -->
          <div id="temp_contents">
            <p>水温</p>
            <input type="number" name="temp" id="temp" min="-20" max="40" value="23" required>
          </div>
          <!-- 潮を選択 -->
          <select name="tide" id="tide" required>
            <option disabled selected value>潮を選択</option>
            <option value="oosio">大潮</option>
            <option value="nakasio">中潮</option>
            <option value="kosio">小潮</option>
            <option value="nagasio">長潮</option>
            <option value="wakasio">若潮</option>
          </select>
          <!-- 日付を入力 -->
          <div id="date_contents" required>
            <input type="date" name="date"><br>
          </div>

          <div id="add">タグを追加</div>

          <div id="tag_contents">
            <input type="text" name="tag"><br>
          </div>

          <!-- 登録ボタン -->
          <button id="send_btn" type="submit" onClick="return run();">登録</button>
        </form>
        <!-- 登録部分ここまで -->

        <div id="tag_contents">
          <div>
            <h2>タグ一覧</h2>
          </div>
          <div id="tag_output">
            <?= $tag_btn ?>
          </div>
        </div>


      </div>
      <!-- input_sectionここまで -->
      <div id="search_section">

        <!-- 検索部分 -->
        <form action=" view.php" method="post" id="search_form">
          <!-- 名前を入力 -->
          name:<input type="text" name="name_search" id="name_search">
          <!-- 科を選択 -->
          <select name="category_search" id="category_search"></select>
          <!-- 潮を選択 -->
          <!-- <select name="tide_search" id="tide_search">
            <option disabled selected value>潮を選択</option>
            <option value="oosio">大潮</option>
            <option value="nakasio">中潮</option>
            <option value="kosio">小潮</option>
            <option value="nagasio">長潮</option>
            <option value="wakasio">若潮</option>
          </select> -->
          <!-- 検索ボタン -->
          <button id="send_btn">検索</button>

        </form>
        <!-- 検索部分ここまで -->

        <!-- 出力部分 -->

        <div id="output_area">
          <?= $output ?>
        </div>

      </div>
      <!-- search_sectionここまで -->
    </div>
    <!-- mainここまで -->
  </div>
  <!-- wrapperここまで -->

  <!-- jquery読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <script>
    // function run() {
    //   if (!document.getElementById("fish_name").checkValidity) {
    //     return true;
    //   }
    // }

    //科の選択を作成するための配列
    const categoryArray = [
      "ベラ科",
      "ハタ科",
      "スズメダイ科",
      "ハゼ科",
      "フグ科",
      "フサカサゴ科",
      "イサキ科",
      "チョウチョウオ科",
      "アジ科",
      "ヨウジウオ科",
      "テンジクダイ科",
      "カワハギ科",
    ];

    //タグ付のための配列
    const tagArray = [];

    //繰り返し処理ための配列
    categoryArray.forEach((x) => {
      tagArray.push(`<option value="${x}">${x}</option>`);
    });
    tagArray.unshift(`<option disabled selected value>科を選択</option>`);

    //selectタグの中に作成
    $('#category').html(tagArray);
    $('#category_search').html(tagArray);

    //名前と日付以外は隠しておく
    $('.infomation').hide();

    //名前がクリックされたら詳細を表示
    $('body').on('click', '.test', function() {
      if ($(this).children('.infomation').hasClass('show')) {
        $(this).children('.infomation').slideUp();
        $(this).children('.infomation').removeClass('show');
      } else {
        $(this).children('.infomation').slideDown();
        $(this).children('.infomation').addClass('show');
      }
    });

    $('#tag_contents').hide();

    $('#add').on('click', function() {
      if ($('#tag_contents').hasClass('show')) {
        $('#tag_contents').hide();
        $('#tag_contents').removeClass('show');
      } else {
        $('#tag_contents').show();
        $('#tag_contents').addClass('show');
      }
    });

  </script>

</body>

</html>