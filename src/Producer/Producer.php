<?php declare(strict_types=1);
/*
 * Copyright (C) 2019-2020 Thomas Alatalo Berg <thomas@izytech.se>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Izytechab\Kafka\Producer;

final class Producer
{

    use \Nette\SmartObject;


    protected $config;
    protected $bulk = false;
    protected $brokers;
    protected $producer;
    protected $topic;

    public function __construct(array $config=[])
    {
        if (!is_array($config['brokers']))
        {
            $this->brokers = explode(",",$config['brokers']);
        }
        else {
            $this->brokers = $config['brokers'];
        }

        $this->config = new \RdKafka\Conf();
        $this->config->set('log_level', (string) LOG_DEBUG);

        
        $this->config->set('metadata.broker.list', join(",",$this->brokers));
        

        $this->producer = new \RdKafka\Producer($this->config);
        $this->topic = $this->producer->newTopic($config['topic']);
    }
    


    public function setBulk(bool $bulk = false)
    {
        $this->bulk = $bulk;
    }

    public function getBulk()
    {
        return $this->bulk;
    }

    public function publish($message="")
    {
        $this->topic->produce(RD_KAFKA_PARTITION_UA, RD_KAFKA_MSG_F_BLOCK, $message);

            $this->producer->poll(0);

            if ($this->bulk === false){
                $this->flush();
            }
    }

    public function flush($retries = 10)
    {
        for ($flushRetries = 0; $flushRetries < $retries; $flushRetries++) {
            $result = $this->producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }
    }

}
