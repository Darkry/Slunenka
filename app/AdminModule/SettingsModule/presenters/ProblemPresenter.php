<?php
namespace AdminModule\SettingsModule;

use \AdminModule\NetteExtension\Form;
/**
 * Allows administrators to report a problem with this administration system.
 *
 * @package DarkAdmin
 * @author Kryštof Selucký
 */
class ProblemPresenter extends \AdminModule\Security\SecuredPresenter {

    private $facade;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("Problem") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->userFacade;
    }

    /**
     * Creates form for contacting creator of the CMS
     * @return Form created form
     */
    public function createComponentContactForm() {
        $form = new Form();

        $email = $this->facade->getUser($this->getUser()->getId())->getEmail();

        $form->addText("email", "Váš e-mail: ")
             ->setDefaultValue($email)
             ->setRequired("Musíte vyplnit Váš e-mail!")
             ->addRule(Form::EMAIL, "Zadaný e-mail má neplatný formát!");

        $form->addText("subject","Předmět: ")
             ->setRequired("Musíte vyplnit předmět!");
        $form->addTextArea("text","Popis problému: ")
             ->setRequired("Vyplňte prosím popis problému.");

        $form->addSubmit("submit", "Odeslat zprávu");

        $form->onSuccess[] = callback($this, "contactFormSubmitted");

        return $form;
    }

    /**
     * Sends message from user
     * @param Form $form submitted form
     */
    public function contactFormSubmitted(Form $form) {
        $val = $form->getValues();

        $mail = new \Nette\Mail\Message();
        $mail->setFrom($val->email);
        $mail->addTo("kry6@seznam.cz");
        $mail->setSubject("DarkAdmin support - ".$val["subject"]);
        $mail->setBody($val["text"]);
        $mail->send();

        $this->flashMessage("Zpráva byla úspěšně odeslána! Pokusíme se Vám odpovědět co nejdříve.", "success");
        $this->redirect("this");
    }

}