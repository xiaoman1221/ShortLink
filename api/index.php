<?php
error_reporting(0);
// 使用绝对路径
include(__DIR__ . '/config.php');

$db = pg_connect("host=$DB_HOST dbname=$DB_NAME user=$DB_USER password=$DB_PASS");
if (!$db) {
    die("无法连接到数据库：" . pg_last_error());
}

if ($_GET['init'] == getenv("INIT_SQL_PASSWORD")) {
    echo init($db);
} elseif ($_GET['s']) {
    $url = pg_escape_string($_GET['s']);
    
    // 确保 URL 包含 http 或 https 前缀
    if (!preg_match('/^(http:\/\/|https:\/\/)/', $url)) {
        $url = 'https://' . $url;
    }
    
    // 先检查 URL 是否已经存在
    $query = "SELECT code FROM url_data WHERE url = '$url'";
    $code_result = pg_query($db, $query);
    
    if ($code_result && pg_num_rows($code_result) > 0) {
        // URL 已存在，获取其短链接
        $code_row = pg_fetch_assoc($code_result);
        $code = $code_row['code'];
        if ($_GET['t'] && $_GET['t'] == "json") {
            $data = array(
                'code' => 200,
                'msg' => 'OK',
                'url' => "https://1v.fit/?j={$code}"
            );
            echo json_encode($data);
            else{
            echo "成功！您的跳转链接为 https://1v.fit/?j={$data}";
        }
    } else {
        // URL 不存在，生成新的短链接
        $randstr = GetRandStr($URL_SHORTENER_LENGTH);
        $id = time();
        $query = "INSERT INTO url_data (id, url, code) VALUES ('$id', '$url', '$randstr')";
        $result = pg_query($db, $query);
        
        if (!$result) {
            die("数据库连接失败：" . pg_last_error());
        }

        if ($_GET['t'] && $_GET['t'] == "json") {
            $data = array(
                'code' => 200,
                'msg' => 'OK',
                'url' => "https://1v.fit/?j={$randstr}"
            );
            echo json_encode($data);
        } else {
            echo "成功！您的跳转链接为 https://1v.fit/?j={$randstr}";
        }
    }
} elseif ($_GET['j']) {
    // 处理跳转请求
    $code = pg_escape_string($_GET['j']);
    $query = "SELECT url FROM url_data WHERE code = '$code'";
    $result = pg_query($db, $query);
    
    if (!$result) {
        die("数据库查询失败：" . pg_last_error());
    }

    $row = pg_fetch_assoc($result);
    if (!$row) {
        die("不存在此链接！");
    }
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
