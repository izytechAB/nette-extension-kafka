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

namespace Izytechab\Kafka\DI;

use \Nette\DI\CompilerExtension as CompilerExtension;

use Izytechab\Kafka\DI\Helpers\ProducersHelper;

use Izytechab\Kafka\Console\ProducerCommand;
use Izytechab\Kafka\Console\ConsumerCommand;


/**
 * Kafka extension for Nette 3.x framework
 * 
 *
 * @author Thomas Alatalo Berg
 */
final class KafkaExtension extends CompilerExtension
{

    public $panel;

    public $defaults = [
        'brokers' => ['kafka']
    ];

    

    public $helpers = [];

    public function __construct()
    {
        $this->helpers['producers'] = new \Izytechab\Kafka\DI\Helpers\ProducersHelper($this);
        $this->helpers['consumers'] = new \Izytechab\Kafka\DI\Helpers\ConsumersHelper($this);
        
    }


    /**
     * Processes configuration data
     *
     * @return void
     */
    public function loadConfiguration()
    {
            
        $config = $this->getConfig($this->defaults);
        $builder = $this->getContainerBuilder();
    
        $config['prefix'] = $this->prefix('');
    

        //Setup all created helpers
        foreach ($this->helpers as $name=>$helper)
        {
            $helper->setup($builder,$config[$name]);
        }

        //Setup
        $this->setupConsoleCommand();


    }



    public function setupConsoleCommand():void
    {
		$builder = $this->getContainerBuilder();
        
		$builder->addDefinition($this->prefix('console.producerCommand'))
			->setFactory(ProducerCommand::class)
            ->setTags(['console.command' => 'kafka:producer']);    

        $builder->addDefinition($this->prefix('console.consumerCommand'))
			->setFactory(ConsumerCommand::class)
            ->setTags(['console.command' => 'kafka:consumer']);    
    }
            

}