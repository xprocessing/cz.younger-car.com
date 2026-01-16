<?php

/**
 * 日志管理类
 * 用于统一管理系统的日志记录
 */
class Logger {
    private static $instance;
    private $log_file;
    private $log_level;
    
    // 日志级别
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_FATAL = 4;
    
    // 日志级别名称映射
    private static $level_names = [
        self::LEVEL_DEBUG => 'DEBUG',
        self::LEVEL_INFO => 'INFO',
        self::LEVEL_WARNING => 'WARNING',
        self::LEVEL_ERROR => 'ERROR',
        self::LEVEL_FATAL => 'FATAL'
    ];
    
    private function __construct() {
        // 初始化日志文件路径
        $log_dir = APP_ROOT . '/logs';
        if (!is_dir($log_dir)) {
            // 尝试创建日志目录，如果失败则使用系统临时目录
            if (!@mkdir($log_dir, 0777, true)) {
                $log_dir = sys_get_temp_dir();
            }
        }
        
        $this->log_file = $log_dir . '/aigc_' . date('Y-m-d') . '.log';
        $this->log_level = self::LEVEL_INFO;
    }
    
    /**
     * 获取单例实例
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }
    
    /**
     * 设置日志级别
     */
    public function setLogLevel($level) {
        if (isset(self::$level_names[$level])) {
            $this->log_level = $level;
        }
    }
    
    /**
     * 记录调试日志
     */
    public function debug($message, $context = []) {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * 记录信息日志
     */
    public function info($message, $context = []) {
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * 记录警告日志
     */
    public function warning($message, $context = []) {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * 记录错误日志
     */
    public function error($message, $context = []) {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * 记录致命错误日志
     */
    public function fatal($message, $context = []) {
        $this->log(self::LEVEL_FATAL, $message, $context);
    }
    
    /**
     * 核心日志记录方法
     */
    private function log($level, $message, $context = []) {
        // 如果日志级别低于当前设置的级别，不记录
        if ($level < $this->log_level) {
            return;
        }
        
        // 获取当前时间
        $timestamp = date('Y-m-d H:i:s.u');
        
        // 获取日志级别名称
        $level_name = self::$level_names[$level];
        
        // 构建上下文信息
        $context_str = '';
        if (!empty($context)) {
            $context_str = ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        // 构建日志行
        $log_line = "[{$timestamp}] [{$level_name}] {$message}{$context_str}\n";
        
        // 写入日志文件
        error_log($log_line, 3, $this->log_file);
        
        // 如果是错误级别以上，同时输出到PHP错误日志
        if ($level >= self::LEVEL_ERROR) {
            error_log($log_line);
        }
    }
    
    /**
     * 记录异常
     */
    public function exception(Exception $exception, $message = null, $context = []) {
        $error_message = $message ? $message : 'Exception occurred';
        
        $context['exception'] = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];
        
        $this->log(self::LEVEL_ERROR, $error_message, $context);
    }
    
    /**
     * 记录API调用日志
     */
    public function apiCall($url, $method, $request, $response, $status_code, $execution_time) {
        $context = [
            'url' => $url,
            'method' => $method,
            'request' => $request,
            'response' => $response,
            'status_code' => $status_code,
            'execution_time' => $execution_time
        ];
        
        $this->log(self::LEVEL_INFO, 'API Call', $context);
    }
    
    /**
     * 记录任务日志
     */
    public function task($action, $task_id, $task_name, $task_type, $user_id, $context = []) {
        $context['task_id'] = $task_id;
        $context['task_name'] = $task_name;
        $context['task_type'] = $task_type;
        $context['user_id'] = $user_id;
        
        $this->log(self::LEVEL_INFO, "Task {$action}", $context);
    }
    
    /**
     * 记录图片处理日志
     */
    public function imageProcess($action, $image_path, $task_id, $result = null, $error = null) {
        $context = [
            'image_path' => $image_path,
            'task_id' => $task_id,
            'result' => $result,
            'error' => $error
        ];
        
        $this->log(self::LEVEL_INFO, "Image {$action}", $context);
    }
}