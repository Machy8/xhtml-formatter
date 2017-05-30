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

namespace XhtmlFormatter\Tests;

require_once 'bootstrap.php';


class FormattingTestCase extends AbstractTestCase
{

	public function testCodePlaceholders()
	{
		$this->assertMatchFile('codePlaceholders');
	}


	public function testCompressedFileFormatting()
	{
		$this->assertMatchFile('compressed');
	}


	public function testLatteFileFormatting()
	{
		$this->assertMatchFile('latte');
	}


	public function testPhpOnlyFileFormatting()
	{
		$this->assertMatchFile('phpOnly');
	}


	public function testPhtmlFileFormatting()
	{
		$this->assertMatchFile('phtml');
	}


	public function testUnformattedFileFormatting()
	{
		$this->assertMatchFile('unformatted');
	}


	public function testXmlFileFormatting()
	{
		$this->formatter->setContentType($this->formatter::CONTENT_XML);
		$this->assertMatchFile('xml');
	}

}

run(new FormattingTestCase());
