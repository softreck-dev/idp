#!/usr/bin/env php
<?php

$composerAutoload = [
    __DIR__ . '/vendor/autoload.php', // standalone with "composer install" run
    __DIR__ . '/../../autoload.php',  // script is installed as a composer binary
];
foreach ($composerAutoload as $autoload) {
    if (file_exists($autoload)) {
        require($autoload);
        break;
    }
}

// Send all errors to stderr
ini_set('display_errors', 'stderr');


$full = false;
$src = [];
foreach($argv as $k => $arg) {
    if ($k == 0) {
        continue;
    }
    if ($arg[0] == '-') {
        $arg = explode('=', $arg);
        switch($arg[0]) {
            case '--full':
                $full = true;
                break;
            case '-h':
            case '--help':
                echo "PHP Markdown to HTML converter\n";
                usage();
                break;
            default:
                error("Unknown argument " . $arg[0], "usage");
        }
    } else {
        $src[] = $arg;
    }
}

if (empty($src)) {
	$markdown = file_get_contents("php://stdin");
} elseif (count($src) == 1) {
	$file = reset($src);
	if (!file_exists($file)) {
		error("File does not exist:" . $file);
	}
	$markdown = file_get_contents($file);
} else {
	error("Converting multiple files is not yet supported.", "usage");
}

$Parsedown = new Parsedown();
$markup =  $Parsedown->text($markdown);

if ($full) {
	echo <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<style>
		body { font-family: Arial, sans-serif; }
		code { background: #eeeeff; padding: 2px; }
		li { margin-bottom: 5px; }
		img { max-width: 1200px; }
		table, td, th { border: solid 1px #ccc; border-collapse: collapse; }
	</style>
</head>
<body>
$markup
</body>
</html>
HTML;
} else {
	echo $markup;
}

// functions

/**
 * Display usage information
 */
function usage() {
	global $argv;
	$cmd = $argv[0];
	echo <<<EOF
Usage:
    $cmd [--flavor=<flavor>] [--full] [file.md]

    --flavor  specifies the markdown flavor to use. If omitted the original markdown by John Gruber [1] will be used.
              Available flavors:

              gfm   - Github flavored markdown [2]
              extra - Markdown Extra [3]

    --full    ouput a full HTML page with head and body. If not given, only the parsed markdown will be output.

    --help    shows this usage information.

    If no file is specified input will be read from STDIN.

Examples:

    Render a file with original markdown:

        $cmd README.md > README.html

    Render a file using gihtub flavored markdown:

        $cmd --flavor=gfm README.md > README.html

    Convert the original markdown description to html using STDIN:

        curl http://daringfireball.net/projects/markdown/syntax.text | $cmd > md.html


[1] http://daringfireball.net/projects/markdown/syntax


EOF;
	exit(1);
}

/**
 * Send custom error message to stderr
 * @param $message string
 * @param $callback mixed called before script exit
 * @return void
 */
function error($message, $callback = null) {
	$fe = fopen("php://stderr", "w");
	fwrite($fe, "Error: " . $message . "\n");

	if (is_callable($callback)) {
		call_user_func($callback);
	}

	exit(1);
}
