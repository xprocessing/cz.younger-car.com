<?php
/**
 * Asinking OpenAPI PHP SDK（完整版，修复语法错误+签名错误）
 * PHP版本: >= 7.4
 */

// 响应模型类
class ResponseResult {
    public ?int $code = null;
    public ?string $message = null;
    public $data = null;
    public $error_details = null;
    public ?string $request_id = null;
    public ?string $response_time = null;
    public ?int $total = null;

    public function __construct(array $data) {
        $this->code = $data['code'] ?? null;
        $this->message = $data['message'] ?? ($data['msg'] ?? null);
        $this->data = $data['data'] ?? null;
        $this->error_details = $data['error_details'] ?? null;
        $this->request_id = $data['request_id'] ?? ($data['traceId'] ?? null);
        $this->response_time = $data['response_time'] ?? null;
        $this->total = $data['total'] ?? null;
    }

    public function toArray() {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
            'error_details' => $this->error_details,
            'request_id' => $this->request_id,
            'response_time' => $this->response_time,
            'total' => $this->total
        ];
    }
}

// AccessToken模型类
class AccessTokenDto {
    public string $access_token;
    public string $refresh_token;
    public int $expires_in;

    public function __construct(array $data) {
        $this->access_token = $data['access_token'] ?? '';
        $this->refresh_token = $data['refresh_token'] ?? '';
        $this->expires_in = $data['expires_in'] ?? 0;
    }
}

// 加密工具类（修复AES Padding逻辑）
class CryptoUtil {
    // AES-128-ECB加密（兼容Python PKCS7 Padding）
    public static function aesEncrypt(string $key, string $data): string {
        $key = substr($key, 0, 16); // 确保密钥为16字节（AES-128）
        $blockSize = 16;
        $pad = $blockSize - (strlen($data) % $blockSize);
        if ($pad == 0) {
            $pad = $blockSize; // 若刚好整除，补一个块的padding
        }
        $data .= str_repeat(chr($pad), $pad);
        
        $encrypted = openssl_encrypt(
            $data,
            'AES-128-ECB',
            $key,
            OPENSSL_RAW_DATA // 原始输出，不自动base64
        );
        
        if ($encrypted === false) {
            throw new Exception("AES加密失败: " . openssl_error_string());
        }
        
        return base64_encode($encrypted);
    }

    // MD5加密（统一大写）
    public static function md5Encrypt(string $text): string {
        return strtoupper(md5(trim($text)));
    }
}

// 签名工具类（核心修正：修复foreach闭合+签名逻辑）
class SignBase {
    /**
     * 生成签名
     * @param string $encryptKey 加密密钥（使用app_id）
     * @param array $requestParams 待签名参数
     * @return string 签名结果
     */
    public static function generateSign(string $encryptKey, array $requestParams): string {
        // 1. 格式化参数（排序+拼接）
        $canonicalQuerystring = self::formatParams($requestParams);
        // 2. MD5加密（大写）
        $md5Str = CryptoUtil::md5Encrypt($canonicalQuerystring);
        // 3. AES-ECB加密 + Base64
        return CryptoUtil::aesEncrypt($encryptKey, $md5Str);
    }

    /**
     * 格式化参数（与Python逻辑完全一致）
     * @param array|null $requestParams
     * @return string
     */
    public static function formatParams(?array $requestParams): string {
        if (empty($requestParams) || !is_array($requestParams)) {
            return '';
        }

        // 第一步：全局按键名升序排序
        ksort($requestParams, SORT_STRING);
        $canonicalStrs = [];

        foreach ($requestParams as $k => $v) {
            // 跳过空值
            if ($v === "" || $v === null) {
                continue;
            }
            
            // 处理数组/对象：先排序内部键，再序列化
            if (is_array($v) || is_object($v)) {
                $arr = is_object($v) ? (array)$v : $v;
                ksort($arr, SORT_STRING); // 数组内部按键排序
                
                $jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
                if (PHP_VERSION_ID >= 70300) {
                    $jsonOptions |= JSON_THROW_ON_ERROR;
                }
                $jsonStr = json_encode($arr, $jsonOptions);
                if ($jsonStr === false) {
                    throw new Exception("JSON序列化失败: " . json_last_error_msg());
                }
                $canonicalStrs[] = $k . '=' . $jsonStr;
            } else {
                // 普通值：直接拼接（转义特殊字符）
                $canonicalStrs[] = $k . '=' . urlencode((string)$v);
            }
        } // 关键：补充foreach的闭合花括号

        return implode('&', $canonicalStrs);
    }
}

// HTTP工具类
class HttpBase {
    private int $defaultTimeout;

    public function __construct(int $defaultTimeout = 30) {
        $this->defaultTimeout = $defaultTimeout;
    }

