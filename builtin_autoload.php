<?php
/**
 * Baobab Built-in Autoload File
 *
 * This file can be used in the cases where composer is not
 * being used for the project.
 *
 * @package  Baobab
 * @author   Joshua Sello <joshuasello@gmail.com>
 * .
 */


require_once __DIR__ . "/src/functions.php";


\spl_autoload_register(function ($cls) {
	// also removes the Baobab part of the namespace.
    $path = __DIR__ .
        DIRECTORY_SEPARATOR .
        "src".
        DIRECTORY_SEPARATOR .
        \ltrim(
        	\str_replace(
        		"\\",
        		DIRECTORY_SEPARATOR,
        		$cls
    	) . ".php", "Baobab" . DIRECTORY_SEPARATOR);
    if (\file_exists($path)) {
        require_once $path;
    } else {
        echo $path . PHP_EOL;
    }
});
