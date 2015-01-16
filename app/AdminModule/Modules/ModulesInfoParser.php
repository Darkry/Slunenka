<?php

namespace AdminModule\Modules;

/**
 * Parsing the info.xml files in each existing modul
 *
 * @author Kryštof Selucký
 */
class ModulesInfoParser {

    /** @var array */
    private $modules;

    /** @var Nette\Caching\Cache */
    private $cache;

    /** @var array */
    private $modulesInfo;

    /**
     * Initializing list of moduless
      -     * @param array $modules
      +     * @param array $modules
     * @return void
     */
    public function __construct($modules, $cache) {
        $this->modules = $modules;
        $this->cache = $cache;
    }

    /**
     * Parse all existing modules
     * @return void
     */
    public function parseModules() {
        if ($this->cache->load("modulesInfo") == NULL) {
            $parsed = array();
            foreach ($this->modules as $path => $module) {
                $xml = simplexml_load_file($path . "/info.xml");
                $parsed[$module] = new ModuleInfo($module, (string) $xml->realName);
                if ((string) $xml->editable === "true") {
                    $parsed[$module]->hasPrivilegies = ($xml->hasPrivilegies == "true");
                    $parsed[$module]->setEditable(true);
                    foreach ($xml->component as $component) {
                        if ((string) $component["state"] == "on") {
                            $parsed[$module]->addComponent((string) $component->name, (string) $component->realName, (string) ((string) $component["state"] == "on"));
                            foreach ($component->action as $action) {
                                $parsed[$module]->addAction((string) $action->name, (string) $action->realName, (string) $component->name, ((string) $action["state"] == "on"));
                            }
                            if (isset($component->options)) {
                                foreach ($component->options->set as $option)
                                    $parsed[$module]->addOption((string) $component->name, (string) $option["name"], ((string) $option == "true"));
                            }
                            if (isset($component->settings)) {
                                foreach ($component->settings->set as $set)
                                    $parsed[$module]->addSetting((string) $component->name, (string) $set["name"], (string) $set);
                            }
                        } else {
                            $parsed[$module]->addComponent((string) $component->name, (string) $component->realName, ((string) $component["state"] == "on"));
                        }
                    }
                } else {
                    $parsed[$module]->setEditable(false);
                }
            }
            $this->modulesInfo = $parsed;
            $this->cache->save("modulesInfo", $parsed);
        }
    }

    /**
     * Get parsed info of all modules
     * @return ModuleInfo[]
     */
    public function getModulesInfo() {
        if ($this->modulesInfo == NULL) {
            if ($this->cache->load("modulesInfo") == NULL)
                $this->parseModules();
            else
                $this->modulesInfo = $this->cache->load("modulesInfo");
        }
        return $this->modulesInfo;
    }

    /** Get parse info of one module
     *  @param $name module name
     *  @return ModuleInfo module info
     */
    public function getModuleInfo($name) {
        if ($this->modulesInfo == NULL) {
            if ($this->cache->load("modulesInfo") == NULL)
                $this->parseModules();
            else
                $this->modulesInfo = $this->cache->load("modulesInfo");
        }

        if (isset($this->modulesInfo[$name])) {
            return $this->modulesInfo[$name];
        } else {
            throw new \Exception("AdminModule\Modules\ModulesInfoParser -> undefined module name");
        }
    }

}