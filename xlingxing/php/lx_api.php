<?php

/**
 * 领星OpenAPI调用封装类
 * 处理认证、请求发送及异常处理
 */
class LingXingApiClient {
    /**
     * @var \Ak\OpenAPI\Services\OpenAPIRequestService 核心请求服务实例
     */
    private $client;

    /**
     * @var string 缓存文件路径（用于存储AccessToken）
     */
    private $cacheFile;

    /**
     * 构造函数：初始化配置并创建请求客户端
     * 
     * @param string $configPath 配置文件路径
     * @param string $cachePath AccessToken缓存文件路径
     * @throws \Exception 配置错误时抛出异常
     */
    public function __construct(string $configPath = __DIR__ . '/config.json', string $cachePath = __DIR__ . '/access_token.cache') {
        // 引入自动加载文件
        $this->loadAutoloader();

        // 加载配置
        $config = $this->loadConfig($configPath);

        // 初始化请求客户端
        $this->client = new \Ak\OpenAPI\Services\OpenAPIRequestService(
            $config->host_url,
            $config->app_id,
            $config->app_secret
        );

        // 初始化缓存文件
        $this->cacheFile = $cachePath;
    }

    /**
     * 加载Composer自动加载器
     * @throws \Exception 自动加载文件不存在时抛出异常
     */
    private function loadAutoloader() {
        $autoloadPath = __DIR__ . '/vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            throw new \Exception("未找到自动加载文件，请执行 composer install");
        }
        require_once $autoloadPath;
    }

    /**
     * 加载配置文件
     * @param string $configPath 配置文件路径
     * @return object 配置对象
     * @throws \Exception 配置文件错误时抛出异常
     */
    private function loadConfig(string $configPath) {
        if (!file_exists($configPath)) {
            throw new \Exception("配置文件不存在：{$configPath}");
        }

        $jsonContent = file_get_contents($configPath);
        $config = json_decode($jsonContent);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("配置文件解析失败：" . json_last_error_msg());
        }

        if (empty($config->LingXing->app_id) || empty($config->LingXing->app_secret) || empty($config->LingXing->host_url)) {
            throw new \Exception("配置文件缺少必要参数（app_id/app_secret/host_url）");
        }

        return $config->LingXing;
    }

    /**
     * 获取AccessToken（带缓存逻辑）
     * @return string AccessToken
     * @throws \Exception 认证失败时抛出异常
     */
    public function getAccessToken(): string {
        // 尝试从缓存读取
        if (file_exists($this->cacheFile)) {
            $cacheData = json_decode(file_get_contents($this->cacheFile), true);
            if ($cacheData && $cacheData['expire_at'] > time()) {
                // 缓存有效，直接使用
                $this->client->setAccessToken(
                    $cacheData['access_token'],
                    $cacheData['refresh_token'],
                    $cacheData['expire_at']
                );
                return $cacheData['access_token'];
            } elseif ($cacheData && !empty($cacheData['refresh_token'])) {
                // 缓存过期，尝试刷新
                try {
                    $tokenDto = $this->client->refreshToken($cacheData['refresh_token']);
                    $this->saveTokenCache($tokenDto);
                    return $tokenDto->getAccessToken();
                } catch (\Exception $e) {
                    // 刷新失败，重新生成
                }
            }
        }

        // 重新生成AccessToken
        $tokenDto = $this->client->generateAccessToken();
        $this->saveTokenCache($tokenDto);
        return $tokenDto->getAccessToken();
    }

    /**
     * 保存AccessToken到缓存
     * @param \Ak\OpenAPI\Dto\AccessTokenDto $tokenDto 令牌对象
     */
    private function saveTokenCache(\Ak\OpenAPI\Dto\AccessTokenDto $tokenDto) {
        $cacheData = [
            'access_token' => $tokenDto->getAccessToken(),
            'refresh_token' => $tokenDto->getRefreshToken(),
            'expire_at' => $tokenDto->getExpireAt()
        ];
        file_put_contents($this->cacheFile, json_encode($cacheData));
    }

    /**
     * 发送GET请求
     * @param string $path 接口路径（如/erp/sc/data/seller/lists）
     * @param array $params 请求参数
     * @return array 接口返回结果
     * @throws \Exception 请求失败时抛出异常
     */
    public function get(string $path, array $params = []): array {
        return $this->request($path, 'GET', $params);
    }

    /**
     * 发送POST请求
     * @param string $path 接口路径
     * @param array $params 请求参数
     * @return array 接口返回结果
     * @throws \Exception 请求失败时抛出异常
     */
    public function post(string $path, array $params = []): array {
        return $this->request($path, 'POST', $params);
    }

    /**
     * 通用请求方法
     * @param string $path 接口路径
     * @param string $method 请求方法（GET/POST）
     * @param array $params 请求参数
     * @return array 接口返回结果
     * @throws \Exception 请求失败时抛出异常
     */
    private function request(string $path, string $method, array $params = []): array {
        try {
            // 确保AccessToken有效
            $this->getAccessToken();
            // 发送请求
            return $this->client->makeRequest($path, $method, $params);
        } catch (\Ak\OpenAPI\Exception\InvalidAccessTokenException $e) {
            throw new \Exception("AccessToken无效：" . $e->getMessage());
        } catch (\Ak\OpenAPI\Exception\RequestException $e) {
            throw new \Exception("请求失败：" . $e->getMessage());
        } catch (\Ak\OpenAPI\Exception\InvalidResponseException $e) {
            throw new \Exception("响应格式错误：" . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception("接口调用失败：" . $e->getMessage());
        }
    }
}

// 使用示例
/*
try {
    // 初始化API客户端
    $apiClient = new LingXingApiClient();
    
    // 调用GET接口示例
    $sellerList = $apiClient->get('/erp/sc/data/seller/lists');
    print_r("卖家列表：" . PHP_EOL);
    print_r($sellerList);
    
    // 调用POST接口示例
    $orderParams = [
        'start_date' => '2023-12-09 00:00:00',
        'end_date' => '2025-12-18 23:59:59'
    ];
    $orders = $apiClient->post('/erp/sc/data/mws/orders', $orderParams);
    print_r("订单数据：" . PHP_EOL);
    print_r($orders);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}
*/