<?php
/*****データベースへの接続*****/
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
//接続の確認
try {
	$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_EMULATE_PREPARES => false));
} catch (PDOException $e) {
 	exit('データベース接続失敗。'.$e->getMessage());
}
//テーブルの作成
$sql = "CREATE TABLE mission4"
."("
."id INT,"
."name char(32),"
."comment TEXT,"
."now TEXT,"
."password TEXT"
.");";
$stmt = $pdo -> query($sql);

//編集投稿の取得
$edit_num = $_POST['edit_num'];
$edit_pw = $_POST['edit_pw'];
if (!empty($edit_num)&&!empty($edit_pw)) {
	$sql = "SELECT * FROM mission4 WHERE id = $edit_num";
	$result = $pdo -> query($sql);
	foreach ($result as $row) {
		if ($edit_pw == $row['password']) {
			$edit_flag = $edit_num;
			$edit_name = $row['name'];
			$edit_comment = $row['comment'];
			$edit_password = $row['password'];
		}
	}
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>mission_4</title>
</head>
<body style="font-family: 'メイリオ','Hiragino Kaku Gothic Pro',sans-serif; background-color: #f0f8ff">
	

<div class="top">
	<h1 style="font-size: 25px; color: #4682b4">Mission4</h1>
</div>

<div class="form">
	<div class="post">
		<h2 style="font-size: 20px; color: #666666">投稿フォーム</h2>
		<form action="http://tt-161.99sv-coco.com/mission_4.php" method="post" accept-charset="UTF-8">
			<p>
			名前：<input type="text" name="name" value="<?=$edit_name?>">
			コメント：<input type="text" name="comment" value="<?=$edit_comment?>">
			パスワード：<input type="text" name="password" value="<?=$edit_password?>">
			<input type="hidden" name="flag" value="<?=$edit_flag?>">
			<input type="submit" value="送信">
			</p>
		</form>
		<p>投稿する際は名前、コメント、パスワードの入力をしてください</p>
	</div>

	<div class="edit">
		<h2 style="font-size: 20px; color: #666666">編集フォーム</h2>
		<form action="http://tt-161.99sv-coco.com/mission_4.php" method="post" accept-charset="UTF-8">
			<p>
			編集対象番号：<input type="number" name="edit_num">
			パスワード：<input type="text" name="edit_pw">
			<input type="submit" value="編集">
			</p>
		</form>
		<p>削除する際は投稿番号、パスワードを入力してください</p>
	</div>

	<div class="delete">
		<h2 style="font-size: 20px; color: #666666">削除フォーム</h2>
		<form action="http://tt-161.99sv-coco.com/mission_4.php" method="post" accept-charset="UTF-8">
			<p>
			削除対象番号：<input type="number" name="del_num">
			パスワード：<input type="text" name="del_pw">
			<input type="submit" value="削除">
			</p>
		</form>
		<p>削除する際は投稿番号、パスワードを入力してください</p>
	</div>
</div>

<div class="threads">
<hr>
<?php
//変数
$name = $_POST['name'];
$comment = $_POST['comment'];
$password = $_POST['password'];
$now = date("Y/m/d H:i:s");
$del_num = $_POST['del_num'];
$del_pw = $_POST['del_pw'];
$flag = $_POST['flag'];
//条件式の簡略化
$threadIsSet = !empty($name)&&!empty($comment)&&!empty($password);

/*****保存*****/
if($threadIsSet&&empty($flag)) {
	//値
	$stmt = $pdo -> query("SELECT * FROM mission4");
	$id = $stmt -> rowCount() + 1;


	//データベースへ書き込み
	$sql = $pdo -> prepare("INSERT INTO mission4(id,name,comment,now,password) VALUES(:id,:name,:comment,:now,:password)");
	$sql -> bindParam(':id',$id,PDO::PARAM_INT);
	$sql -> bindParam(':name',$name,PDO::PARAM_STR);
	$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql -> bindParam(':now',$now,PDO::PARAM_STR);
	$sql -> bindParam(':password',$password,PDO::PARAM_STR);
	$sql -> execute();
}

/*****編集*****/
if($threadIsSet&&!empty($flag)) {
	$sql = "update mission4 set name='$name',comment='$comment' where id=$flag";
	$result = $pdo -> query($sql);
}

/*****削除*****/
if (!empty($del_num)&&!empty($del_pw)) {
	$sql = "SELECT * FROM mission4 WHERE id = $del_num";
	$result = $pdo -> query($sql);
	foreach ($result as $row) {
		if ($del_pw == $row['password']) {
			$sql = "delete from mission4 where id=$del_num";
			$result = $pdo ->query($sql);
			//投稿番号の更新
			$results = $pdo -> query("SELECT * FROM mission4 ORDER BY id");
			foreach ($results as $row) {
				if ($row['id'] > $del_num) {
					$num = $row['id'];
					$new_num = $row['id'] - 1;
					$sql = "update mission4 set id='$new_num' where id=$num";
					$result = $pdo -> query($sql);
				}
			}
			

		}
	}
}

/*****表示*****/
$results = $pdo -> query("SELECT * FROM mission4 ORDER BY id");
foreach ($results as $row) {
	echo $row['id'].'|';
	echo '投稿日時：'.$row['now'].'|';
	echo '投稿者：'.$row['name'].'|';
	echo $row['comment'].'<br>';
}


?>
</div>

</body>
</html>