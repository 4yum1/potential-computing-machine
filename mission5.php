<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
    <style>
        .space {
            margin-bottom: 15px;
        }
        
        h1 {
            font-size: 25px;
        }
    </style>
</head>
<body>
    <?php

    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    // テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
    
    // テーブルの項目（カラム）
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY," // 投稿番号
    . "name varchar(32)," // 名前
    . "comment TEXT," // コメント
    . "date char(19)," // 日付
    . "pass varchar(32)" // パスワード
    .");";
    $stmt = $pdo -> query($sql);
    
    
    // 投稿日時
    $date = date("Y/m/d H:i:s"); // 2022/01/01 01:23:45
    
    
    // 編集機能
    if(!empty($_POST["editnum"])) {
        $editpass = $_POST["editpass"];
        
        if(empty($editpass)){
            echo "パスワードを入力してください<hr>";

        } else {
            $id = $_POST["editnum"];
            
            // 投稿番号とパスワードが一致する投稿のみ抽出
            $sql = 'SELECT * FROM mission5 WHERE id=:id AND pass=:pass';
            // ユーザの入力準備
            $stmt = $pdo -> prepare($sql);
            // ユーザが入力したidとpassをSQL文に代入
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt -> bindParam(':pass', $editpass, PDO::PARAM_INT);
            // SQL実行
            $stmt -> execute();
            // 抽出されたデータを配列にする
            $results = $stmt -> fetchAll();
            
            foreach ($results as $row){
                if($editpass == $row['pass']) {
                    $editdisplay = $row['id'];
                    $editName = $row['name'];
                    $editComment = $row['comment'];
                }
            }
        } 
        
        
    }
    
    
    // 投稿機能
    // 名前とコメントフォームの中身があるとき
    if(!empty($_POST["username"]) && !empty($_POST["comment"])) {
    
        $name = $_POST["username"];
        $comment = $_POST["comment"];
        
        $pass = $_POST["password"];
        $deletepass = $_POST["deletepass"];
        $editpass = $_POST["editpass"];
        
        // 編集番号表示フォームが空か？
        if(empty($_POST["editdisplay"])) {
            
        // 通常の投稿
            $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql -> execute();

            if(isset($_POST["submit"])) {
                echo "投稿が完了しました<hr>";
            }
            
        } else {

        // 編集モード
            $id = $_POST["editdisplay"];

            // 編集番号とidが一致する投稿の名前とコメントを更新
            $sql = 'UPDATE mission5 SET name=:name, comment=:comment, date=:date WHERE id=:id';
            // ユーザの入力準備
            $stmt = $pdo -> prepare($sql);
            // 編集番号表示フォームのid、ユーザが入力したname・comment、更新時のdateをSQL文に代入
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            // SQL実行
            $stmt -> execute();
            
        }
    
    // 名前入力フォームが空のとき
    } elseif (empty($_POST["username"]) && !empty($_POST["comment"])) {
        echo "名前を入力してください<hr>";

    // コメント入力フォームが空のとき
    } elseif (!empty($_POST["username"]) && empty($_POST["comment"])) {
        echo "コメントを入力してください<hr>";
    }
    
    
    // 削除機能
    if(!empty($_POST["deletenum"])) {
        $deletepass = $_POST["deletepass"];
        $id = $_POST["deletenum"];

        // パスワード入力欄が空の時
        if(empty($deletepass)) {
            echo "パスワードを入力してください<hr>";

        } else {
            // 投稿番号とパスワードが一致する投稿のみ削除
            $sql = 'delete from mission5 WHERE id=:id AND pass=:pass';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt -> bindParam(':pass', $deletepass, PDO::PARAM_STR);
            $stmt -> execute();

        }
    }
    
?>

<!--入力フォーム……この位置に置かないと、実行順的に$editNameや$editCommentが表示できない-->

<form action="" method="post">
    
    <!--投稿フォーム-->
    <input
        type="text" name="username" placeholder="名前"
        value="<?php
                if(isset($editName)) {
                    echo $editName;
                } ?>"><br>
             
    <input
        type="text" name="comment" placeholder="コメント"
        value="<?php 
                if(isset($editComment)) {
                    echo $editComment;
                } ?>"><br>

    <!--パスワード入力フォーム-->
    <input type="text" name="password" placeholder="パスワード">
    
    <!--新規投稿ボタン-->
    <input type="submit" name="submit" value="投稿" class="space"><br>


    <!--削除機能-->
    <input type="number" name="deletenum" placeholder="削除対象番号"><br>
    <!--パスワード入力フォーム-->
    <input type="text" name="deletepass" placeholder="パスワード">
    <!--削除ボタン-->
    <input type="submit" name="deletesub" value="削除" class="space"><br>
    

    <!--編集機能-->
    <input type="number" name="editnum" placeholder="編集対象番号"><br>
    <!--パスワード入力フォーム-->
    <input type="text" name="editpass" placeholder="パスワード">
    <!--編集ボタン-->
    <input type="submit" name="editsub" value="編集" class="space"><br>

    <!--編集番号表示機能 【完成時はtype="hidden"にする】-->
    <input
        type="hidden" name="editdisplay" placeholder="編集番号表示"
        value="<?php
                if(isset($editdisplay)) {
                    echo $editdisplay;
                } ?>">
</form>

<hr>
<h1>投稿一覧</h1>
    
<?php

    $sql = 'SELECT * FROM mission5';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    
    foreach ($results as $row){
        echo $row['id'] . " ";
        echo $row['name'] . " ";
        echo $row['comment'] . " ";
        // echo $row['pass'] . " ";    // 【確認用 完成時は消す】
        echo $row['date'] . '<br>';
    }
    echo "<hr>";

?>

</body>
</html>