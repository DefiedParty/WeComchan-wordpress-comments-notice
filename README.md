# WeComchan-wordpress-comments-notice

WeCom酱WordPress博客评论微信通知插件

```
  由于WeCom政策修改，新建内部应用需设置IP白名单才能操作对应API，且云函数开始收费，故云函数已经不太合适了，于是更新了2.0.0这版直接操作WeComAPI发信。
  如想继续使用云函数可以使用Releases页面中的1.0.2版本，自行研究使用方法或找到之前的README文件操作。
```

## 使用帮助

### 1.配置企业微信设置

这里不细说了，需要在企业微信后台获取 应用ID(AgentId) 应用密钥(Secret) 企业ID(corpid)

### 2.安装WordPress插件

到 [插件Release页](https://github.com/DefiedParty/WeComchan-wordpress-comments-notice/releases) 下载最新版安装

### 3.转到网站讨论页配置信息

![image](https://user-images.githubusercontent.com/44311872/211920889-ff2ce267-1723-4b60-b407-2329e92a237d.png)

根据实际情况填入图示4项参数，均为必填。

### 4.enjoy

点击保存即可，上一张效果图

![image](https://user-images.githubusercontent.com/44311872/135795597-4c485cae-f96b-4fd2-9b5f-663d3963e5ff.png)

### 5.已知问题
- [2021/9/23]× 评论中markdown部分，在电脑端微信、企业微信中可以正常解析，而在手机中显示为HTML代码，待修复

  *WeCom政策更新，markdown转换的html标签在所有平台均失效不能再展示。*
  
- [2023/1/12]√ 英文引号未转义引起隔断，引号后所有内容缺失。已修复
