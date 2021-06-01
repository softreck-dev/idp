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


$file = "README.md";

if (!file_exists($file)) {
    error("File does not exist:" . $file);
}
$markdown = file_get_contents($file);

$Parsedown = new Parsedown();
$markup =  $Parsedown->text($markdown);

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
