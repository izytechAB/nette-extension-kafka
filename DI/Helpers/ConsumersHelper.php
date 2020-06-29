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


namespace Extensions\Kafka\DI\Helpers;

use Extensions\Kafka\DI\Helpers\AbstractHelper;

use Extensions\Kafka\Consumer\Consumer;
use Extensions\Kafka\Consumer\Factory;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

final class ConsumersHelper extends AbstractHelper
{

	
    
	/**
	 * @var array
	 */
	protected $defaults = [
		'brokers' => ['kafka'],
        'topics' => ['default'],
        'level'=>'low',
	];



	public function setup(ContainerBuilder $builder,array $config = []): ServiceDefinition
	{

        $consumersConfig = [];
		foreach ($config as $consumerName => $consumerData) {
            $consumerConfig = $this->extension->validateConfig($this->getDefaults(),$consumerData);
			$consumersConfig[$consumerName] = $consumerConfig;
        }

    	return $builder->addDefinition($this->extension->prefix('consumerFactory'))
            ->setFactory(Factory::class)
			->setArguments([$consumersConfig]);
    }
    
}