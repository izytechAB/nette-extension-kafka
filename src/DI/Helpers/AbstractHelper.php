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

use Izytechab\Kafka\DI\KafkaExtension;

abstract class AbstractHelper
{

	/**
	 * @var array
	 */
	protected $defaults = [];

	protected $extension;


	public function __construct(KafkaExtension $extension)
	{
		$this->extension = $extension;
	}


	public function getDefaults(): array
	{
		return $this->defaults;
	}

}