<?php

//DB接続のための関数
//new PDO($dbn, $user, $pwd)を返す


function connect_to_db()
{
  // 各種項目設定
  $dbn = 'mysql:dbname=gsacf_l06_10;charset=utf8mb4;port=3306;host=localhost';
  $user = 'root';
  $pwd = '';

  // DB接続
  try {
    return new PDO($dbn, $user, $pwd);
  } catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
  }
}

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．
