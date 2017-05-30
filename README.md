# XML (X)HTML formatter / beautifier

[![Build Status](https://travis-ci.org/Machy8/xhtml-formatter.svg?branch=0.1)](https://travis-ci.org/Machy8/xhtml-formatter)

````
composer require machy8/xhtml-formatter
````

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
$formatter = new XhtmlFormatter\Formatter();
$output = $formatter->format($string);
````
