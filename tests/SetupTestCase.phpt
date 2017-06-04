<?php

/**
 *
 * This file is part of the Xhtml-formatter
 *
 * Copyright (c) 2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace XhtmlFormatter\Tests;

require_once 'bootstrap.php';


class SetupTestCase extends AbstractTestCase
{

	public function testOutputIndentation()
	{
		$this->formatter->setSpacesIndentationMethod(4);
		$this->assertMatchFile('outputIndentation');
	}


	public function testUnpairedElementsAddition()
	{
		$this->formatter
			->addUnpairedElements('unpaired', $this->formatter::CONTENT_XML)
			->setContentType($this->formatter::CONTENT_XML);
		$this->assertMatchFile('unpairedElementsAddition');
	}

}

run(new SetupTestCase());
