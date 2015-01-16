<?php

namespace AdminModule\Modules;

/**
 * hold modul informations
 *
 * @author Kryštof Selucký
 * @package DarkAdmin
 */
class ModuleInfo extends \Nette\Object {

    /** @var name of module */
    public $moduleName;

    /** Can be module used in ACL system? */
    public $hasPrivilegies;

    /** @var real name of module in menu */
    public $realModuleName;
    private $editable;

    /**
     *
     * @var array - structure:
     * componentName => array
     *    -realName
     *    -state
     *    -actions => array
     *      -action[name] => array(realName, state)
     *      -action[name] => array(realName, state)
     *      - ...
     *    -options => array
     *      - option[name] => boolean
     *      - ...
     *    -settings => array
     *      - setting[name] => value
     *      - ...
     */
    private $components = array();

    /**
     *
     * @param string $moduleName original name of module
     * @param type $realModuleName real name of module in menu
     */
    public function __construct($moduleName, $realModuleName) {
        $this->moduleName = $moduleName;
        $this->realModuleName = $realModuleName;
    }

    /**
     *
     * @param bool $editable isModuleEditable
     * @return void
     */
    public function setEditable($editable) {
        $this->editable = $editable;
    }

    /**
     * is module editable?
     * @return void
     */
    public function isEditable() {
        return $this->editable;
    }

    /**
     * adds new component to our info of module
     * @param $name string name of component
     * @param $realName string real name of component (for using in front end)
     * @param $state bool component status
     * @return void
     */
    public function addComponent($name, $realName, $state) {
        if (!$this->isEditable())
            throw new \Exception("Cannot add component to uneditable module");
        if (isset($this->components[$name]))
            throw new \Exception("Adding existing component.");
        $this->components[$name]["realName"] = $realName;
        $this->components[$name]["state"] = $state;
    }

    /**
     * returns real name of module or false if module isnt editable
     * @param string $name name of component
     * @return string || boolean real name of component or false if module isnt editable
     */
    public function getComponentRealName($name) {
        if (!$this->isEditable() || !isset($this->components[$name]))
            throw new \Exception("Component does not exist or the module is not editable.");
        return $this->components[$name]["realName"];
    }

    public function getComponentState($name) {
        if (!$this->isEditable() || !isset($this->components[$name]))
            throw new \Exception("Component does not exist or the module is not editable.");
        return $this->components[$name]["state"];
    }

    /**
     * returns all components in module
     * @return array list of component or null
     */
    public function getComponents() {
        if (!$this->isEditable())
            throw new \Exception("Module is not editable.");

        return $this->components;
    }

    /**
     * Return all actions of component
     * @param string $name component name
     * @return array list of actions
     */
    public function getComponentActions($name) {
        if (!$this->isEditable() || !isset($this->components[$name]))
            throw new Exception("Component does not exist or the module is not editable.");
        if (count($this->components[$name]["actions"]) == 0)
            return null;

        return $this->components[$name]["actions"];
    }

    public function getActiveComponentActions($name) {
        if (!$this->isEditable() || !isset($this->components[$name]))
            throw new Exception("Component does not exist or the module is not editable.");
        if (count($this->components[$name]["actions"]) == 0)
            return null;

        $return = array();
        foreach ($this->components[$name]["actions"] as $actionName => $action) {
            if ($this->getActionState($name, $actionName) == true)
                $return[$actionName] = $action;
        }

        return $return;
    }

    /**
     * has component any actions?
     * if component is turned off, return FALSE
     * @param string $name component name
     * @return bool
     */
    public function hasActions($name) {
        if (isset($this->components[$name]["actions"])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add option
     * @param string $component
     * @param string $optionName
     * @param bool $status
     */
    public function addOption($component, $optionName, $status) {
        if (!$this->isEditable())
            throw new \Exception("Cannot add option to uneditable module.");

        if (!isset($this->components[$component]))
            throw new \Exception("Component does not exist.");
        if (isset($this->components[$component]["options"][$optionName]))
            throw new \Exception("Adding existing option");

        $this->components[$component]["options"][$optionName] = $status;
    }

    /**
     * Get options status
     * @param string $component
     * @param string $optionName
     * @return bool option status
     */
    public function getOption($component, $optionName) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exception("Component does not exist or the module is not editable.");

        if (!isset($this->components[$component]["options"][$optionName]))
            throw new \Exception("Option does not exist.");

        return $this->components[$component]["options"][$optionName];
    }

    /**
     * Returns all options of one component
     * @param string $componentName
     * @return array list of options
     */
    public function getComponentOptions($component) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exception("Component does not exist or the module is not editable.");

        return $this->components[$component]["options"];
    }

    /**
     * Add settings
     * @param string $component
     * @param string $settingName
     * @param bool $value
     */
    public function addSetting($component, $settingName, $value) {
        if (!$this->isEditable())
            throw new \Exception("Cannot add setting to uneditable module.");

        if (!isset($this->components[$component]))
            throw new \Exception("Component does not exist.");
        if (isset($this->components[$component]["settings"][$settingName]))
            throw new \Exception("Adding existing setting");

        $this->components[$component]["settings"][$settingName] = $value;
    }

    /**
     * Get options status
     * @param string $component
     * @param string $optionName
     * @return bool option status
     */
    public function getSetting($component, $settingName) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exception("Component does not exist or the module is not editable.");

        if (!isset($this->components[$component]["settings"][$settingName]))
            throw new \Exception("Setting does not exist.");

        return $this->components[$component]["settings"][$settingName];
    }

    /**
     * Returns all options of one component
     * @param string $componentName
     * @return array list of options
     */
    public function getComponentSettings($component) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exception("Component does not exist or the module is not editable.");

        return $this->components[$component]["settings"];
    }

    /**
     * Returns real name of an action
     * @param string $component
     * @param string $actionName
     * @return string real name of action
     */
    public function getActionRealName($component, $actionName) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exception("Component does not exist or the module is not editable.");

        if (!isset($this->components[$component]["actions"][$actionName]))
            throw new \Exception("Action does now exist");

        return $this->components[$component]["actions"][$actionName]["realName"];
    }

    /**
     * Returns state of action
     * @param string $component
     * @param string $actionName
     * @return bool state of action
     */
    public function getActionState($component, $actionName) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exception("Component does not exist or the module is not editable.");

        if (!isset($this->components[$component]["actions"][$actionName]))
            throw new \Exception("Action does now exist");

        return $this->components[$component]["actions"][$actionName]["state"];
    }

    /**
     * Adds action to module info
     * @param string $name
     * @param string $realName
     * @param string $component
     */
    public function addAction($name, $realName, $component, $state) {
        if (!isset($this->components[$component]) || !$this->isEditable())
            throw new \Exeption("Component does not exist or the module is not editable.");

        if (isset($this->components[$component]["actions"][$name]))
            throw new \Exception("Adding existing action");

        $this->components[$component]["actions"][$name] = array("realName" => $realName, "state" => $state);
    }

    /**
     * Returns module name
     * @return string module name
     */
    public function getModuleName() {
        return $this->moduleName;
    }

    /**
     * Returns real name of module
     * @return string module real name
     */
    public function getModuleRealName() {
        return $this->realModuleName;
    }

    public function hasModulePrivilegies() {
        return $this->hasPrivilegies;
    }

}