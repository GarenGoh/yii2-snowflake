# yii2-snowflake
SnowFlake for yii framework.

## Installation

`composer require garengoh/snowflake`

## Usage

在配置文件`config/web.php`中添加配置,当然你也可以添加到`config/common.php`

```php
'components' => [
    'snowflake' => [
        'class' => 'garengoh\snowflake\Snowflake',
        'epoch_offset' => 1514736000000,   //2018-01-01
        //其他可配置的属性(也可以不用配置)
        'timestamp_bits' => 41,     //时间戳位数(算法默认41位,可以使用69年)
        'data_center_bits' => 5,    //IDC(数据中心)编号位数(算法默认5位,最多支持部署32个节点)
        'machine_id_bits' => 5,     //机器编号位数(算法默认5位,最多支持部署32个节点)
        'sequence_bits' => 12,      //计数序列号位数,即一系列的自增id，可以支持同一节点同一毫秒生成多个ID序号(算法默认12位,支持每个节点每毫秒产生4096个ID序号)。
    ]
]
```
项目中调用
```php
//只有一台机器(服务器,虚拟机)时
Yii::$app->snowflake->generateID();

//多个数据中心或多台机器时(第一个参数是数据中心编号,第二个机器是机器编号)
Yii::$app->snowflake->generateID(1,3);
```

## 相关文章
- [php实现雪花算法（ID递增)](http://wqiang.net/article/view?id=23)
- [Twitter Snowflake算法详解](https://blog.csdn.net/yangding_/article/details/52768906)
