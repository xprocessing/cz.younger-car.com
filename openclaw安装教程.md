官方网站：https://openclaw.ai/

# 第1步：安装前准备安装软件 node和git
nodejs下载链接：https://nodejs.org/en/download
git下载链接：https://git-scm.com/

下载后用默认安装即可。

# 第2步： 安装openclaw  https://openclaw.ai/

1. windows系统，打开开始按钮，搜索powershell，选择管理员运行。
2. 输入: iwr -useb https://openclaw.ai/install.ps1 | iex 
3. 回车 安装
4. 输入：openclaw onboard 回车，进行初始化配置。

请注意，初始化配置选择很重要，否则会不断重新初始化。
用键盘的 上下左右箭头 选择，或者 用空格键进行选择。
用 回车 确认

# 初始化具体步骤：
1. I understand this is powerful and inherently risky. Continue?
这一步选择yes

2. Onboarding mode 
选择 QuickStart

3. Config handling
选择 Use existing values

4. Model/auth provider
选择  Qwen

5. Qwen auth method

选择Qwen OAuth  会跳出浏览器网页 登录授权。

6. Default model
选择 Keep current (qwen-portal/coder-model)

7. Select channel (QuickStart) 
选择 Skip for now

8. Configure skills now? (recommended) 

选择 NO

9. Enable hooks?

用 空格键 全选 ，然后回车

10.  Gateway service already installed （重新安装才才会遇到，默认安装是没有的。）
选择  Restart


11.  How do you want to hatch your bot?

选择  Open the Web UI

12. 默认就直接可用。

常见错误：
如果出现不能链接错误， 就在 http://127.0.0.1:18789/overview  填入token，再点Connect

token所在位置： C:\Users\用户名\.openclaw 文件夹的 openclaw.json 文件中 找到gateway  token 
