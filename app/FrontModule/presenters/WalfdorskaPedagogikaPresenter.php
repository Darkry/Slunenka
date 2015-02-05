<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class WalfdorskaPedagogikaPresenter extends SecurityPresenter
{

	public function renderDefault($edit=false) {
		$this->edit($edit);
	}

	public function renderPrincipy($edit=false) {
		$this->edit($edit);
		
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(9);
	}

	public function renderClanky($edit=false) {
		$this->edit($edit);
		
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(10);
	}

	public function renderOdkazy($edit=false) {
		$this->edit($edit);
		
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(11);
	}

}
