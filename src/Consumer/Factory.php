<?php declare(strict_types=1);
/**
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

use Izytechab\Kafka\Consumer\Consumer;
use Izytechab\Kafka\Config\Config;


final class Factory
{
    
	/**
	 * @var Consumer[]
	 */
	private $consumer = [];
    
    private $config = [];

	public function __construct($config=[]) 
    {
        $this->config = $config;
	}


	/**
	 * @throws ConsumerFactoryException
	 */
	public function getConsumer(string $group="default", string $name,string $topic): Consumer
	{

		if (!isset($this->consumer["{$name}:{$topic}"])) {
            $this->consumer["{$group}:{$name}:{$topic}"] = $this->create($group,$name,$topic);
            
		}
		return $this->consumer["{$group}:{$name}:{$topic}"];
	}

	/**
	 * @throws ConsumerFactoryException
	 */
	private function create(string $group, string $name,string $topic): Consumer
	{

        /**
         * @todo fix validation of configured topic
         */


        //$this->config[$name]['topic'] = $topic;

        $consumer = new Consumer();

        $consumer->setName($name);
        $consumer->setGroup($group);
        $consumer->setTopic($topic);
        $consumer->setBrokers($this->config[$name]['brokers']);

		return $consumer;
	}

}