<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class HomepagePresenter extends SecurityPresenter
{
	public function renderDefault($edit=false)
	{
		$this->edit($edit);
		
		$this->template->items = $this->getContext()->listFacade->getAllItems();
	}
}
