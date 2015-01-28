<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class WalfdorskaPedagogikaPresenter extends BasePresenter
{

	public function renderPrincipy() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(9);
	}

	public function renderClanky() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(10);
	}

	public function renderOdkazy() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(11);
	}

}
