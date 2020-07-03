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
namespace Izytechab\Kafka\Console;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


final class ConsumerCommand extends AbstractConsumerCommand
{

	protected function configure(): void
	{
		$this->setName('kafka:consumer');
		$this->setDescription('Display messages from kafka server');

		$this->addArgument('consumerName', InputArgument::REQUIRED, 'Name of the consumer');
        $this->addArgument('consumerTopic', InputArgument::REQUIRED, 'Name of the topic');
        $this->addOption('debug',null, InputOption::VALUE_OPTIONAL, 'Debug messges',false);
        

	}


	/**
	 * @throws \InvalidArgumentException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$consumerName = $input->getArgument('consumerName');
		$consumerTopic = $input->getArgument('consumerTopic');
        $consumerDebug = $input->getOption('debug');

		if (!is_string($consumerName)) {
			throw new \UnexpectedValueException;
		}

		$this->validateConsumerName($consumerName,$consumerTopic);
        
       
        $consumer = $this->consumerFactory->getConsumer('default',$consumerName,$consumerTopic);
        
        //Handle debug option
        if ($consumerDebug === false)
        {

        }
        $consumer->setDebug($consumerDebug);

        $consumer->addCallbackMessage([$this,'doPrintMessage']);
        $consumer->addCallbackStatus([$this,'doPrintStatua']);
        $consumer->consume();


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
    

    public function doPrintMessage($message)
    {

        echo "{$message}\n";
    }

    public function doPrintStatus($status)
    {
        print_r($status);
    }
}