<?php
error_reporting(0);
// 必须使用绝对路径
include('/var/task/user/api/config.php');
$db = pg_connect("host=$DB_HOST dbname=$DB_NAME user=$DB_USER password=$DB_PASS");
if (!$db) {
    die("无法连接到数据库");
}
$randstr = GetRandStr($URL_SHORTENER_LENGHT);

if ($_GET['init'] == getenv("INIT_SQL_PASSWORD")) {
    // 处理初始化逻辑
    die(init($db));
} elseif ($_GET['submit']) {
    // 处理提交逻辑
    $query = "INSERT INTO url_data (id, url, code) VALUES (time(), ?, ?)";
    $stmt = pg_prepare($db, "insert_query", $query);
    $result = pg_execute($db, "insert_query", [$_GET['submit'], $randstr]);
    if ($result) {
        echo "成功！您的跳转链接为 http://test.com/jump=" . urlencode($randstr);
    } else {
        echo "插入数据失败！";
    }
} elseif ($_GET['jump']) {
    // 处理跳转逻辑
    $query = "SELECT url FROM url_data WHERE code = ?";
    $stmt = pg_prepare($db, "select_query", $query);
    $result = pg_execute($db, "select_query", [$_GET['jump']]);
    if ($result) {
        $row = pg_fetch_assoc($result);
        header('Location: ' . $row['url']);
        exit;
    } else {
        echo "不存在此链接！";
    }
}
function GetRandStr($length)
{
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $len = strlen($str) - 1;
    $randstr = '';
    for ($i = 0; $i < $length; $i++) {
        $num = mt_rand(0, $len);
        $randstr .= $str[$num];
    }
    return $randstr;
}
function init($db) {
    $query = "CREATE TABLE IF NOT EXISTS url_data (
        id serial PRIMARY KEY,
        url text NOT NULL,
        code text NOT NULL
    )";
    $result = pg_query($db, $query);
    return "成功初始化一个数据表，您可以开始提交链接了，注意此方法只能执行一遍！";
}
