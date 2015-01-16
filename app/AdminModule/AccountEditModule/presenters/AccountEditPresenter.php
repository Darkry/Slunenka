<?php

namespace AdminModule\AccountEditModule;

use \Nette\Forms\Form;

class AccountEditPresenter extends \AdminModule\Security\SecuredPresenter {

    private $facade;
    private $authenticator;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("accountEditation") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->userFacade;
        $this->authenticator = $this->getContext()->authenticator;
    }

    public function isTurnedOn($optionName) {
        return parent::isOptionAllowed("accountEditation", $optionName);
    }

    public function createComponentChangePasswordForm() {
        $form = new \Nette\Application\UI\Form();
        $authenticator = $this->authenticator;
        $userName = $this->getUser()->getIdentity()->userName;

        $form->addPassword("oldPassword", "Staré heslo: ")->setRequired("Vyplňte prosím Vaše původní heslo.")
                ->addRule(function ($control) use ($authenticator, $userName) {
                            return $authenticator->canBeAuthenticated(array($userName, $control->value));
                        }, "Zadané staré heslo je neplatné.");
        $form->addPassword("newPassword", "Nové heslo: ")->setRequired("Vyplňte prosím Vaše nové heslo.")->addRule(Form::MIN_LENGTH, "Zadané heslo je příliš krátké.", 6);
        $form->addPassword("newPasswordAgain", "Nové heslo znovu:")
                ->setRequired("Vyplňte prosím Vaše nové heslo ještě jednou pro ověření.")
                ->addRule(Form::EQUAL, 'Zadaná hesla se musí shodovat.', $form['newPassword']);
        $form->addSubmit("submit", "Změnit heslo");

        $form->onSuccess[] = callback($this, "changePasswordFormSubmitted");

        return $form;
    }

    public function changePasswordFormSubmitted(Form $form) {
        $values = $form->getValues();

        if ($this->isTurnedOn("changePassword")) {
            $password = $this->authenticator->calculateHash($values->newPassword);
            $this->facade->changePassword($this->getUser()->getIdentity()->userName, $password);
            $this->addEvent("Uživatel ".$this->loggedUserName." si změnil své heslo.");
            $this->flashMessage("Vaše heslo bylo úspěšně změněno.", "success");
        } else {
            $this->flashMessage("Možnost změnit své heslo je momentálně vypnuta.");
        }

        $this->redirect("this");
    }

    public function createComponentChangeRealNameForm() {
        $form = new \Nette\Application\UI\Form();

        $form->addText("realName", "Jméno: ")->setRequired("Musíte vyplnit jméno.")->setDefaultValue($this->getUser()->getIdentity()->realName);
        $form->addSubmit("submit", "Změnit jméno");

        $form->onSuccess[] = callback($this, "changeRealNameFormSubmitted");

        return $form;
    }

    public function changeRealNameFormSubmitted(Form $form) {
        $values = $form->getValues();

        if ($this->isTurnedOn("changeRealName")) {
            $this->facade->changeRealName($this->getUser()->getId(), $values->realName);
            $this->addEvent("Uživatel ".$this->loggedUserName." si změnil své jméno.");
            $this->flashMessage("Vaše jméno bylo úspěšně změněno.", "success");
        }
        $this->getUser()->getIdentity()->realName = $values->realName;

        $this->redirect("this");
    }

    public function createComponentChangeEmailForm() {
        $form = new \Nette\Application\UI\Form();

        $authenticator = $this->authenticator;
        $userName = $this->getUser()->getIdentity()->userName;

        $form->addText("email", "E-mail: ")->setRequired("Musíte vyplnit nový e-mail.")->setDefaultValue($this->getUser()->getIdentity()->email);
        $form->addPassword("password", "Heslo: ")
                ->setRequired("Vyplňte prosím Vaše heslo.")
                ->addRule(function ($control) use ($authenticator, $userName) {
                            return $authenticator->canBeAuthenticated(array($userName, $control->value));
                        }, "Zadané heslo je neplatné.");
        $form->addSubmit("submit", "Změnit e-mail");

        $form->onSuccess[] = callback($this, "changeEmailFormSubmitted");

        return $form;
    }

    public function changeEmailFormSubmitted(Form $form) {
        $values = $form->getValues();

        if ($this->isTurnedOn("changeEmail")) {
            $this->facade->changeEmail($this->getUser()->getId(), $values->email);
            $this->addEvent("Uživatel ".$this->loggedUserName." si změnil svůj e-mail.");
            $this->flashMessage("Váš e-mail byl úspěšně změněn.", "success");
        }
        $this->getUser()->getIdentity()->email = $values->email;

        $this->redirect("this");
    }

}
