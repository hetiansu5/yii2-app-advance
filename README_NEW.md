## 缓存方案

### 使用场景
项目中大部分的缓存都是将ID->数据的方式进行缓存的，每一个缓存类一般对应一个数据表业务，不同缓存类的前缀键名会设置不同，确保不同业务之间键名不会冲突。

### 目录规范

* 缓存类：`common\cache`
* 底层实现：`common\lib\fw\cache`


### 键前缀方案
* 一级前缀 `ModuleKey.php`
* 二级前缀 `{Type}ModuleKey.php`

### 类方法
* `key($id)`                              缓存key生成规则
* `get($id)`                             获取单键缓存
* `set($id, $value, $ttl = 0)`            设置单键缓存
* `delete($id)`                           删除单键缓存
* `getMulti($idArr, &$idsByMiss=[])`      批量获取多键缓存
* `setMulti($items, $ttl)`                批量设置多键缓存
* `deleteMulti($idArr)`                  批量删除多键缓存

参数说明

* `$id` 一般为数据的主键或者键名
* `$value`  数据
* `$ttl` 数据缓存时间，单位秒。0表示永久有效。
* `$idArr`  主键数组
* `$idsByMiss` 未获取到缓存的主键数组
* `$items` 键值对的数组 eg.[$id1 => $data1, $id2 => $data2]





## 计数器方案

### 使用场景
有些频繁更新的计数或者统计需求，比如用户点赞数、文章评论数，可以考虑使用计数器。
订单ID的生成也可以考虑使用计数器来实现，避免订单ID冲突。

### 目录规范

* 计数器类: `common\counter`
* 底层实现：`common\lib\fw\counter`

### 键前缀方案
* 一级前缀 `CounterKey.php`

### 类方法
* `key($id, $field = '')`                      缓存key(单/无子业务)生成规则
* `get($id, $field = '')`                      获取单键(单/无子业务)计数
* `set($id, $count, $field = '', $ttl = 0)`    设置单键(单/无子业务)计数
* `incr($id, $step = 1, $field = '', $ttl = 0)`   自增单间(单/无子业务)计数
* `decr($id, $step = 1, $field = '', $ttl = 0)`   自减单间(单/无子业务)计数 
* `delete($id)`                                删除单键(单/无子业务)计数
* `getMulti($idArr)`                           批量获取多键(无子业务)计数   
* `deleteMulti($idArr)`                       批量删除多键(无子业务)计数   
* `getOneCounts($id, $fields = [])`            批量获取单键(多子业务)计数
* `getMultiCounts(array $idArr, $fields = [])` 批量获取多键(多子业务)计数
* `deleteOneCounts($id, $fields = [])`         批量删除单键(多子业务)计数   
* `deleteMultiCounts(array $idArr, $fields = [])` 批量删除多键(多子业务)计数

参数说明

* `$id` 一般为数据的主键或者键名
* `$field` 子业务字段 eg.Example::USERS_COUNT
* `$count` 计数
* `$ttl` 数据缓存时间，单位秒。0表示永久有效。
* `$step` 自增/减值
* `$idArr`  主键数组
* `$fields` 子业务字段数组 `eg.[Example::USERS_COUNT, Example::MEDIAS_COUNT]`




## 消息队列方案

### 启动队列任务守护进程
* `vim /etc/crontab`
* `* * * * * root sh /www/logistics/console/web/daemon_watchdog.sh`

### 后台队列任务管理
可针对具体队列任务启动、暂停、重启、配置Worker数、查看任务挤压数

### 队列任务目录
* 目录：`common/job`

### 生产消息(入队列)
* 示例代码：`(new OrderEventJob($msg))->send();`

### 消费消息(出队列)
开启队列守护进程后，只需要在后台队列任务管理，可以对队列任务进行控制，开发只需要注意消费逻辑代码。
可以查看示例 common/job/OrderEventJob.php

### 调试消费队列逻辑
`console/web/yii task/work {jobName}`

eg. `console/web/yii task/work order_event`


## 后台菜单方案

### 菜单配置目录
`backend/config/purview`

### 注意事项
每个Controller需要给$curModule赋值，要与菜单配置的模块名一致。

### 前端框架
http://zui.sexy

## 业务日志规范

此跟系统本身的日志是不一样，Yii::\[info|error|...\]()是不一样的。目前系统日志主要是用于业务没有处理而由系统接管捕获的一些异常错误。

### 日志级别
按低到高排序：
* debug 调试 
* info 信息
* warn 告警
* error 错误

### 调用描述
```
//第一个参数为日志信息，支持数组和字符串格式
//第二个参数为日志类型，主要是用于标记业务的
Logger::getInstance()->error($msg, LogType::ORDER);
//Logger::getInstance()->info(...);
//Logger::getInstance()->warn(...);
//Logger::getInstance()->error(..);
```

### 日志配置
在项目配置的params参数中设置。
```
'log.path' => '/www//privdata/logistics/log/', //日志存储目录
'log.level' => 'info', //针对那个等级以上的才写入日志文件
```

## 异常处理
重写了用户端和商户端的应用的异常处理类，类名 common\components\ErrorHandler