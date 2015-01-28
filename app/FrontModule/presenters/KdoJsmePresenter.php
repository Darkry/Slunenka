<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class KdoJsmePresenter extends BasePresenter
{

	public function renderKontakty() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(13);
	}

	public function renderLide() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(14);
	}

	public function renderONas() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(17);
	}

	public function renderSpolek() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(15);
	}

	public function renderMisto() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(16);
	}

}
