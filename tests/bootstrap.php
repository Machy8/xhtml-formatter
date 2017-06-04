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

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

function run(Tester\TestCase $testCase)
{
	$testCase->run();
}
