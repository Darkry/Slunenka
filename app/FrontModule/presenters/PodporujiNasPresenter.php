<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class PodporujiNasPresenter extends BasePresenter
{

	public function renderDefault() {
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(12);
	}

}
