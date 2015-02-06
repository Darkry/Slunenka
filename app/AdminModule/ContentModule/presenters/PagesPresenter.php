<?php
namespace AdminModule\ContentModule;
/**
 * Index page when user log in
 *
 * @package DarkAdmin
 * @author Kryštof Selucký
 */
class PagesPresenter extends \AdminModule\Security\SecuredPresenter {

	private $db;

	public function startup() {
		parent::startup();

		$this->db = $this->getContext()->contentFacade;
	}

	public function renderDefault() {
		$this->template->pages = $this->db->getPages();
	}

}