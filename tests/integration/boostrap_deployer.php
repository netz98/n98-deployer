<?php

$bootstrapDeployer = function () {
// Deployer constants
    define('DEPLOYER', true);
    define('DEPLOYER_BIN', __FILE__);

    $deployFile = __DIR__ . '/deploy.php';

    $pathToVendor = getcwd() . '/vendor';
    $deployFilePath = dirname($deployFile);

    $autoload = ["$pathToVendor/autoload.php"];

    $includes = ["$pathToVendor/deployer/deployer"];

    $loaded = false;
    $includePath = false;

    $count = count($autoload);
    for ($i = 0; $i < $count; $i++) {
        if (file_exists($autoload[$i]) && file_exists($includes[$i])) {
            require $autoload[$i];
            $includePath = $includes[$i];
            $loaded = true;
            break;
        }
    }

    if (!$loaded) {
        die(
            'You need to set up the project dependencies using the following commands:' . PHP_EOL .
            'wget http://getcomposer.org/composer.phar' . PHP_EOL .
            'php composer.phar install' . PHP_EOL
        );
    }

// Setup include path
    set_include_path($includePath . PATH_SEPARATOR . get_include_path());

// Detect version
    $version = 'master';

// Init Deployer
    $console = new \Deployer\Console\Application('Deployer', $version);
    $input = new \Symfony\Component\Console\Input\ArgvInput();
    $output = new \Symfony\Component\Console\Output\ConsoleOutput();
    $deployer = new \Deployer\Deployer($console, $input, $output);

};
$bootstrapDeployer();
unset($bootstrapDeployer);