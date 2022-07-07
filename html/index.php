<?php

session_start();

//ログインしていない場合、login.phpを表示
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('db.php');
require_once('functions.php');

/**
 * @param String $tweet_textarea
 * つぶやき投稿を行う。
 */
 
 if ($_POST) { /* POST Requests */
     if (isset($_POST['logout'])) { //ログアウト処理
         logout();
         header("Location: login.php");
     } elseif (isset($_POST['tweet_textarea'])) { //投稿処理
         if (isset($_POST['reply_id'])) {
             newReplyTweet($_POST['tweet_textarea'], $_POST['reply_id']);
         } else {
             newtweet($_POST['tweet_textarea']);
         }
         header("Location: index.php");
     }
 }

function newtweet($tweet_textarea)
{
    // 汎用ログインチェック処理をルータに作る。早期リターンで
    createTweet($tweet_textarea, $_SESSION['user_id']);
}

function newReplyTweet($tweet_textarea, $reply_id)
{
    createReplyTweet($tweet_textarea, $_SESSION['user_id'], $reply_id);
}

/**
 * ログアウト処理を行う。
*/
function logout()
{
    $_SESSION = [];
    $msg = 'ログアウトしました。';
}

//返信かどうかの判定
//返信だった場合、返信フラグを立てる
$isReply = false;
if (isset($_GET['reply'])) {
    $isReply = true;
}
//Tweetsテーブルをもとに、Userテーブルを紐づけ、ユーザー名を取得する。
function getUserName($post_id)
{
    $sql = 'select u.name';
    $sql .=' from tweets t join users u on t.user_id = u.id';
    $sql .=' where t.id = :post_id';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $name = $stmt->fetch(PDO::FETCH_COLUMN);
    return $name;
}

function getUserReplyText($post_id)
{
    return "Re: @" . getUserName($post_id) . ' ';
}


$tweets = getTweets();
$tweet_count = count($tweets);
/* 返信課題はここからのコードを修正しましょう。 */
/* 返信課題はここからのコードを修正しましょう。 */
?>

<!DOCTYPE html>
<html lang="ja">

<?php require_once('head.php'); ?>

<body>
  <div class="container">
    <h1 class="my-5">新規投稿</h1>
    <div class="card mb-3">
      <div class="card-body">
        <form method="POST">
          
          <!-- 返信課題はここからのコードを修正しましょう。 -->
          <?php if ($isReply === true) { //Q：なんで===じゃないとうまくいかなかったのか？　?>
          <textarea class="form-control" type=textarea name="tweet_textarea" ?><?= getUserReplyText($_GET['reply']) ?></textarea>
          <input type="hidden" name="reply_id" value="<?= $_GET['reply'] ?>" />
           <?php
} else { //返信フラグが立っていない場合、テキストエリア＝空白、隠し項目なし?>
          <textarea class="form-control" type=textarea name="tweet_textarea" ?></textarea>
          <?php
} ?>
          <!-- 返信課題はここからのコードを修正しましょう。 -->
          <br>
          <input class="btn btn-primary" type=submit value="投稿">
        </form>
      </div>
    </div>
    <h1 class="my-5">コメント一覧</h1>
    <?php foreach ($tweets as $t) {
    ?>
      <div class="card mb-3">
        <div class="card-body">
          <p class="card-title"><b><?= "{$t['id']}" ?></b> <?= "{$t['name']}" ?> <small><?= "{$t['updated_at']}" ?></small></p>
          <p class="card-text"><?= "{$t['text']}" ?></p>
          <!--返信課題はここから修正しましょう。-->
          <p><a href = "index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a>
          <span> <?php  if (isset($t['reply_id'])) {
        ?>
          <a href="./view.php?id=<?= "{$t['reply_id']}" ?>">[返信元のメッセージ]</a></span>
          </p>
          <?php
    } ?> 
          <!--返信課題はここまで修正しましょう。-->
        </div>
      </div>
    <?php
} ?>
    <form method="POST">
      <input type="hidden" name="logout" value="dummy">
      <button class="btn btn-primary">ログアウト</button>
    </form>
    <br>
  </div>
</body>

</html>
