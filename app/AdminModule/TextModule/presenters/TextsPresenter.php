<?php
namespace AdminModule\TextModule;

use \AdminModule\NetteExtension\Form;

/**
 * Description of TextPresenter
 *
 * @author Kryštof
 */
class TextsPresenter extends \AdminModule\Security\SecuredPresenter {

    /** @var /Facade/TextFacade */
    private $facade;

    private $text;

    protected function startup() {
        parent::startup();

        if($this->moduleOptions->getComponentState("Texts") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->textFacade;
    }

    /**
     * Send application options to template
     * @return void
     */
    public function beforeRender() {
        parent::beforeRender();
        $this->template->options = $this->moduleOptions->getComponentOptions("Texts");
    }

    /**
     * Renders default template for texts list
     * @return void
     */
    public function renderDefault() {
        $vp = new \VisualPaginator($this, 'vp');

        $this->template->texts = $this->facade->getTextList($vp, 15);
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

    /**
     * Action for one text
     * @param int $id text id
     */
    public function actionText($id) {
        if(!$this->isAllowed("Texts", "editTexts"))
        {
            $this->flashMessage("Nemáte právo editovat texty!","error");
            $this->redirect("default");
        }
    }

    /**
     * Rendering one text template
     * @param int $id text id
     */
    public function renderText($id) {
        $this->text = $this->facade->getTextById($id);
        $this->template->text = $this->text;
    }

    /**
     * Creates component for editing text
     * @return Form form ready to render
     */
    public function createComponentEditTextForm() {
        if($this->text == NULL)
            $this->text = $this->facade->getTextById($this->getParam("id"));
        $form = new Form();
        if($this->isOptionAllowed("Texts", "name"))
            $form->addText("name", "Název: ")->setDefaultValue($this->text->getName());

        if($this->isOptionAllowed("Texts", "author"))
            $form->addText("author", "Autor: ")->setDefaultValue($this->text->getAuthor());

        if($this->isOptionAllowed("Texts", "date"))
            $form->addDatePicker("date", "Datum: ")->addRule(Form::VALID, 'Neplatný formát data!')->setDefaultValue($this->text->getDate());

        if($this->isOptionAllowed("Texts", "shortVersion"))
                $form->addTextArea("shortVersion", "Zkrácená verze: ")->setRequired()
                                                                  ->setAttribute("class","ckeditor")
                                                                  ->setDefaultValue($this->text->getShortVersion());

        $form->addTextArea("text", "Text: ")->setRequired()->setAttribute("class","ckeditor")->setDefaultValue($this->text->getText());
        $form->addSubmit("submit", "Upravit text");
        $form->onSuccess[] = callback($this, "editTextFormSubmitted");

        return $form;
    }

    /**
     * Proccess submitted form for editing text
     * @param Form $form submitted form
     */
    public function editTextFormSubmitted(Form $form) {
        if(!$this->isAllowed("Texts", "editTexts"))
            $this->flashMessage ("Nemáte práva pro upravování textů!", "error");
        else {
            $val = $form->getValues();
            $this->facade->updateText($this->text->getId(), $val);
            $this->addEvent("Uživatel $this->loggedUserName upravil <a href='".$this->lazyLink(':Admin:Text:Texts:text', $this->text->getId())."'>text s id ".$this->text->getId()."</a>.");
            $this->flashMessage("Text byl úspěšně upraven", "success");
        }
        $this->redirect("default");
    }
}