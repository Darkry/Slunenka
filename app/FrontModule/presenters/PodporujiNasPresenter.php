<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class PodporujiNasPresenter extends SecurityPresenter
{

	public function renderDefault($edit=false) {
		$this->edit($edit);
		
		$facade = $this->getContext()->textFacade;
		$this->template->text = $facade->getTextById(12);
	}

}
