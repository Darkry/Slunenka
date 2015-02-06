<?php

/**
 * My Application bootstrap file.
 */
use Nette\Diagnostics\Debugger,
	Nette\Application\Routers\Route,
        Nette\Application\Routers\RouteList;


// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Enable Nette Debugger for error visualisation & logging
Debugger::$logDirectory = __DIR__ . '/../log';
Debugger::$strictMode = TRUE;
Debugger::enable();


// Configure application
$configurator = new Nette\Config\Configurator;
$configurator->setTempDirectory(__DIR__ . '/../temp');

// Enable RobotLoader - this will load all classes automatically
$robot = $configurator->createRobotLoader();

$robot->addDirectory(APP_DIR)
      ->addDirectory(LIBS_DIR)
      ->register();

\Nella\NetteAddons\Doctrine\Config\Extension::register($configurator);
\Nella\NetteAddons\Doctrine\Config\MigrationsExtension::register($configurator);

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

$cache = new Nette\Caching\Cache($container->getService("cacheStorage"));
$container->addService("cache", new Nette\Caching\Cache($container->getService("cacheStorage")));
$container->addService('modulesManagement', new AdminModule\Modules\ModulesManagement($robot, $cache));
$container->addService('modulesInfoParser', new AdminModule\Modules\ModulesInfoParser($container->getService("modulesManagement")->getExistingModules(), $cache));

$container->addService("aclFacade", new Facade\AclFacade($container->getService("database"), $container->getService("modulesInfoParser")));
$container->addService("textFacade", new Facade\TextFacade($container->getService("database")));
$container->addService("userFacade", new Facade\UserFacade($container->getService("database")));
$container->addService("historyFacade", new Facade\HistoryFacade($container->getService("database")));
$container->addService("galleryFacade", new Facade\GalleryFacade($container->getService("database")));
$container->addService("listFacade", new Facade\ListFacade($container->getService("database")));
$container->addService("contentFacade", new Facade\ContentFacade($container->getService("database")));

$container->addService("acl", new AdminModule\Security\Acl($container->getService("aclFacade"), $cache, $container->getService("modulesInfoParser")));

Nette\Utils\Html::$xhtml = false;

// Opens already started session
if ($container->session->exists()) {
    $container->session->start();
}

 	$container->router[] = new Route('index.php', 'Front:Default:default', Route::ONE_WAY);

	$container->router[] = $adminRouter = new RouteList('Admin');
	$adminRouter[] = new Route('admin/<module>/<presenter>/<action>', array(
            "module" => "Index",
            "presenter" => "Index",
            "action" => "default"
        ));

	$container->router[] = $frontRouter = new RouteList('Front');
  $frontRouter[] = new Route('<presenter>/<action>/[/<id>]', 'Homepage:default');

// Configure and run the application!
$application = $container->application;
//$application->catchExceptions = TRUE;
$application->errorPresenter = 'Error';

if (PHP_SAPI == 'cli') {
	$container->console->run();
} else {
	$container->application->run();
}
