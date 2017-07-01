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

use XhtmlFormatter\Formatter;
use Tester\Assert;
use Tester\TestCase;


abstract class AbstractTestCase extends TestCase
{

	/**
	 * @var Formatter
	 */
	public $formatter;

	/**
	 * @var string
	 */
	private $actualTestsDirectoryNamePrefix;


	public function setUp()
	{
		parent::setUp();
		$this->setActualTestsDirectoryNamePrefix();
		$this->formatter = new Formatter;
	}


	protected function assertMatchFile(string $fileName, bool $rewriteExpectedFile = NULL)
	{
		if ($rewriteExpectedFile) {
			$this->rewriteExpectedFile($fileName);
		}

		Assert::matchFile(
			$this->getExpectedFilePath($fileName),
			$this->formatter->format($this->getCompiledFileContent($this->getActualFilePath($fileName))));
	}


	protected function assertSame(string $expected, string $actual)
	{
		Assert::same($expected . "\n", $this->formatter->format($actual));
	}


	protected function getActualFilePath(string $fileName): string
	{
		return $this->getActualDir() . '/' . $fileName . '.txt';
	}


	protected function rewriteExpectedFile(string $testName)
	{
		file_put_contents(
			$this->getExpectedFilePath($testName),
			$this->formatter->format($this->getCompiledFileContent($this->getActualFilePath($testName)))
		);
	}


	protected function setActualTestsDirectoryNamePrefix()
	{
		if ($this->actualTestsDirectoryNamePrefix) {
			return;
		}

		$childClassNamespace = explode('\\', get_class($this));
		$childClassName = str_replace('TestCase', '', end($childClassNamespace));
		$this->actualTestsDirectoryNamePrefix = $childClassName;
	}


	private function getActualDir(): string
	{
		return __DIR__ . '/' . $this->actualTestsDirectoryNamePrefix . 'Tests/Actual';
	}


	private function getCompiledFileContent(string $filePath): string
	{
		return file_get_contents($filePath);
	}


	private function getExpectedDir(): string
	{
		return __DIR__ . '/' . $this->actualTestsDirectoryNamePrefix . 'Tests/Expected';
	}


	private function getExpectedFilePath(string $fileName): string
	{
		return $this->getExpectedDir() . '/' . $fileName . '.txt';
	}

}
