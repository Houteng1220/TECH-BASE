<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php
            //PHPでPOSTデータを受け取り
            @$name=htmlspecialchars($_POST["name"],ENT_QUOTES);
            @$comment=htmlspecialchars($_POST["comment"],ENT_QUOTES);
            @$display=$_POST["display"];
            @$delete=$_POST["delete"];
            @$edit=$_POST["edit"];
            @$pass=htmlspecialchars($_POST["pass"],ENT_QUOTES);
            @$edipass=htmlspecialchars($_POST["edipass"],ENT_QUOTES);
            @$delpass=htmlspecialchars($_POST["delpass"],ENT_QUOTES);
            
            //データベースに接続
            $dsn = 'データベース名'; //データベース名とホスト名
            $user = 'ユーザー名'; //ユーザー名
            $password = 'パスワード'; //パスワード
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            //テーブルの作成
            $sql = "CREATE TABLE IF NOT EXISTS mission_5_1" //テーブル名
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "date char(32),"
            . "pass char(32)"
            .");";
            $stmt = $pdo->query($sql);
            
            //データを入力（新規投稿）
            if(!empty($name)&&!empty($comment)&&!empty($pass)&&empty($display)){
                $date = date("Y/m/d H:i:s");
                $sql = $pdo -> prepare("INSERT INTO mission_5_1 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $sql -> execute(); //実行する
            }
            
            //編集選択機能
            if(!empty($edit)&&!empty($edipass)){ //編集対象番号とパスワードが送信されたとき
                $sql = 'SELECT * FROM mission_5_1'; // *は全てという意味。mission_5_1に入っているデータ全てを取り出す
                $stmt = $pdo->query($sql); //接続したデータベースの$sqlを取り出して、テーブルの全ての中身を取得する。
                $results = $stmt->fetchAll();
                
                foreach ($results as $row){ //$resultに入っているもの(テーブル)を$rowへ格納
                    //$rowの添字（［］内）にはテーブルのカラム名が入る
                    if($row['id'] == $edit && $row['pass'] == $edipass){ //投稿番号と編集対象番号が同じでパスワードも同じ場合
                        $ename = $row['name'];
                        $ecomment = $row['comment'];
                    }
                }
            }
            
            //編集実行機能
            if(!empty($name)&&!empty($comment)&&!empty($pass)&&!empty($display)){
                $date = date("Y/m/d H:i:s");
                $id = $_POST["display"];
                $sql = 'UPDATE mission_5_1 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute(); //実行する
            }
            
            //削除機能
            if(!empty($delete)&&!empty($delpass)){
                $id = $_POST["delete"];
                $pass = $_POST["delpass"];
                $sql = 'delete from mission_5_1 where id=:id AND pass=:pass';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_INT);
                $stmt->execute();
            }
            
        ?>
        <form action="" method="post">
            <h3>【投稿フォーム】</h3>
            <input type="text" name="name" placeholder="名前" value="<?php if(!empty($ename)){echo $ename;} ?>"><br>
            <input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($ecomment)){echo $ecomment;} ?>"><br>
            <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" name="submit" value="送信"><br>
            <input type="hidden" name="display" value="<?php if(!empty($edit) && $row['id']==$edit){echo $edit;} ?>"><br>
            <h3>【削除フォーム】</h3>
            <input type="number" name="delete" placeholder="削除対象番号"><br>
            <input type="text" name="delpass" placeholder="パスワード">
            <input type="submit" name="submit" value="削除"><br><br>
            <h3>【編集フォーム】</h3>
            <input type="number" name="edit" placeholder="編集対象番号"><br>
            <input type="text" name="edipass" placeholder="パスワード">
            <input type="submit" name="submit" value="編集"><br><br>
            <h3>【　投稿一覧　】</h3>
        </form>
        <?php
            //表示機能
            $sql = 'SELECT * FROM mission_5_1';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            echo "<hr>";
            }
            
        ?>
    </body>
</html>