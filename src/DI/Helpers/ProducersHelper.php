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


namespace Izytechab\Kafka\DI\Helpers;

use Izytechab\Kafka\DI\Helpers\AbstractHelper;

use Izytechab\Kafka\Producer\Producer;
use Izytechab\Kafka\Producer\Factory;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

final class ProducersHelper extends AbstractHelper
{

	
    
	/**
	 * @var array
	 */
	protected $defaults = [
		'brokers' => ['kafka'],
		'topics' => ['default'],
	];


	public function setup(ContainerBuilder $builder,array $config = []): ServiceDefinition
	{

        $producersConfig = [];
		foreach ($config as $producerName => $producerData) {
            $producerConfig = $this->extension->validateConfig($this->getDefaults(),$producerData);
			$producersConfig[$producerName] = $producerConfig;
        }

    	return $builder->addDefinition($this->extension->prefix('producerFactory'))
            ->setFactory(Factory::class)
            
			->setArguments([$producersConfig]);
	}
}