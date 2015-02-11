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

    public function handleSaveContent($json) {
        if($this->isAjax()) {
            $content = json_decode($json);
            $db = $this->getContext()->contentFacade;

            foreach($content as $id => $contentBox) {
                if($db->doesExistFrameWithId($id)) {
                    $db->updateFrame($id, $contentBox);
                } else {
                    $presenter = explode(":", $this->getName());
                    $page = $presenter[1].":".$this->getAction(); //tvar adresy aktulní stránky

                    $pageId = $db->getPageByLink($page)->getId();

                    $db->addFrame($id, $contentBox, $pageId);
                }
            }

            $this->payload->status = "ok";
            $this->sendPayload();
        }
    }

}