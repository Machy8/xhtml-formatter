# XML + (X)HTML formatter / beautifier

[![Build Status](https://travis-ci.org/Machy8/xhtml-formatter.svg?branch=0.1)](https://travis-ci.org/Machy8/xhtml-formatter)

## Installation
````
composer require machy8/xhtml-formatter
````

## Usage
Compiles this 💩
````HTML
<!DOCTYPE html>
<html lang="en">
    <head><meta charset="utf-8"><title>title</title>
    <link rel="stylesheet" href="style.css">
<script src="script.js"></script></head><body><!-- page content --></body></html>
````

into this 😱😭
````HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>
			title
		</title>
		<link rel="stylesheet" href="style.css">
		<script src="script.js"></script>
	</head>
	<body>
		<!-- page content -->
	</body>
</html>
````

and all you need is
````PHP
use XhtmlFormatter\Formatter;

$formatter = new Formatter();

$output = $formatter->format($string);
````

and if you want to disable formatting
````HTML

<div>
	<formatter-off>
		<p>
			<b>Unformatted code goes <u>here</u></b>!
		</p>
	</formatter-off>
</div>

````

## Setup

````PHP
$formatter

	// Change the content type from CONTENT_HTML (default) to CONTENT_XML or CONTENT_XHTML
	->setContentType(Formatter::CONTENT_XML)

	// Add new unpaired element
	->addUnpairedElement('element', Formatter::CONTENT_XML)

	// Add skipped elements
	->addSkippedElement('elementA elementB')

	// Indent file by 4 spaces instead of tabs
	->setSpacesIndentationMethod(4);

````
