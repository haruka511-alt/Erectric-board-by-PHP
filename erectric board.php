<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>5-1</title>
  <style>
  * {
    background-color: #f5f5f5;
    font-family: 'Noto Sans CJK JP';
  }
  .add, .delete, .modify {
    display: flex;
  }
  .modify {
    margin-bottom: 100px;
  }
  </style>
</head>
<body>

<!-- DB関係 -->
<?php
// DB接続設定
$dsn = 'データベース名';
$user = 'ユーザ名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

// <!-- ブラウザ表示関係 -->
  $edit = $_POST['edit'];
  // 初期値設定
  $name_edit = '';
  $comment_edit = '';
  $edit_num = '';
  $pass_edit='';
  
  //   追加処理
  function addition(){
    if(!empty($_POST["comment"]) && !empty($_POST["name"]) && !empty($_POST["password1"])){
        global $pdo;
        $sql = $pdo -> prepare("INSERT INTO MUSIC (name, comment, pass) VALUES (:name, :comment, :pass)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $name = $_POST['name'];//歌手名
        $comment = $_POST['comment']; //曲名
        $pass = $_POST['password1'];//パスワード
        $sql -> execute();
      }else{
          echo '入力されていない項目があります。';
      }
  }

    // 削除処理
  function delete_line(){
      global $pdo, $results;
      $sql = 'SELECT * FROM MUSIC';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach($results as $row){
        $storedpass = $row['pass'];
        if($_POST['password2']==$storedpass){
          $id = $_POST['num'];
          $sql = 'delete from MUSIC where id=:id';
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          $stmt->execute();
        }
      }
  }
  
  if(isset($_POST['bt1'])){
    //隠しボックス'now'に値が入っている場合
    if(!empty($_POST['now'])){
      //   テキストファイル編集処理②
      // 'MUSIC'のレコード編集
      $id = $_POST['now']; //変更する投稿番号 
      $name = $_POST['name'];
      $comment = $_POST['comment']; 
      $pass = $_POST['password1']; 
      $sql = 'UPDATE MUSIC SET name=:name,comment=:comment,pass=:pass WHERE id=:id';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
      $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      }else{
        // 隠しボックス'now'が空だった場合
        addition();
      }
  }

  //   テキストファイル削除処理
  elseif(isset($_POST['bt2'])){
    if(!empty($_POST['num'])&&!empty($_POST['password2'])){
      delete_line();
    } else {
      echo '未入力の項目があります';
    }        
  }
  //   テキストファイル編集処理①
  elseif(isset($_POST['bt3'])){
    // 編集番号ボックスに値が入っていた時
    if(!empty($_POST['edit'])){
      // select文でデータベースからレコードを抽出
      $sql = 'SELECT * FROM MUSIC';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach($results as $row){
        $storedId = $row['id'];
        $storedName = $row['name'];
        $storedComment = $row['comment'];
        $storedPass = $row['pass'];
        if($_POST['edit']==$storedId){
          // 編集パスワードと保存パソワードが一致した場合
          // inputにレコードを反映
          if($_POST['password3']==$storedPass){
          $name_edit = $storedName;
          $comment_edit = $storedComment;
          $pass_edit = $storedPass;
          }
        }
      } 
    }  
  }
  ?>

  <h1>好きな曲をおすすめしあうスレッド</h1>
  <h2>余力があれば愛も語ってください。</h2>  
  
  <form action="" method="post">
  <div class='add'>
  <label for="id_name">歌手名:</label>
  <input type="text" id='id_name' name='name' value=<?= $name_edit ?>>
  <br>
  <label for="id_comment">曲名:</label>
  <input type="text" id='id_comment' name='comment' value=<?= $comment_edit ?>>
  <br>
  <label for="id_password1">パスワード:</label>
  <input type="password" name="password1" id="id_password1" value=<?= $pass_edit ?>>
  <br>
  <input type="submit" name='bt1'>
  </div>
  <br>
  <br>
  <div class="delete">
  <label for="id_num">削除するスレッド番号:</label>
  <input type="number" id='id_num' name='num'>
  <br>
  <label for="id_password2">パスワード:</label>
  <input type="password" name="password2" id="id_password2">
  <br>
  <input type="submit" value='削除' name='bt2'>
  </div>
  <br>
  <br>
  <div class="modify">
  <label for="id_edit">編集するスレッド番号:</label>
  <input type="number" id='id_edit' name='edit'>
  <br>
  <label for="id_password3">パスワード:</label>
  <input type="password" name="password3" id="id_password3">
  <input type="submit" value='編集' name='bt3'>
  <br>
  <!--編集確認番号ボックス（利用者からは隠す）-->
  <!-- <label for="id_now">編集中のスレッド番号:</label> -->
  <input type='hidden' id='id_now' name='now' value=<?= $edit ?>>
  </div>
</form>
  

<!-- // ブラウザ反映処理 -->
<?php
 // テーブルレコード抽出、表示
$sql = 'SELECT * FROM MUSIC';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      //$rowの中にはテーブルのカラム名が入る
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].'<br>';
      echo $row['time'].'<br>';
      // echo $row['pass'].'<br>';
      echo "<hr>";
    }
?>
  
</body>
</html>