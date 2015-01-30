<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class AktivityPresenter extends BasePresenter
{

	public function renderNejmladsi() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(4);
	}

	public function renderPredskolni() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(5);
	}

	public function renderSkolaci() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(6);
	}

	public function renderStarsi() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(7);
	}

	public function renderTerapie() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(8);
	}

	public function renderNovinky() {
		$facade = $this->getContext()->listFacade;
		$this->template->novinky = $facade->getListItems(2, true);
	}

	public function renderProbehlo() {

	}


}