<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class SkolkaPresenter extends BasePresenter
{

	public function renderProvoz() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(1);
	}

	public function renderProstredi() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(2);
	}

	public function renderDokumenty() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(3);
	}

	public function renderNovinky() {
		$facade = $this->getContext()->listFacade;
		$this->template->novinky = $facade->getListItems(1, true);
	}

	public function renderProbehlo() {
		
	}


}
