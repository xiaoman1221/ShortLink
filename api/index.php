<?php
//数据库名
$DB_HOST=getenv("POSTGRES_HOST");
//数据库名
$DB_NAME=getenv("POSTGRES_DATABASE");
//数据库登录用户名
$DB_USER=getenv("POSTGRES_USER");
//数据库登录密码
$DB_PASS=getenv("POSTGRES_PASSWORD");
//几秒后跳转（等于0为关闭跳转页面）
$JUMP_TIME=1;
//站点名称
$TITLE="云电短链";
//生成短链的长度
$URL_SHORTENER_LENGHT=8;

$db = pg_connect("host=$DB_HOST dbname=$DB_NAME user=$DB_USER password=$DB_PASS");

if (!$db) {
    die("无法连接到数据库");
}
print(GetRandStr($URL_SHORTENER_LENGHT));

$query = "SELECT username, email FROM user";
$result = pg_query($db, $query);
print($result);

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