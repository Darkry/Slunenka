<?php

namespace FrontModule;

/**
 * This class is an abstract parent for all CMS presenter which will be avaible only if the user is logged in.
 *
 * @author Kryštof Selucký
 */
abstract class SecurityPresenter extends BasePresenter {

    public function isAllowedToEdit() {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect(":Admin:Login:Login:");
        }

        //TODO DODELAT: kontrola práv uživatele + kontrola zapnutí Text editu.

        return true;
    }

    public function edit($edit) {
    	if($edit == 1)
        	$this->template->edit = $this->isAllowedToEdit() ? true : false;
        else
        	$this->template->edit = false;
    }

}