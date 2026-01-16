
开发一个AI图片处理模块aigc，在一个页面实现下面功能，支持批量上传本地图片进行处理
1.批量去除瑕疵，调整亮度对比度（按1200x1200像素保存jpg格式）
2.批量抠图-导出png图
3.批量抠图-导出800x800白底图（产品主体占比80%）
4.批量改尺寸：（按像素大小：1920x1080，1200x1200,800x800，400*400）
5.批量打水印：（按位置：左上，右上，左下，右下，居中）文字水印，图片水印。
6.批量模特换脸


使用阿里云百炼API调用通义千问Qwen-image模型 
ID：引用 config/config.php中的ALIYUN_API_ID
API Key：引用config/config.php中的ALIYUN_API_KEY
归属账户：1487014886144352

HTTP调用 通义千问qwen-image-edit-plus
北京地域：POST https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation


- 创建AIGC任务表（合并结果表字段，仅保存图像URL）
CREATE TABLE IF NOT EXISTS aigc_tasks (
    -- 主键id，自增整数
    task_id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '任务ID',
    -- 用户ID，关联users表
    user_id INT NOT NULL COMMENT '用户ID',   
    -- 任务类型，用于区分不同功能的任务
    task_type ENUM('remove_defect', 'crop_png', 'crop_white_bg', 'resize', 'watermark', 'face_swap', 'multi_angle', 'other') NOT NULL COMMENT '任务类型',
    -- 任务状态
    task_status ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending' COMMENT '任务状态',
    -- 任务参数，JSON格式存储具体配置
    task_params JSON NOT NULL COMMENT '任务参数（JSON格式）',  
    -- 原始图片路径（网络地址）
    original_path VARCHAR(255) DEFAULT NULL COMMENT '原始文件路径',
    -- 处理结果状态
    process_status ENUM('success', 'failed') DEFAULT NULL COMMENT '处理状态',
    -- 处理结果URL（保存图像URL，不存储base64）
    result_url VARCHAR(255) DEFAULT NULL COMMENT '处理结果图像URL',
    -- api返回信息/错误信息（如果处理失败）
    result_data TEXT DEFAULT NULL COMMENT '处理结果数据（JSON格式）',   
    -- 开始处理时间
    started_at DATETIME DEFAULT NULL COMMENT '开始时间',
    -- 处理完成时间
    completed_at DATETIME DEFAULT NULL COMMENT '完成时间',
    
    -- 设置主键
    PRIMARY KEY (task_id),
    -- 添加外键约束
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='AI图片处理任务表）';

业务逻辑：
1.用户访问aigc.php 选择处理不同的图片处理方式（不要采用拉选择，要求页面左侧导航展示），选择多张图片，执行图片处理
2.图片上传到服务器接，存储到public/temp目录，返回图片路径https://cz.younger-car.com/admin-panel/public/temp/+图片文件名
3.根据多张图片的url，批量发起通义千问qwen-image-edit-plus模型处理任务
4.服务器在后台异步处理任务，每个api请求创建一个task_id， 调用通义千问qwen-image-edit-plus模型，处理参数为json格式，image字段包含图片url，处理方式（如remove_defect）
5.任务处理完成后，更新aigc_tasks表，设置状态为completed，保存处理结果URL等
6.提供入口aigc_task.php,展示任务处理状态和结果列表，可以下载处理结果图片


#单图编辑 请求 
curl --location 'https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation' \
--header 'Content-Type: application/json' \
--header "Authorization: Bearer $DASHSCOPE_API_KEY" \
--data '{
    "model": "qwen-image-edit-plus",
    "input": {
        "messages": [
            {
                "role": "user",
                "content": [
                    {
                        "image": "https://help-static-aliyun-doc.aliyuncs.com/file-manage-files/zh-CN/20250925/fpakfo/image36.webp"
                    },
                    {
                        "text": "生成一张符合深度图的图像，遵循以下描述：一辆红色的破旧的自行车停在一条泥泞的小路上，背景是茂密的原始森林"
                    }
                ]
            }
        ]
    },
    "parameters": {
        "n": 2,
        "negative_prompt": "低质量",
        "prompt_extend": true,
        "watermark": false
    }
}'

执行成功结果

{
    "output": {
        "choices": [
            {
                "finish_reason": "stop",
                "message": {
                    "role": "assistant",
                    "content": [
                        {
                            "image": "https://dashscope-result-sz.oss-cn-shenzhen.aliyuncs.com/xxx.png?Expires=xxx"
                        },
                        {
                            "image": "https://dashscope-result-sz.oss-cn-shenzhen.aliyuncs.com/xxx.png?Expires=xxx"
                        }
                    ]
                }
            }
        ]
    },
    "usage": {
        "width": 1248,
        "image_count": 2,
        "height": 832
    },
    "request_id": "bf37ca26-0abe-98e4-8065-xxxxxx"
}

多图融合

curl --location 'https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation' \
--header 'Content-Type: application/json' \
--header "Authorization: Bearer $DASHSCOPE_API_KEY" \
--data '{
    "model": "qwen-image-edit-plus",
    "input": {
        "messages": [
            {
                "role": "user",
                "content": [
                    {
                        "image": "https://help-static-aliyun-doc.aliyuncs.com/file-manage-files/zh-CN/20250925/thtclx/input1.png"
                    },
                    {
                        "image": "https://help-static-aliyun-doc.aliyuncs.com/file-manage-files/zh-CN/20250925/iclsnx/input2.png"
                    },
                    {
                        "image": "https://help-static-aliyun-doc.aliyuncs.com/file-manage-files/zh-CN/20250925/gborgw/input3.png"
                    },
                    {
                        "text": "图1中的女生穿着图2中的黑色裙子按图3的姿势坐下"
                    }
                ]
            }
        ]
    },
    "parameters": {
        "n": 2,
        "negative_prompt": "低质量",
        "prompt_extend": true,
        "watermark": false
    }
}'