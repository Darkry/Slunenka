<?php
namespace AdminModule;

use \AdminModule\NetteExtension\Form;
/**
 * Page with the log in form
 * 
 * @package DarkAdmin
 * @author Kryštof Selucký
 */
class HomepagePresenter extends BasePresenter {

    /**
     * Creates form for log in
     * @return Form created form
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
     * Process submitted form
     * @param Form $form submitted form
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
            $this->redirect('Admin:default');
        } catch (NS\AuthenticationException $e) {
            $form->addError('Neplatné uživatelské jméno nebo heslo.');
        }
    }
}