    /**
     * 发送HTTP请求
     * @param string $method GET/POST/PUT/DELETE
     * @param string $url 请求地址
     * @param array $options 配置：params/headers/json/timeout
     * @return ResponseResult
     * @throws Exception
     */
    public function request(string $method, string $url, array $options = []): ResponseResult {
        $timeout = $options['timeout'] ?? $this->defaultTimeout;
        $params = $options['params'] ?? [];
        $json = $options['json'] ?? null;
        $headers = $options['headers'] ?? [];

        $ch = curl_init();

        // 处理URL参数
        if (!empty($params)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        }

        // 设置CURL选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略SSL证书（生产环境建议开启）
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 处理JSON请求体
        if ($json !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json, JSON_UNESCAPED_UNICODE));
            if (!isset($headers['Content-Type'])) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($this->formatHeaders($headers), ['Content-Type: application/json']));
            }
        }

        // 执行请求
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        // 处理CURL错误
        if ($curlError) {
            throw new Exception("CURL请求失败: {$curlError}");
        }

        // 处理HTTP状态码
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new Exception("HTTP响应错误 [{$httpCode}]: {$response}");
        }

        // 解析JSON响应
        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON解析失败: " . json_last_error_msg() . " | 原始响应: {$response}");
        }

        return new ResponseResult($responseData);
    }

    /**
     * 格式化请求头
     * @param array $headers
     * @return array
     */
    private function formatHeaders(array $headers): array {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }
}

// OpenAPI基础类（修正签名密钥）
class OpenApiBase {
    private string $host;
    private string $appId;
    private string $appSecret;

    public function __construct(string $host, string $appId, string $appSecret) {
        $this->host = rtrim($host, '/');
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * 获取AccessToken
     * @return AccessTokenDto
     * @throws Exception
     */
    public function generateAccessToken(): AccessTokenDto {
        $path = '/api/auth-server/oauth/access-token';
        $url = $this->host . $path;
        $params = [
            "appId" => $this->appId,
            "appSecret" => $this->appSecret,
        ];

        $http = new HttpBase();
        $respResult = $http->request("POST", $url, ['params' => $params]);

        if ($respResult->code != 200) {
            throw new Exception("获取access_token失败 [{$respResult->code}]: {$respResult->message}");
        }

        return new AccessTokenDto($respResult->data);
    }

    /**
     * 刷新AccessToken
     * @param string $refreshToken
     * @return AccessTokenDto
     * @throws Exception
     */
    public function refreshToken(string $refreshToken): AccessTokenDto {
        $path = '/api/auth-server/oauth/refresh';
        $url = $this->host . $path;
        $params = [
            "appId" => $this->appId,
            "refreshToken" => $refreshToken,
        ];

        $http = new HttpBase();
        $respResult = $http->request("POST", $url, ['params' => $params]);

        if ($respResult->code != 200) {
            throw new Exception("刷新access_token失败 [{$respResult->code}]: {$respResult->message}");
        }

        return new AccessTokenDto($respResult->data);
    }

    /**
     * 发送API请求（核心：修正签名密钥为appId）
     * @param string $accessToken
     * @param string $routeName
     * @param string $method
     * @param array $reqParams
     * @param array $reqBody
     * @param array $options
     * @return ResponseResult
     * @throws Exception
     */
    public function request(
        string $accessToken,
        string $routeName,
        string $method,
        array $reqParams = [],
        array $reqBody = [],
        array $options = []
    ): ResponseResult {
        $url = $this->host . $routeName;
        $headers = $options['headers'] ?? [];

        // 1. 合并所有待签名参数（请求参数 + 请求体 + 公共参数）
        $genSignParams = array_merge($reqBody, $reqParams);
        $signParams = [
            "app_key" => $this->appId,
            "access_token" => $accessToken,
            "timestamp" => (string)time(), // 秒级时间戳（与Python一致）
        ];
        $genSignParams = array_merge($genSignParams, $signParams);

        // 2. 生成签名（关键：使用appId作为加密密钥）
        $sign = SignBase::generateSign($this->appId, $genSignParams);
        $signParams["sign"] = $sign;

        // 3. 合并签名参数到URL参数中
        $reqParams = array_merge($reqParams, $signParams);

        // 4. 发送请求
        $httpOptions = array_merge($options, [
            'params' => $reqParams,
            'headers' => $headers,
            'json' => $reqBody
        ]);

        $http = new HttpBase();
        return $http->request($method, $url, $httpOptions);
    }
}

// 凌星API客户端
class LingXingApiClient {
    private string $baseUrl;
    private string $appId;
    private string $appSecret;
    private OpenApiBase $opApi;
    private ?string $accessToken = null;
    private ?string $refreshToken = null;
    private int $tokenExpireTime = 0; // token过期时间戳

