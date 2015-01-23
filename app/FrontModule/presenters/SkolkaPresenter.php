<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class SkolkaPresenter extends BasePresenter
{

	//todo default page with links

	public function renderProvoz() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(1);
	}

}
