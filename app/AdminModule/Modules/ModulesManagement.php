<?php
namespace AdminModule\Modules;

use \Nette\Caching\Cache;

/**
 * Using Nette RobotLoader determine which modules exist in AdminModule
 * @author Kryštof Selucký
 */
class ModulesManagement {
    /** @var \Nette\Loaders\RobotLoader */
    private $robot;

    /** @var \Nette\Caching\Cache */
    private $cache;

    /**
     * @param \Nette\Loaders\RobotLoader $robotLoader
     * @param \Nette\Caching\Cache $cache
     * @return void
     */
    public function __construct(\Nette\Loaders\RobotLoader $robotLoader, \Nette\Caching\Cache $cache) {
        $this->robot = $robotLoader;
        $this->cache = $cache;
    }

    /**
     * Return all indexed classes
     * @return type array
     */
    public function getClasses() {
        return $this->robot->getIndexedClasses();
    }

    /**
     * returns all existing modules in AdminModule
     * @return type array $existingModels
     */
    public function getExistingModules() {
        if($this->cache->load("existingModules") !== NULL) {
            return $this->cache->load("existingModules");
        }
        else {
            $existingClasses = $this->getClasses();
            $existingModules = array();
            foreach($existingClasses as $fullName => $path)
            {
                $explode = explode("\\", $fullName);
                if($explode[0] == "AdminModule" && !in_array($explode[1], $existingModules) && preg_match("~[a-zA-Z]+Module~",$explode[1]) !== 0) {
                        $path = pathinfo($path, PATHINFO_DIRNAME);
                        $path = str_replace("/presenters", "", $path);
                        $path = str_replace("\presenters", "", $path);
                        $existingModules[$path] = $explode[1];
                }
            }
            $this->cache->save("existingModules", $existingModules);
            return $existingModules;
        }
    }
}