<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Plugin;
use \RebelCode\EddBookings\System\LoopMachine;
use \RebelCode\EddBookings\System\Module\Module;
use \RebelCode\EddBookings\System\ModuleLoader\JsonModuleLoader;
use \Symfony\Component\Finder\Finder;

return array(
    'factory' => function() {
        return eddBkContainer();
    },
    'autoloader' => function() {
        return eddBkAutoloader();
    },
    'plugin' => function(ContainerInterface $c) {
        return new Plugin($c, $c);
    },
    // simle alias
    'app' => function(ContainerInterface $c) {
        return $c->get('plugin');
    },
    'loop_machine' => function() {
        return new LoopMachine();
    },
    'module_dirs' => function() {
        return array(EDD_BK_MODULES_DIR);
    },
    'module_finder' => function(ContainerInterface $c) {
        $dirs   = $c->get('module_dirs');
        $finder = new Finder();

        $finder
            ->directories()
            ->depth('== 0')
            ->in($dirs)
        ;

        return $finder;
    },
    'module_loader' => function(ContainerInterface $c) {
        return new JsonModuleLoader($c->get('plugin'), $c, $c->get('factory'), $c->get('autoloader'));
    },
    'module' => function(ContainerInterface $c, $previous = null, array $config = array()) {
        return new Module($config);
    }
);
