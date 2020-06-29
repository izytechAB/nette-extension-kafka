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

namespace Extensions\Kafka\Producer;

use Extensions\Kafka\Producer\Producer;
use Extensions\Kafka\Config\Config;
//use Contributte\RabbitMQ\Exchange\ExchangeFactory;
//use Contributte\RabbitMQ\Producer\Exception\ProducerFactoryException;
//use Contributte\RabbitMQ\Queue\QueueFactory;

final class Factory
{
    
	/**
	 * @var Producer[]
	 */
	private $producers = [];
    
    public $config = [];

	//public function __construct(ProducersDataBag $producersDataBag, QueueFactory $queueFactory,ExchangeFactory $exchangeFactory) 
	public function __construct($config=[]) 
    {
        $this->config = $config;
	}


	/**
	 * @throws ProducerFactoryException
	 */
	public function getProducer(string $name,string $topic): Producer
	{

		if (!isset($this->producers["{$name}:{$topic}"])) {
			$this->producers["{$name}:{$topic}"] = $this->create($name,$topic);
		}
		return $this->producers["{$name}:{$topic}"];
	}

	/**
	 * @throws ProducerFactoryException
	 */
	private function create(string $name,$topic): Producer
	{

        /**
         * @todo fix validation of configured topic
         */


        $this->config[$name]['topic'] = $topic;

		return new Producer($this->config[$name]);
	}

}