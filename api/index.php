<?php
error_reporting(0);
// 使用绝对路径
include(__DIR__ . '/config.php');

$db = pg_connect("host=$DB_HOST dbname=$DB_NAME user=$DB_USER password=$DB_PASS");
if (!$db) {
    die("无法连接到数据库：" . pg_last_error());
}

$randstr = GetRandStr($URL_SHORTENER_LENGHT);

if ($_GET['init'] == getenv("INIT_SQL_PASSWORD")) {
    echo init($db);
} elseif ($_GET['s']) {
    $url = $_GET['s'];
    $id = time();
    $query = "INSERT INTO url_data (id, url, code) VALUES ('$id', '$url', '$randstr')";
    $result = pg_query($db, $query);
    if ($_GET['type'] == "json"){
        $data = array(
            'code' => 200,
            'msg' => 'OK',
            'url' => "https://link.1v.fit/?j={$randstr}"
        )
        echo json_encode($data)
    }else{
        echo "成功！您的跳转链接为 https://link.1v.fit/?j={$randstr}";
    }
} elseif ($_GET['j']) {
    $code = pg_escape_string($_GET['jump']);
    $query = "SELECT url FROM url_data WHERE code = '$code'";
    $result = pg_query($db, $query);
    if (!$result) {
        die("不存在此链接！");
    }
    $row = pg_fetch_assoc($result);
    // echo $row['url'];
    header("Location: " . $row['url']);
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

function init($db)
{
    $query = "CREATE TABLE IF NOT EXISTS url_data (
        id serial PRIMARY KEY,
        url text NOT NULL,
        code text NOT NULL
    )";
    $result = pg_query($db, $query);
    return "成功初始化一个数据表，您可以开始提交链接了，注意此方法只能执行一遍！";
}
?>
