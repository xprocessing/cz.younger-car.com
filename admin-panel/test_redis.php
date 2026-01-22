<?php
// 测试Redis连接
try {
    $redis = new Redis();
    $connected = $redis->connect('localhost', 6379, 2);
    
    if ($connected) {
        echo "Redis连接成功！\n";
        
        // 测试认证（如果有密码）
        $password = ''; // 填写Redis密码
        if ($password) {
            $authResult = $redis->auth($password);
            echo "Redis认证: " . ($authResult ? "成功" : "失败") . "\n";
        }
        
        // 测试操作
        $redis->set('test', 'Hello Redis!');
        $value = $redis->get('test');
        echo "Redis操作测试: " . $value . "\n";
        
        $redis->close();
    } else {
        echo "Redis连接失败！\n";
    }
} catch (Exception $e) {
    echo "Redis错误: " . $e->getMessage() . "\n";
}
?>