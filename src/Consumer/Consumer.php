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

namespace Izytechab\Kafka\Consumer;

final class Consumer
{

    use \Nette\SmartObject;

    protected $consumer;
    protected $callbacks = [];
    protected $config;
    protected $childs = [];
    protected $brokers;
    protected $debug = false;
    protected $level = 'low';

    protected $group = "default";
    protected $name = "default";
    protected $topic = "default";
    

    public function __construct()
    {
        pcntl_async_signals(true);
        $mypid = posix_getpid();
    }

    public function setBrokers($brokers)
    {
        if (!is_array($brokers))
        {
            $this->brokers = explode(",",$brokers);
        }
        else 
        {
            $this->brokers = $brokers;
        }

    }

    public function getBrokers()
    {
        return $this->brokers;
    }

    public function setLevel($level = "low")
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setTopic($topic)
    {    
        if (!is_array($topic))
        {
            $this->topic = explode(",",$topic);
        }
        else 
        {
            $this->topic = $topic;
        }
       
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setName($name)
    {
        $this->name = $name;        
    }

    public function getName()
    {
        return $this->name;        
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setDebug($debug = false)
    {
        if ($debug == 'all')
        {
            $this->config->set('debug', 'all');
            $this->debug = $debug;
        }
        elseif ($debug == 'kafka')
        {
            $this->config->set('debug', 'all');
            $this->debug = $debug;
        }
        elseif ($debug == 'consumer')
        {
            $this->debug = $debug;
        }

    }

    public function getDebug()
    {
        return $this->debug;
    }


    public function setCallbacks($callbacks = [])
    {
        $this->callbacks = $callbacks;
    }

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    public function addCallback(callable $callback)
    {
        $this->callbacks[]= $callback;
        
    }


    public function doCallback($message="")
    {
        pcntl_async_signals(true);
       

        $pid = pcntl_fork();


        if ($pid===0){

            foreach ($this->getCallbacks() as $callback)
            {
                $callback($message);
            }

            $mypid = posix_getpid();

            posix_kill($mypid,SIGKILL);
            exit(SIGCHLD);
        }
        else {
            $this->childs[$pid]=time();
        }
      
        
        

    }

    public function garbageCollaction()
    {
        foreach($this->childs as $pid => $starttime){
            $check = pcntl_waitpid($pid, $status, WNOHANG | WUNTRACED);
            switch($check){
                case $pid:
                    //echo "ended successfully {$pid}\n";
                    unset($this->childs[$pid]);
                    break;
                case 0:
                    if ( pcntl_wifstopped( $status ))
                    {

                        if(!posix_kill($pid,SIGKILL)){
                            trigger_error('Failed to kill '.$pid.': '.posix_strerror(posix_get_last_error()), E_USER_WARNING);
                        }
                        pcntl_waitpid($pid);
                        unset($this->childs[$pid]);
                    }
                    break;
                case -1:
                    //echo "case -1\n";
                default:
                    trigger_error('Something went terribly wrong with process '.$pid, E_USER_WARNING);
                    //@todo ? kill
                    //@todo ? unset it from $this->children[$pid]
                    break;

            }
     
        }
    }

    public function debugMsg($msg)
    {
        if (($this->debug == 'all') || ($this->debug == 'consumer'))
        {
            echo "{$msg}\n";
        }
    }

    public function initConsumer()
    {
        $this->debugMsg("Init consumer");

        //set rdkafka config defaults
        $this->config = new \RdKafka\Conf();
        //$this->config->set('log_level', (string) LOG_DEBUG);
        //$this->config->set('debug', 'all');

        $this->config->set('group.id', $this->getGroup());
        
        $this->config->set('metadata.broker.list', join(",",$this->brokers));
        
        $this->config->set('auto.offset.reset', 'smallest');
        $this->config->set('enable.auto.commit','true');
        $this->config->set('auto.commit.interval.ms', '50');
        $this->config->set('batch.num.messages', '1');
        $this->config->set('heartbeat.interval.ms','10');

        //$this->consumer = new \RdKafka\KafkaConsumer($this->config);

        


        // Set a rebalance callback to log partition assignments (optional)
        $this->config->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    //echo "Assign: ";
                    //var_dump($partitions);
                    $kafka->assign($partitions);
                    break;
        
                    case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                        //echo "Revoke: ";
                        //var_dump($partitions);
                        $kafka->assign(NULL);
                        break;
        
                    default:
                    throw new \Exception($err);
            }
        });
        




        $this->consumer = new \RdKafka\KafkaConsumer($this->config);
        
        //register consumer how? callback?

    }



    public function consume()
    {
        $this->initConsumer();


        $this->consumer->subscribe($this->getTopic());

        $this->debugMsg("Subscribe {$topic}");
        $this->debugMsg("Waiting for partition assignment... (make take some time when quickly re-joining the group after leaving it.)");


        while (true) {

            $message = $this->consumer->consume(1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    if ($message->err !== null){
                        $this->doCallback($message->payload);
                    }                      
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $this->debugMsg("No more messages; will wait for more");
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $this->debugMsg("Timed out");
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }

            $this->garbageCollaction();

        }

        
    }


}