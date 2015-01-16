<?php

/**
 * Base class for all application presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected function startup() {
        parent::startup();
        \Panel\User::register()
            ->addCredentials('admin', 'heslo')
            ->addCredentials('editor', 'heslo')
            ->addCredentials('textEditor', 'heslo')
            ->addCredentials('imageEditor','heslo');
    }
}
