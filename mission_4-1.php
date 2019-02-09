<?PHP
//DB接続
$dsn='mysql:dbname=DATABASE;host=localhost';
$user='USERNAME';
$password='PASSWORD';
$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

//テーブル作成（新規・編集・削除）
$cre="CREATE TABLE IF NOT EXISTS create9"
."("
."id INT,"
."name char(32),"
."comment TEXT,"
."pass TEXT,"
."date DATETIME"
.");";
$stmt=$pdo->query($cre);

//入力

$cnt=$pdo->prepare("SELECT COUNT(*)FROM create9");
$cnt->execute();
$id=($cnt->fetchColumn()+1);
$name=$_POST['name'];
$comment=$_POST['comment'];
$pass=$_POST['pas'];
$date=date("Y/m/d H:i:s");

if(!empty($name) && (!empty($comment))){
  if(empty($pass)){
    $pass="0000";
$cre=$pdo->prepare("INSERT INTO create9 (id,name,comment,pass,date)VALUES(:id,:name,:comment,:pass,:date)");
$cre->bindParam(':id',$id,PDO::PARAM_INT);
$cre->bindParam(':name',$name,PDO::PARAM_STR);
$cre->bindParam(':comment',$comment,PDO::PARAM_STR);
$cre->bindParam(':pass',$pass,PDO::PARAM_STR);
$cre->bindParam(':date',$date,PDO::PARAM_STR);
$cre->execute();
    echo $name."さんのコメントを受け付けました<br>";
    echo "パスワードが入力されなかったため'0000'に設定されました";
  }else{
$cre=$pdo->prepare("INSERT INTO create9 (id,name,comment,pass,date)VALUES(:id,:name,:comment,:pass,:date)");
$cre->bindParam(':id',$id,PDO::PARAM_INT);
$cre->bindParam(':name',$name,PDO::PARAM_STR);
$cre->bindParam(':comment',$comment,PDO::PARAM_STR);
$cre->bindParam(':pass',$pass,PDO::PARAM_STR);
$cre->bindParam(':date',$date,PDO::PARAM_STR);
$cre->execute();
    echo $name,"さんのコメントを受け付けました<br>";
    echo "パスワードは".$pass."です<br>";
  }
}

//編集表示
$edtnum=$_POST['edtnum'];
$edtnam=$_POST['edtnam'];
$edtcom=$_POST['edtcom'];
$edtpas=$_POST['edtpas'];
$edtbtn=$_POST['edtbtn'];
$prsnum=$_POST['prsnum'];
$prsnam=$_POST['prsnam'];
$prscom=$_POST['prscom'];
if($_POST['prsbtn']){
  if(!empty($edtnum)){
    $prs='SELECT*FROM create9';
    $stmt=$pdo->query($prs);
    $results=$stmt->fetchAll();
      foreach($results as $row){
        if($row['id']==$edtnum){
          $prsnum=$row['id'];
          $prsnam=$row['name'];
          $prscom=$row['comment'];
        }
      }

  }else{
    echo "番号を入力してください";
  }
}

//編集記入
if($edtbtn){
  if(!empty($edtnum) && (!empty($edtnam)) && (!empty($edtcom)) && (!empty($edtpas))){
    $prs='SELECT*FROM create9';
    $stmt=$pdo->query($prs);
    $results=$stmt->fetchAll();
    foreach($results as $row){
      if($row['id']==$edtnum){
        if($row['pass']==$edtpas){
//各項目ごと編集。一度には更新できないよ。
        $edt1='update create9 set name =:name where id=:id';
        $stmt=$pdo->prepare($edt1);
        $stmt->bindParam(':name',$edtnam,PDO::PARAM_STR);
        $stmt->bindParam(':id',$edtnum,PDO::PARAM_INT);
        $stmt->execute();
        $edt='update create9 set comment =:comment where id=:id';
        $stmt=$pdo->prepare($edt);
        $stmt->bindParam(':comment',$edtcom,PDO::PARAM_STR);
        $stmt->bindParam(':id',$edtnum,PDO::PARAM_INT);
        $stmt->execute();
          echo "編集内容を反映しました";
        }else{
          echo "パスワードがちゃいまっせ";
        }
      }
    }
  }else{
    echo "編集内容を記入してください";
  }
}

//削除
$delnum=$_POST['delnum'];
$delpas=$_POST['delpas'];
$j=1;
if($_POST['delbtn']){
if(!empty($delnum) && (!empty($delpas))){
//パスワード一致作業
$prs='SELECT*FROM create9 ORDER BY id ASC';
$stmt=$pdo->query($prs);
//file関数の働きfetch
$results=$stmt->fetchAll();
  foreach($results as $row){
    if($row['id']==$delnum){
      if($row['pass']==$delpas){
    //削除の動作
      $del='delete from create9 where id=:id';
      $stmt=$pdo->prepare($del);
      $stmt->bindParam(':id',$delnum,PDO::PARAM_INT);
      $stmt->execute();
      echo "消したで！";
      }else{
        echo "パスワードが違います";
      }
    }else{
      $k=$j++;
      $rownum=$row['id'];
      $udt='update create9 set id =:newid where id=:id';
      $stmt=$pdo->prepare($udt);
      $stmt->bindParam(':newid',$k,PDO::PARAM_INT);
      $stmt->bindParam(':id',$rownum,PDO::PARAM_STR);
      $stmt->execute();

    }
  }
}else{
  echo "番号またはパスワードが入力されていません";
}
}
?>
<html>
  <head>
    <meta charset='UTF-8'>
      <title>Mission 4-1</title>
  </head>
  <body>
    <h1>掲示板 with MySQL</h1>
      <hr>
  <p>入力フォーム</p>
  <form method="POST" action="mission_4-1.php">
  <p>名前：
  <input type="text" name="name"></p>
  <p>コメント：
  <input type="text" name="comment"></p>
  <p>パスワード：
  <input type="text" name="pas" autocomplete="off"></p>
  <input type="submit" name="crebtn" value="送信"><br>
  </form>
      <hr>
  <p>編集フォームSTEP①</p>
  <form action="" method="POST">
  <p>投稿番号：
  <input type="number" name="edtnum" value="<?PHP echo $prsnum ?>">
  <input type="submit" name="prsbtn" value="投稿内容を表示"></p>
      <hr>
  <p>編集フォームSTEP②</p>
  <p>名前:
  <input type="text" name="edtnam" value="<?PHP echo $prsnam ?>"></p>
  <p>コメント：
  <input type="text" name="edtcom" value="<?PHP echo $prscom ?>"></p>
  <p>パスワード：
  <input type="text" name="edtpas" autocomplete="off"></p>
  <input type="submit" name="edtbtn" value="編集"><br>
  </form>
      <hr>
  <p>削除フォーム</p>
  <form action="" method="POST">
  <p>削除番号:
  <input type="number" name="delnum"></p>
  <p>パスワード：
  <input type="text" name="delpas" autocomplete="off"></p>
  <input type="submit" name="delbtn" value="削除"><br>
  </form>
      <hr>
<font size="5">コメント欄</font>
      <hr>
  </body>
</html>
<?PHP
$cre='SELECT*FROM create9 ORDER BY id ASC';
$stmt=$pdo->query($cre);
$res=$stmt->fetchAll();
  foreach($res as $row){
    echo "投稿番号：".$row['id']."　"."名前：".$row['name']."　"."コメント：".$row['comment']."　"."投稿時間：".$row['date']."<br>\n";
  }
  echo "<hr>";

?>
