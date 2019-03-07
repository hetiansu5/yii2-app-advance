## 项目概述
基于YII2.0多应用项目源码[https://github.com/yiisoft/yii2-app-advanced](https://github.com/yiisoft/yii2-app-advanced)做了一些优化调整，感觉官方的项目使用还存在诸多不便，这边做的调整主要是让项目搭建运行起来更加流畅。主要涉及如下修改项：
* 多环境的配置方案优化；
* 优化本地Docker开发环境的流程；
* 引入离线队列消费管理；
* Model层增加了基础数据操作类，所以具体的Model实例都可以继承此类，减少重复代码；
* 多语言包支持；
* common目录增加cache缓存层、constants常量层、counter计数器层、service服务层、traits代码复用类层、formatter数据格式化层、lib第三方扩展类层、components组件层

## 目录结构
-------------------

```
common
    config/              配置文件
    mail/                邮箱模板文件
    models/              数据库操作类
    widgets/             前端小物件
    lib/                 扩展类
    messages/            多语言包
    formatter/           数据格式化类
    constants/           常量
    service/             服务层（如第三方支付、物流商家）
    cache/               缓存层
    counter/             计数器层
    job/                 队列任务
    traits/              代码复用类
    components/          自定义组件
console
    config/              配置文件
    controllers/         控制器
    migrations/          数据库迁移相关
    runtime/             运行期间生成的文件（缓存、日志等）
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              配置文件
    controllers/         控制器
    runtime/             运行期间生成的文件（缓存、日志等）
    views/               视图
    web/                 入口脚本和文件
    formatter/           数据格式化类
env/                     多环境配置文件
    dev/                 本地开发环境
    test/                测试环境
    prod/                线上环境
vendor/                  第三方依赖包
assets/                  辅助脚本和环境配置文件等    
```

## 项目根目录
建议是以这种方式来
/www/${appName}，若当前项目的appName为frameworks，新项目初始化可以全局搜索替换项目名称

## 环境变量

通过环境变量依赖外部注入，不同环境加载不同的配置，默认使用线上配置。

### 环境变量类型

* dev   开发环境    YII_ENV_DEV
* pre   测试环境    YII_ENV_PRE (不用YII_ENV_TEST，是因为框架代码里面居然对这个环境做了很多特殊日志处理，乱输出东西)
* prod  线上环境    YII_ENV_PROD

### 注入方式

* 系统环境变量（支持cli模式）
```
sudo vi /etc/profile
export YII_ENV="dev"
source /etc/profile
```
* Nginx配置（支持fpm模式）
```
fastcgi_param YII_ENV "dev";
```

## 开发环境搭建

### 本机LNMP环境

* Nginx配置，demo配置见assets/nginx_conf/*.conf
* 环境变量配置
* 执行sh assets/sh/init.sh初始化
    * composer update, 不翻墙的话要等比较久
    * 目录权限变更
    * 创建必要文件目录
    * Windows的话执行不了shell，大家可以参考一下shell脚本的实现一下
* 本地HOST
    127.0.0.1 host.docker.internal redis.logistics.dev memcache.logistics.dev    

### Docker容器

* 详细见[DOCKER.md](DOCKER.md)


## 项目域名方案

### dev开发环境
* API域名  `api.frameworks-dev.xmwula.com`
* 后台域名    `admin.frameworks-dev.xmwula.com`


### pre测试环境  
* 用户端API域名  `c.frameworks-pre.xmwula.com`
* 后台域名    `admin.frameworks-pre.xmwula.com`

### prod线上环境
* 用户端API域名  `api-frameworks.cxyuns.com`
* 后台域名    `admin-frameworks.cxyuns.com`
      

## 分支管理

* master ->  线上环境
* pre -> 测试环境
* 个人分支 dev/xxx，以个人姓名拼音首字母简写，如张三的拼音简写为zs
* 项目初开发阶段，所有人统一在pre分支开发；
* 项目上线后个人从master分出个人分支dev/xxx，现在个人分支下开发，再合并到pre分支，测试环境验证通过后，再将个人分支代码合并到master

    
## 配置文件

配置文件目前分为3类，若相同键值配置，按照优先级依次递增覆盖。

* 公共配置  `common/config/main,params.php`           所有应用所有环境
* 应用配置  `{application}/config/main,params.php`    当前应用所有环境
* 环境配置  `env/{YII_ENV}/main,params.php`           所有应用当前环境


## CLI访问

`console/web/yii <route> [--option1=value1 --option2=value2 ... argument1 argument2 ...]`

以上，<route> 指的是控制器动作的路由。选项将填充类属性， 参数是动作方法的参数。

eg. `console/web/yii test/say --times=2 hello`

windows：`yii.bat task/work test_event`


























