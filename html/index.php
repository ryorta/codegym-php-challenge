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
function newtweet($tweet_textarea)
{
    // 汎用ログインチェック処理をルータに作る。早期リターンで
    createTweet($tweet_textarea, $_SESSION['user_id']);
}

//いいねを取り消すことでdbのfavoriteテーブルからデータを削除する関数を呼び出す。

/**
 * ログアウト処理を行う。
 */
function logout()
{
    $_SESSION = [];
    $msg = 'ログアウトしました。';
}

if ($_POST) { /* POST Requests */
    if (isset($_POST['logout'])) { //ログアウト処理
        logout();
        header("Location: login.php");
    } elseif (isset($_POST['tweet_textarea'])) { //投稿処理
        newtweet($_POST['tweet_textarea']);
        header("Location: index.php");
    }
}

/*GETの判定*/
if ($_GET) {
    if (isset($_GET['favorite'])) { //リクエストパラメータにfavoriteがあるか？
        if (isMyfavorite($_SESSION['user_id'], $_GET['favorite'])) {
            deleteFavorite($_SESSION['user_id'], $_GET['favorite']);
        } else {
            createFavorite($_SESSION['user_id'], $_GET['favorite']);
        }
        header("Location: index.php");
        exit; //これを入れるだけで削除して作り直すっていうのはなくなると思う。
        //if-elseを使うのも有り。
    }
}    //DBにあるか？該当の投稿IDがfavoriteテーブルにあるか？
    //ある
    //deleteを発行する関数を実行　関数（投稿ID,SESSION['user_id']）
    //リダイレクト
    //ない
    //insertを発行する関数を実行　関数（投稿ID,SESSION['user_id']）//createUserを参考
    //リダイレクト


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
          <textarea class="form-control" type=textarea name="tweet_textarea" ?></textarea>
          <!-- 返信課題はここからのコードを修正しましょう。 -->
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
          <!--dbにデータが入っているなら、特に「member_id」に値が入っている(つまり自分が押したいいね)ものには、赤いハートが表示されるようにする。
          そうでないなら灰色。-->
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
    
          <!--また、「post_id」にデータが入っているもの、つまり誰かしらによっていいねが押されている状態のものに関しては、ハートの横にいいね数(count($post_id=○○))を表示する。-->
          <!--返信課題はここから修正しましょう。-->
          <!--<p>[返信する] [返信元のメッセージ]</p>-->
          <!--返信課題はここまで修正しましょう。-->
        </div>
      </div>
    <?php
}
     ?><!--foreach ($tweets as $t)の終点-->
    <form method="POST">
      <input type="hidden" name="logout" value="dummy">
      <button class="btn btn-primary">ログアウト</button>
    </form>
    <br>
  </div>
</body>

</html>
