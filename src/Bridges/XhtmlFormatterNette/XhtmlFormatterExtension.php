<?php

/**
 *
 * This file is part of the Xhtml formatter
 *
 * Copyright (c) 2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace XhtmlFormatter\Bridges;

use Nette\DI\CompilerExtension;


class XhtmlFormatterExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('formatter'))
			->setClass('XhtmlFormatter\Formatter');
	}

}
