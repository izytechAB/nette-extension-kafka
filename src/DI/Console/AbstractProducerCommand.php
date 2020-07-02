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

namespace Izytechab\Kafka\Console\Command;

use Izytechab\Kafka\Producer\Factory as ProducerFactory;

use Symfony\Component\Console\Command\Command;

abstract class AbstractProducerCommand extends Command
{

	protected $producerFactory;


	public function __construct(ProducerFactory $producerFactory)
	{
		parent::__construct();
		$this->producerFactory = $producerFactory;
	}


	/**
	 * @throws \InvalidArgumentException
	 */
	protected function validateProducerName(string $name,string $topic): void
	{
		try {
			$this->producerFactory->getProducer($name,$topic);

		} catch (ConsumerFactoryException $e) {
			throw new \InvalidArgumentException(
				sprintf(
					"Producer [$name] does not exist. \n\n Available producers: %s",
					implode('', array_map(function($s): string {
						return "\n\t- [{$s}]";
					}, ['aaaaaaa']))
				)
			);
		}
	}

}