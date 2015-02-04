<?php
namespace FrontModule;

use Nette\Application\UI\Form;

class HomepagePresenter extends BasePresenter
{
	public function renderDefault()
	{
		$this->template->items = $this->getContext()->listFacade->getAllItems();
	}
}
