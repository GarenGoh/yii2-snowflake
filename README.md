# yii2-snowflake
SnowFlake for yii framework.

## Installation

`composer require --prefer-dist "garengoh/yii2-snowflake" "dev-master"`

## Usage

Configure it in the `config/web.php` file.

```php
'components' => [
    'snowflake' => [
        'class' => 'garengoh\snowflake\Snowflake',
        'epoch_offset' => 15147360000000,   //2018-01-01
        //其他可配置的属性
        'timestamp_bits' => 41,     //时间戳位数(算法默认41位,可以使用69年)
        'data_center_bits' => 5,    //IDC(数据中心)编号位数(算法默认5位,最多支持部署32个节点)
        'machine_id_bits' => 5,     //机器编号位数(算法默认5位,最多支持部署32个节点)
        'sequence_bits' => 12,      //计数序列号位数,即一系列的自增id，可以支持同一节点同一毫秒生成多个ID序号(算法默认12位,支持每个节点每毫秒产生4096个ID序号)。
    ]
]
```
