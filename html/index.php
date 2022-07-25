<?php

session_start();

//ログインしていない場合、login.phpを表示
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('db.php');
require_once('functions.php');

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
         exit;
     }
 }
 
/**
 * @param String $tweet_textarea
 * つぶやき投稿を行う。
 */
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

$isReply = false;
if (isset($_GET['reply'])) {
    $isReply = true;
}

if ($_GET) {
    if (isset($_GET['favorite'])) {
        if (isMyfavorite($_SESSION['user_id'], $_GET['favorite'])) {
            deleteFavorite($_SESSION['user_id'], $_GET['favorite']);
        } else {
            createFavorite($_SESSION['user_id'], $_GET['favorite']);
        }
        header("Location: index.php");
        exit;
    }
}

$tweets = getTweets();
$tweet_count = count($tweets);

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
          
          <?php if ($isReply === true) {
    ?>
          <textarea class="form-control" type=textarea name="tweet_textarea" ?><?= getUserReplyText($_GET['reply']) ?></textarea>
          <input type="hidden" name="reply_id" value="<?= $_GET['reply'] ?>" />
           <?php
} else {
        ?>
          <textarea class="form-control" type=textarea name="tweet_textarea" ?></textarea>
          <?php
    } ?>
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
        <?php
          if (isMyfavorite($_SESSION['user_id'], $t['id'])) {
              ?>
          <a href="index.php?favorite=<?= "{$t['id']}" ?>"><img class="favorite-image" src='/images/heart-solid-red.svg'></a>
        <?php
          } else {
              ?>
          <a href="index.php?favorite=<?= "{$t['id']}" ?>"><img class="favorite-image" src='/images/heart-solid-gray.svg'></a>
        <?php
          }
        echo countFavorites($t['id']); ?> 
    
          <p><a href = "index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a>
          <span> <?php  if (isset($t['reply_id'])) {
            ?>
          <a href="./view.php?id=<?= "{$t['reply_id']}" ?>">[返信元のメッセージ]</a></span>
          </p>
          <?php
        } ?> 
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
