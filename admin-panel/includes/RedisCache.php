<?php
/**
 * Redis缓存类
 * 用于缓存统计数据，减少数据库查询，提高页面加载速度
 */
class RedisCache {
    private static $instance = null;
    private $redis;
    private $connected = false;
    
    private function __construct() {
        try {
            // 检查Redis扩展是否可用
            if (!extension_loaded('redis')) {
                throw new Exception('Redis extension is not loaded');
            }
            
            // 创建Redis实例
            $this->redis = new Redis();
            
            // 连接Redis服务器
            $this->connected = $this->redis->connect(
                REDIS_HOST, 
                REDIS_PORT, 
                REDIS_TIMEOUT
            );
            
            // 如果有密码，进行认证
            if (REDIS_PASSWORD) {
                $this->redis->auth(REDIS_PASSWORD);
            }
            
            // 选择数据库
            $this->redis->select(REDIS_DB);
            
        } catch (Exception $e) {
            // Redis连接失败，记录错误但不影响程序运行
            error_log('Redis connection failed: ' . $e->getMessage());
            $this->connected = false;
        }
    }
    
    /**
     * 获取Redis缓存实例
     * @return RedisCache
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new RedisCache();
        }
        return self::$instance;
    }
    
    /**
     * 设置缓存
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $expire 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $expire = 3600) {
        if (!$this->connected) {
            return false;
        }
        
        try {
            $jsonValue = json_encode($value);
            return $this->redis->setex($key, $expire, $jsonValue);
        } catch (Exception $e) {
            error_log('Redis set failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 获取缓存
     * @param string $key 缓存键
     * @return mixed|null
     */
    public function get($key) {
        if (!$this->connected) {
            return null;
        }
        
        try {
            $jsonValue = $this->redis->get($key);
            if ($jsonValue === false) {
                return null;
            }
            return json_decode($jsonValue, true);
        } catch (Exception $e) {
            error_log('Redis get failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 删除缓存
     * @param string $key 缓存键
     * @return bool
     */
    public function delete($key) {
        if (!$this->connected) {
            return false;
        }
        
        try {
            return $this->redis->del($key) > 0;
        } catch (Exception $e) {
            error_log('Redis delete failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 检查缓存是否存在
     * @param string $key 缓存键
     * @return bool
     */
    public function exists($key) {
        if (!$this->connected) {
            return false;
        }
        
        try {
            return $this->redis->exists($key) > 0;
        } catch (Exception $e) {
            error_log('Redis exists failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 清除所有缓存
     * @return bool
     */
    public function flushAll() {
        if (!$this->connected) {
            return false;
        }
        
        try {
            return $this->redis->flushAll();
        } catch (Exception $e) {
            error_log('Redis flushAll failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 获取Redis连接状态
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
    }
}

// 全局Redis缓存实例
$redisCache = RedisCache::getInstance();
?>