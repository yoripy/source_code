<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
  <?php
		if(empty($_POST['check'])){
		}else{
			$check=$_POST['check'];
			if($check=="true"){
				$inputfile=$_POST['file1'];
				$filename=$_POST['file2'];
				if (file_exists($inputfile)) {
					unlink($inputfile);
				}
				if (file_exists($filename)) {
					unlink($filename);
				}
			}
		}
  ?>
  <form method="post" action="php_get.php">
	ファイル名とコードを入力してください<br>
	<textarea spellcheck="false" name="inputTxt2" cols="50"></textarea><br>
	<textarea spellcheck="false" name="inputTxt" cols="50" rows="40"></textarea><br>
	<input type="submit" value="アセンブル">
	</form>
 </body>
</html>
