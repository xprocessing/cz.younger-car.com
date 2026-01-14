
开发一个AI图片处理模块aigc，在一个页面实现下面功能，支持批量上传本地图片进行处理
1.批量去除瑕疵，调整亮度对比度（按1200x1200像素保存jpg格式）
2.批量抠图-导出png图
3.批量抠图-导出800x800白底图（产品主体占比80%）
4.批量改尺寸：（按比例：4：3，3：4，16：9，9：16，1:1 按像素大小：1920x1080，1200x1200,800x800，400*400）
5.批量打水印：（按位置：左上，右上，左下，右下，居中）文字水印，图片水印。
6.批量模特换脸
7.生成多角度图片（按角度：30度，60度，90度，120度，150度，180度）
8.支持自定义模板，存储模板到数据库，选择使用模板


使用阿里云百炼API调用通义千问Qwen-image模型 
ID：引用 config/config.php中的ALIYUN_API_ID
API Key：引用config/config.php中的ALIYUN_API_KEY
归属账户：1487014886144352

HTTP调用 通义千问Qwen-image模型支持同步接口，一次请求即可获得结果，调用流程简单，推荐用于多数场景。
北京地域：POST https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation

