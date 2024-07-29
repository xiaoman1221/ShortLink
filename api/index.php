<?php
// 必须使用绝对路径
include('/var/task/user/api/config.php');
$db = pg_connect("host=$DB_HOST dbname=$DB_NAME user=$DB_USER password=$DB_PASS");
if (!$db) {
    die("无法连接到数据库");
}
$randstr = GetRandStr($URL_SHORTENER_LENGHT);
if ($_GET['init'] == "1145141*1"){
    die(init($db));
}else{
    die("输入正确的密码！");
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
function init($db){
    $query = "CREATE TABLE `url_data` (
  `id` int NOT NULL,
  `url` text NOT NULL,
  `code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;";
    $result = pg_query($db, $query);
    return $result;
}