<?php
namespace AdminModule\NetteExtension;

class Form extends \Nette\Application\UI\Form {
	
	public function addError($message)
    {
        $this->valid = FALSE;
        if ($message !== NULL) {
            $this->getPresenter()->flashMessage($message, 'error');
        }
    }

}