<?php
namespace AdminModule;

namespace AdminModule\LoginModule;

use \AdminModule\NetteExtension\Form;
use Nette\Security as NS;
/**
 * This presenter allows user to log in.
 *
 * @author Kryštof Selucký
 */
class LoginPresenter extends \AdminModule\BasePresenter {

    /** @var \Facade\UserFacade */
    private $userFacade;

    protected function startup() {
        parent::startup();
        if($this->getUser()->isLoggedIn()) {
            $this->redirect(":Admin:Index:Index:");
        }
        $this->userFacade = $this->getContext()->userFacade;
    }

/**
 * Creating form that allows user to log in
 * @return Form
 */
    protected function createComponentSignInForm()
    {
        $form = new Form();
        $form->addText('username', 'Uživatelské jméno:', 30, 20);
        $form->addPassword('password', 'Heslo:', 30);
        $form->addCheckbox('persistent', 'Pamatovat si mě na tomto počítači');
        $form->addSubmit('login', 'Přihlásit se');
        $form->onSuccess[] = callback($this, 'signInFormSubmitted');
        return $form;
    }

    /**
     * Proccess sended form and log in user
     * @param Form $form sended form
     * @return void
     */
    public function signInFormSubmitted(Form $form)
    {
        try {
            $user = $this->getUser();
            $values = $form->getValues();
            if ($values->persistent) {
                $user->setExpiration('+30 days', FALSE);
            }
            $user->login($values->username, $values->password);
            $this->flashMessage('Přihlášení bylo úspěšné.', 'success');
            $this->redirect(':Admin:Index:Index:');
        } catch (NS\AuthenticationException $e) {
            $form->addError('');
            $this->flashMessage("Neplatné uživatelské jméno nebo heslo.", "error");
            $this->redirect("this");
        }
    }

    public function createComponentForgottenPasswordForm() {
        $form = new Form();

        $form->addText("email", "Váš e-mail: ")
                ->setRequired("Vyplňte prosím Váš e-mail.")
                ->addRule(Form::EMAIL, "Vyplněný e-mail má neplatný formát.");
        $form->addSubmit("submit", "Odeslat");

        $form->onSuccess[] = callback($this, "forgottenPasswordFormSubmitted");

        return $form;
    }

    public function forgottenPasswordFormSubmitted(Form $form) {
        $values = $form->getValues();

        if(($user = $this->userFacade->getUserByEmail($values->email)) != NULL)
        {
            $password = \AdminModule\Tools\PasswordGenerator::generatePassword();
            $hash = \AdminModule\Tools\PasswordGenerator::generatePassword(30, 30);
            $link = $this->link("changePassword", array("hash" => $hash));

            $url = new \Nette\Http\Url($this->getService("httpRequest")->getUrl());
            $url = $url->hostUrl;

            $link = $url . $link;

            $cache = $this->getContext()->cache;
            $cache->save($hash, array("email" => $values->email, "password" => $password), array(
                \Nette\Caching\Cache::EXPIRATION => "+ 10 hours"
            ));

            $mail = new \Nette\Mail\Message();
            $mail->setFrom("robot@dark-project.cz");
            $mail->setSubject("Nové heslo");
            $mail->setBody("Dobrý den,\n na stránce $url byl zaslán požadavek na zaslání nového hesla pro uživatele ".$user->getRealName().".\n
                    Vaše přihlašovací jméno: ".$user->getUserName()."\n
                    Vaše nové heslo je: $password\n
                    Pokud ho chcete uvést v platnost navštivte prosím tento odkaz:\n
                    $link\n\n S pozdravem,\n DarkAdmin Robot");
            $mail->addTo($values->email);
            $mail->send();

            $this->flashMessage("Na zadanou adresu byl odeslán e-mail s novým heslem a dalším postupem.", "success");
            $this->redirect("default");
        } else {
            $this->flashMessage("Zadaný e-mail není přiřazen žádnému existujícímu uživateli.", "error");
        }
    }

    public function actionChangePassword($hash) {
         $cache = $this->getContext()->cache;
         $info = $cache->load($hash);
         if($info == NULL) {
             $this->flashMessage("Váš odkaz je neplatný a nebo už vypršela jeho platnost.", "error");
             $this->redirect("default");
         } else {
             $password = $this->getContext()->authenticator->calculateHash($info["password"]);
             $this->userFacade->changePasswordByEmail($info["email"], $password);
             $cache->remove($hash);
             $this->flashMessage("Vaše heslo bylo úspěšně změněno.","success");
             $this->redirect("default");
         }
    }

}