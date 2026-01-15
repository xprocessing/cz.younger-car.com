<?php
// 测试PHP配置信息
echo "<h1>PHP配置信息测试</h1>";

echo "<h2>文件上传相关配置</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>配置项</th><th>当前值</th></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "<tr><td>upload_tmp_dir</td><td>" . ini_get('upload_tmp_dir') . "</td></tr>";
echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? 'On' : 'Off') . "</td></tr>";
echo "</table>";

echo "<h2>目录权限测试</h2>";
$temp_dir = __DIR__ . '/public/temp/';
echo "<p>临时目录: {$temp_dir}</p>";
echo "<p>是否存在: " . (is_dir($temp_dir) ? '是' : '否') . "</p>";
echo "<p>是否可写: " . (is_writable($temp_dir) ? '是' : '否') . "</p>";

echo "<h2>PHP版本信息</h2>";
echo "<p>PHP版本: " . phpversion() . "</p>";

echo "<h2>当前时间</h2>";
echo "<p>当前系统时间: " . date('Y-m-d H:i:s') . "</p>";
?>