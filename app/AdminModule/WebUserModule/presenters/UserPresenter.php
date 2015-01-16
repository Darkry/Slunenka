<?php
namespace AdminModule\WebUserModule;

use \AdminModule\NetteExtension\Form;

/**
 * Description of TextPresenter
 *
 * @author Kryštof
 */
class UserPresenter extends \AdminModule\Security\SecuredPresenter {

    /** @var /Facade/TextFacade */
    private $facade;

    private $text;

    protected function startup() {
        parent::startup();

        if($this->moduleOptions->getComponentState("UserList") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        //$this->facade = $this->getContext()->webUserFacade
    }

    /**
     * Send application options to template
     * @return void
     */
    public function beforeRender() {
        parent::beforeRender();
    }

    /**
     * Renders default template for texts list
     * @return void
     */
    public function renderDefault() {
        $vp = new \VisualPaginator($this, 'vp');

        //$this->template->texts = $this->facade->getTextList($vp, 15);
    }

    /**
     * Deletes one text
     * @param int $id id text to delete
     * @return void
     */
    public function handleDeleteText($id) {
        if($this->isAllowed('Texts', 'deleteTexts')) {
            $this->facade->deleteText($id);
            $this->addEvent("Uživatel $this->loggedUserName odstranil text s ID $id.");
            $this->flashMessage("Text byl úspěšně smazán!", "success");
        }
        else
            $this->flashMessage("Nemáte potřebná oprávnění pro mazání textů.", "error");

        if($this->isAjax()) {
            $this->invalidateControl("textTable");
            $this->invalidateControl("flashMessages");
        }
        else
            $this->redirect("this");
    }

}