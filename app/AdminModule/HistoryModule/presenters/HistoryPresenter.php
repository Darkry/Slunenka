<?php

namespace AdminModule\HistoryModule;

use \Nette\Forms\Form;

class HistoryPresenter extends \AdminModule\Security\SecuredPresenter {

    private $facade;

    public function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("History") === false
            || $this->isAllowed("History","viewHistory") === false
            || $this->isAllowed("History","viewHistory") === NULL) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->historyFacade;
    }

    public function renderDefault() {
        $vp = new \VisualPaginator($this, 'vp');

        $this->template->events = $this->facade->getEventsList($vp, 15);
    }

}
