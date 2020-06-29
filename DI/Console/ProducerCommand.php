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
namespace Extensions\Kafka\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


final class ProducerCommand extends AbstractProducerCommand
{

	protected function configure(): void
	{
		$this->setName('kafka:producer');
		$this->setDescription('Publish a message trough a producer');

		$this->addArgument('producerName', InputArgument::REQUIRED, 'Name of the producer');
        $this->addArgument('producerTopic', InputArgument::REQUIRED, 'Name of the topic');
        $this->addArgument('message', InputArgument::REQUIRED, 'Message to publish');
        

	}


	/**
	 * @throws \InvalidArgumentException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$producerName = $input->getArgument('producerName');
		$producerTopic = $input->getArgument('producerTopic');
		$message = $input->getArgument('message');

		if (!is_string($producerName)) {
			throw new \UnexpectedValueException;
		}

		$this->validateProducerName($producerName,$producerTopic);
        
        /*
		if ($secondsToLive !== null) {
			if (!is_numeric($secondsToLive) || is_array($secondsToLive)) {
				throw new \UnexpectedValueException;
			}

			$secondsToLive = (int) $secondsToLive;
			$this->validateSecondsToRun($secondsToLive);
		}
        */
        $producer = $this->producerFactory->getProducer($producerName,$producerTopic);
        
		$producer->publish($message);

		return 0;
	}


	/**
	 * @throws \InvalidArgumentException
	 */
	private function validateSecondsToRun(int $secondsToLive): void
	{
		if ($secondsToLive <= 0) {
			throw new \InvalidArgumentException(
				'Parameter [secondsToLive] has to be greater then 0'
			);
		}
	}
}