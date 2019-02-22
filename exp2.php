<?php
/**
 * mysql数据字典在线生成
 * @author change
 */

//配置数据库
$dbserver   = "127.0.0.1";
$dbusername = "root";
$dbpassword = "root";
$database   = 'ztwebdatas';

//其他配置
$title      = '数据字典';
$mysql_conn = @mysql_connect($dbserver, $dbusername, $dbpassword) or die("Mysql connect is error.");
mysql_select_db($database, $mysql_conn);
mysql_query('SET NAMES utf8', $mysql_conn);
$table_result = mysql_query('show tables', $mysql_conn);

//取得所有的表名
while ($row = @mysql_fetch_array($table_result)) {
    $tables[]['TABLE_NAME'] = $row[0];
}

//循环取得所有表的备注
foreach ($tables as $k => $v) {
    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.TABLES ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
    $table_result = mysql_query($sql, $mysql_conn);

    while ($t = mysql_fetch_array($table_result)) {
        $tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
    }

    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
    $fields       = array();
    $field_result = mysql_query($sql, $mysql_conn);
    while ($t = @mysql_fetch_array($field_result)) {
        $fields[] = $t;
    }
    $tables[$k]['COLUMN'] = $fields;
}

mysql_close($mysql_conn);

?>
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Author" content="changyuan">
  <meta name="Keywords" content="db">
  <meta name="Description" content="db">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title><?=$title;?></title>
  <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <style type="text/css">
      .box{
        margin: 0 auto;
        text-align: center;
        width: 80%;
      }
      caption,th,td{
        text-align: center;
      }
      caption {
        font-weight: bold;
        font-size: 20px;
      }
  </style>
 </head>
 <body>
<div class="box">
<h2><?=$title;?></h2>

<?php if (!empty($tables)): ?>
    <?php foreach ($tables as $k => $v): ?>
        <h2><?=$v['TABLE_COMMENT'];?></h2>
        <table class="table table-hover table-bordered" width="80%">
            <caption><?=$v['TABLE_NAME'];?></caption>
            <thead>
                <tr class="success">
                    <th>字段名</th>
                    <th>数据类型</th>
                    <th>默认值</th>
                    <th>允许非空</th>
                    <th>自动递增</th>
                    <th>注释</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($v['COLUMN'] as $f): ?>
                    <tr>
                        <td><?=$f['COLUMN_NAME'];?></td>
                        <td><?=$f['COLUMN_TYPE'];?></td>
                        <td><?=$f['COLUMN_DEFAULT'];?></td>
                        <td><?=$f['IS_NULLABLE'];?></td>
                        <td><?=$f['EXTRA'] == 'auto_increment' ? "√" : "";?></td>
                        <td><?=$f['COLUMN_COMMENT'];?></td>
                    </tr>
                <?php endforeach?>
            </tbody>
        </table>
    <?php endforeach?>
<?php endif?>
</div>
 </body>
</html>