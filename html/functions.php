<?php
/**
 * @param string $name ユーザー名
 * @return PDOStatement ユーザー情報の連想配列を格納したPDOStatement
 * 名前を元にユーザー情報を取得します。
 */
function getUserByName($name)
{
    $sql = 'select * from users where name = :name';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string $name ユーザー名
 * @param string $$password_hash ユーザーパスワードハッシュ値
 * @return bool 成功・失敗
 */
function createUser($name, $password_hash)
{
    $sql = 'insert into users (name, password_hash, created_at, updated_at)';
    $sql .= ' values (:name, :password_hash, :created_at, :updated_at)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    return $stmt->execute();
}

/**
 * @param string $text 投稿内容
 * @param string $user_id ユーザーID
 * @return bool 成功・失敗
 */
function createTweet($text, $user_id)
{
    $sql = 'insert into tweets (text, user_id, created_at, updated_at)';
    $sql .= ' values (:text, :user_id, :created_at, :updated_at)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    return $stmt->execute();
}

/**
 * @return PDOStatement ユーザー情報の連想配列を格納したPDOStatement
 * 投稿の一覧を取得します。
 */
function getTweets()
{
    $sql = 'select t.id, t.text, t.user_id, t.created_at, t.updated_at, t.reply_id, u.name';
    $sql .= ' from tweets t join users u on t.user_id = u.id';
    $sql .= ' order by t.updated_at desc';
    $stmt = getPdo()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTweet($id)
{
    $sql = 'select t.id, t.text, t.user_id, t.created_at, t.updated_at, t.reply_id, u.name';
    $sql .=' from tweets t join users u on t.user_id = u.id';
    $sql .=' where t.id = :id';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createReplyTweet($text, $user_id, $reply_id)
{
    $sql = 'insert into tweets (text, user_id, created_at, updated_at, reply_id)';
    $sql .= ' values (:text, :user_id, :created_at, :updated_at, :reply_id)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
    return $stmt->execute();
}

function createFavorite($member_id, $post_id)
{
    $sql = 'insert into favorites (member_id, post_id, created_at, updated_at)';
    $sql .= ' values (:member_id, :post_id, :created_at, :updated_at)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_STR);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    return $stmt->execute();
}

function deleteFavorite($member_id, $post_id)
{
    $sql = 'delete from favorites';
    $sql .= ' where member_id = :member_id and post_id = :post_id';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * @param string $user_id ユーザー名
 * @return array 自分がいいねした投稿IDの配列
 */
function isMyfavorite($member_id, $post_id)
{
    $sql = 'select post_id';
    $sql .= ' from favorites';
    $sql .= ' where member_id = :member_id and post_id = :post_id';
    $sql .= ' order by updated_at desc';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return (0 < count($data));
}

function countFavorites($post_id)
{
    $sql = 'select post_id';
    $sql .= ' from favorites';
    $sql .= ' where post_id = :post_id';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $fav = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return count($fav);
}
