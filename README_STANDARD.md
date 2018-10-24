## 项目规范

### 默认设置

* 时区 默认欧洲格林威治标准时间时间
   
* 告警级别 忽略Notice级别和Warning的告警
   
* 调试模式 默认线上不开启，其他环境开启

### 常量

* 公共常量       `common/constants/Common.php`
* 错误码常量     `common/constants/ErrorCode.php`
* 日志类型常量    `common/constants/LogType.php`

### 多语言
 
* 业务多语言包

路径： `common/messages/{language}/{category}.php`

引用:  `\Yii::t($category, $message)` 

* 错误消息多语言

引入了错误码常量与多语言消息映射的关系，通过调用\Yii::t($category, $errorCode); 

### 目录调整

* controllers和views每个应用独立，models所有应用共用


### model规范

* 涉及model数据更新操作引发的操作，如状态变量引起的操作，尽量封装在一起。保持业务操作的一致性。
* 所有的model类都以Model结尾。如UserModel.php



## 命名规范
* namespace、类名、类文件、与namespace映射的文件夹采用StudlyCaps大驼峰命名法;
* 方法名、属性名、变量名采用camelCase小驼峰命名法;
* 函数名、非类文件、与namespace没有映射关系的文件夹采用下划线命名法;
* 全局常量、类常量采用大写下划线命名法;
* 数组键名、视图文件中的php变量名采用下划线命名法;




## Mysql建表规范

* 库、表、字段默认使用uft8mb4字符编码；
* 库、表、字段名称原则上不超过32位，全部使用小写，多个单词之间用下划线间隔；
* 项目使用只有增删改查的项目账号，不要使用root等高权限的账号，提高安全风险意识；

### 索引规范
* 单表索引不超过3个，联合索引不超过3个字段；
* 字符串索引要合理使用前缀索引，减少索引占用的空间；
* 辨识度比较低的字段不要建索引，如状态字段；
* 普通索引使用idx_{字段名}作为索引名称，唯一索引使用uniq_{字段名}作为索引名称；