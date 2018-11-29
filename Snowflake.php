<?php

namespace garengoh\snowflake;

/**
 * 雪花算法类
 */
class Snowflake
{
    public $epoch_offset;  //偏移时间戳,该时间一定要小于第一个id生成的时间,且尽量大(影响算法的有效可用时间)

    private $sign_bits = 1;        //最高位(符号位)位数，始终为0，不可用
    public $timestamp_bits = 41;  //时间戳位数(算法默认41位,可以使用69年)
    public $data_center_bits = 5;  //IDC(数据中心)编号位数(算法默认5位,最多支持部署32个节点)
    public $machine_id_bits = 5;  //机器编号位数(算法默认5位,最多支持部署32个节点)
    public $sequence_bits = 12;   //计数序列号位数,即一系列的自增id，可以支持同一节点同一毫秒生成多个ID序号(算法默认12位,支持每个节点每毫秒产生4096个ID序号)。

    /**
     * @var null|integer 上一次生成id使用的时间戳(毫秒级别)
     */
    protected $lastTimestamp = null;

    /**
     * @var int
     */
    protected $sequence = 1;    //序列号

    /**
     * 使用雪花算法生成一个唯一ID
     * @return string 生成的ID
     * @throws \Exception
     */
    public function generateID($dataCenter_id = 0, $machine_id = 0)
    {
        $sign = 0; //符号位,值始终为0
        $signLeftShift = $this->timestamp_bits + $this->data_center_bits + $this->machine_id_bits + $this->sequence_bits;  //符号位左位移位数
        $timestampLeftShift = $this->data_center_bits + $this->machine_id_bits + $this->sequence_bits;    //时间戳左位移位数
        $dataCenterLeftShift = $this->machine_id_bits + $this->sequence_bits;   //IDC左位移位数
        $machineLeftShift = $this->sequence_bits;  //机器编号左位移位数
        $maxSequenceId = -1 ^ (-1 << $this->sequence_bits);    //最大序列号
        $maxMachineId = -1 ^ (-1 << $this->machine_id_bits);   //最大机器编号
        $maxDataCenterId = -1 ^ (-1 << $this->data_center_bits);   //最大数据中心编号

        if ($dataCenter_id > $maxDataCenterId) {
            throw new \Exception('数据中心编号取值范围为:0-' . $maxDataCenterId);
        }
        if ($machine_id > $maxMachineId) {
            throw new \Exception('机器编号编号取值范围为:0-' . $maxMachineId);
        }

        $timestamp = $this->getUnixTimestamp();
        if ($timestamp < $this->lastTimestamp) {
            throw new \Exception('时间倒退了!');
        }

        //与上次时间戳相等,需要生成序列号.不相等则重置序列号
        if ($timestamp == $this->lastTimestamp) {
            $sequence = ++$this->sequence;
            if ($sequence == $maxSequenceId) { //如果序列号超限，则需要重新获取时间
                $timestamp = $this->getUnixTimestamp();
                while ($timestamp <= $this->lastTimestamp) {    //时间相同则阻塞
                    $timestamp = $this->getUnixTimestamp();
                }
                $this->sequence = 0;
                $sequence = ++$this->sequence;
            }
        } else {
            $this->sequence = 0;
            $sequence = ++$this->sequence;
        }

        $this->lastTimestamp = $timestamp;
        $time = (int)($timestamp - $this->epoch_offset);
        $id = ($sign << $signLeftShift) | ($time << $timestampLeftShift) | ($dataCenter_id << $dataCenterLeftShift) | ($machine_id << $machineLeftShift) | $sequence;

        return (string)$id;
    }

    /**
     * 获取去当前时间戳
     *
     * @return integer 毫秒级别的时间戳
     */
    private function getUnixTimestamp()
    {
        return floor(microtime(true) * 1000);
    }
}