    /**
     * 构造函数
     * @param string $configPath 配置文件路径
     * @param string $env 环境名称
     * @throws Exception
     */
    public function __construct(string $configPath = 'config.json', string $env = 'LingXing') {
        // 加载配置文件
        if (!file_exists($configPath)) {
            throw new Exception("配置文件不存在: {$configPath}");
        }

        $configContent = file_get_contents($configPath);
        $config = json_decode($configContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("配置文件解析错误: " . json_last_error_msg());
        }

        if (!isset($config[$env])) {
            throw new Exception("环境配置不存在: {$env}");
        }

        $envConfig = $config[$env];
        $this->baseUrl = $envConfig['base_url'] ?? '';
        $this->appId = $envConfig['app_id'] ?? '';
        $this->appSecret = $envConfig['app_secret'] ?? '';

        // 验证配置
        if (empty($this->baseUrl) || empty($this->appId) || empty($this->appSecret)) {
            throw new Exception("配置不完整：base_url/app_id/app_secret不能为空");
        }

        $this->opApi = new OpenApiBase($this->baseUrl, $this->appId, $this->appSecret);
    }

    /**
     * 获取有效AccessToken（自动刷新）
     * @return string
     * @throws Exception
     */
    public function getToken(): string {
        // 检查token是否过期（提前60秒刷新）
        $now = time();
        if (empty($this->accessToken) || $this->tokenExpireTime - 60 < $now) {
            if (!empty($this->refreshToken)) {
                try {
                    // 尝试刷新token
                    $tokenResp = $this->opApi->refreshToken($this->refreshToken);
                    $this->accessToken = $tokenResp->access_token;
                    $this->refreshToken = $tokenResp->refresh_token;
                    $this->tokenExpireTime = $now + $tokenResp->expires_in;
                    return $this->accessToken;
                } catch (Exception $e) {
                    error_log("刷新token失败，尝试重新获取: " . $e->getMessage());
                }
            }

            // 重新获取token
            $tokenResp = $this->opApi->generateAccessToken();
            $this->accessToken = $tokenResp->access_token;
            $this->refreshToken = $tokenResp->refresh_token;
            $this->tokenExpireTime = $now + $tokenResp->expires_in;
        }

        return $this->accessToken;
    }

    /**
     * 调用API（自动处理token）
     * @param string $apiPath API路径
     * @param array $reqBody 请求体
     * @param string $method 请求方法
     * @return ResponseResult
     * @throws Exception
     */
    public function requestApi(
        string $apiPath,
        array $reqBody = [],
        string $method = "POST"
    ): ResponseResult {
        $token = $this->getToken();
        
        // 第一次请求
        $resp = $this->opApi->request($token, $apiPath, $method, [], $reqBody);

        // 处理token过期（兜底）
        if ($resp->code == 'TOKEN_EXPIRED' || $resp->code == 401) {
            $this->accessToken = null; // 强制刷新token
            $token = $this->getToken();
            $resp = $this->opApi->request($token, $apiPath, $method, [], $reqBody);
        }

        // 签名错误提示
        if (strpos(strtolower($resp->message ?? ''), 'sign') !== false) {
            throw new Exception("签名验证失败: {$resp->message} | 请求ID: {$resp->request_id}");
        }

        return $resp;
    }
}

// ====================== 使用示例 ======================
/**
 * 主函数
 */
function main() {
    try {
        // 1. 配置文件示例（config.json）：
        /*
        {
            "LingXing": {
                "base_url": "https://api.xxx.com",
                "app_id": "你的app_id",
                "app_secret": "你的app_secret"
            }
        }
        */

        // 2. 初始化客户端
        $client = new LingXingApiClient('config.json', 'LingXing');
        
        // 3. 调用API示例
        $apiPath = "/pb/mp/shop/v2/getSellerList";
        $reqBody = [
            "offset" => 0,
            "length" => 200,
            "platform_code" => [10008, 10011],
            "is_sync" => 1,
            "status" => 1
        ];
        
        // 4. 发送请求
        $resp = $client->requestApi($apiPath, $reqBody);
        
        // 5. 输出结果
        echo "===== 响应结果 =====\n";
        echo "状态码: " . $resp->code . "\n";
        echo "消息: " . $resp->message . "\n";
        echo "请求ID: " . $resp->request_id . "\n";
        echo "数据: " . print_r($resp->data, true) . "\n";

        // 6. 保存结果到文件
        file_put_contents(
            "seller_list_" . date('YmdHis') . ".json",
            json_encode($resp->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

    } catch (Exception $e) {
        echo "请求失败: " . $e->getMessage() . "\n";
        error_log("API请求错误: " . $e->getMessage() . " | 堆栈: " . $e->getTraceAsString());
    }
}

// 运行示例
main();