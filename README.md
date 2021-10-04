# WeComchan-wordpress-comments-notice

WeCom酱WordPress博客评论微信通知插件

## 使用帮助

### 1.部署WeComchan-Go版云函数

这里不细说了，官方教程：https://github.com/easychen/wecomchan/tree/main/go-scf

如果能力允许可以自由发挥，WeComchan还有其它版本

### 2.安装WordPress插件

到 [插件Release页](https://github.com/DefiedParty/WeComchan-wordpress-comments-notice/releases) 下载最新版安装

### 3.转到网站讨论页配置信息

![image](https://user-images.githubusercontent.com/44311872/135795520-e67cbd69-b50d-4c52-ac7f-af426e9c6ecb.png)

**访问路径** 在腾讯云控制台或者根据自己的实际情况

**sendkey** 部署云函数时有用到

### 4.enjoy

点击保存即可，上一张效果图

![image](https://user-images.githubusercontent.com/44311872/135795597-4c485cae-f96b-4fd2-9b5f-663d3963e5ff.png)

### 5.已知问题
- [2021/9/23] 评论中markdown部分，在电脑端微信、企业微信中可以正常解析，而在手机中显示为HTML代码，待修复
