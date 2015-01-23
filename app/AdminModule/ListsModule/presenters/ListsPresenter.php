<?php

namespace AdminModule\ListsModule;

use \AdminModule\NetteExtension\Form;

class ListsPresenter extends \AdminModule\Security\SecuredPresenter {

    /** @var \Facade\ListFacade */
    private $facade;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("Lists") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }
        $this->facade = $this->getContext()->listFacade;
    }

    public function renderList($id) {
        $this->template->items = $this->facade->getListItems($id);
    }

    public function createComponentAddItemForm() {
        $form = new Form;

        $form->addText("name", "Název:")->setRequired("Název musíte vyplnit.");

        if ($this->isOptionAllowed("Items", "description")) {
            $form->addTextArea("description", "Popis: ");
            if($this->isOptionAllowed("Items","isDescriptionWYSIWYG"))
                $form["description"]->setAttribute("class","ckeditor");
        }

        if ($this->isOptionAllowed("Items", "other")) {
            $form->addText("other", $this->getSetting("Items", "otherName") . ": ");

            if ($this->isOptionAllowed("Items", "otherRequired"))
                $form["other"]->setRequired("Položku " . $this->getSetting("Items", "otherName") . " musíte vyplnit.");
        }

        if ($this->isOptionAllowed("Items", "image")) {
            for ($i = 1; $i <= (int) $this->getSetting("Items", "imageCount"); $i++) {
                $form->addUpload("img" . $i, "Obrázek " . $i . ": ")->addCondition(Form::FILLED)->addRule(Form::IMAGE, "Nahrávaný soubor má neplatný formát.");
            }
        }

        $form->addHidden("list", $this->getParam("id"));
        $form->addSubmit("submit", "Přidat " . $this->getSetting("Items", "itemName"));

        $form->onSuccess[] = callback($this, "addItemFormSubmitted");

        return $form;
    }

    public function addItemFormSubmitted(Form $form) {
        $val = $form->getValues();

        $list = $this->facade->getList($val["list"]);

        $item = new \Entity\ListItem();
        $item->setName($val["name"]);
        if ($this->isOptionAllowed("Items", "description"))
            $item->setDescription($val["description"]);

        if ($this->isOptionAllowed("Items", "other"))
            $item->setOther($val["other"]);

        $item->setList($list);

        if($this->isOptionAllowed("Items","other")) {
            $item->setDate(new \DateTime("now"));
        }

        $images = array();
        if($this->isOptionAllowed("Items", "image")) {
            for ($i = 1; $i <= (int) $this->getSetting("Items", "imageCount"); $i++) {
                $file = $val["img" . $i];
                if ($file instanceof \Nette\Http\FileUpload && $file->isOk() && $file->isImage()) {
                    $img = $file->toImage();
                    $fileName = uniqid() . $file->getName();

                    $images[$i] = new \Entity\ItemImage();

                    if ($this->getSetting("Items", "imageThumbnail") != 0) {
                        $dimensions = explode("x", $this->getSetting("Items", "imageThumbnail"));
                        $x = ($dimensions[0] == 0) ? NULL : $dimensions[0];
                        $y = ($dimensions[1] == 0) ? NULL : $dimensions[1];

                        $thumbnail = clone $img;
                        $thumbnail->resize($x, $y);
                        $thumbnail->save(WWW_DIR . "/upload/min_" . $fileName);
                        $images[$i]->setThumbnail("min_" . $fileName);
                    }

                    $img->save(WWW_DIR . "/upload/" . $fileName);

                    $images[$i]->setImage($fileName);
                    $images[$i]->setItem($item);
                }
            }
        }

        $this->facade->addItem($item, $images);

        $this->addEvent("Uživatel " . $this->loggedUserName . " přidal položku " . $val["name"] . " do seznamu <a href=\"" . $this->link("this", $list->getId()) . "\">" . $list->getName() . "</a>.");
        $this->flashMessage("Položka byla úspěšně přidána.", "success");
        $this->redirect("this");
    }

    public function handleDeleteItem($iId) {
        if ($this->isAllowed("Items", "deleteItems")) {
            $item = $this->facade->getItem($iId);
            $this->facade->deleteItem($iId);

            $this->addEvent("Uživatel " . $this->loggedUserName . " smazal položku " . $item->getName() . " ze seznamu <a href=\"" . $this->link("this", $item->getList()->getId()) . "\">" . $item->getList()->getName() . "</a>.");
            $this->flashMessage("Položka byla úspěšně smazána.", "success");
        } else
            $this->flashMessage ("Nemáte dostatečná oprávnění pro mazání položek.", "error");

        $this->redirect("this");
    }

    public function renderItem($id) {
        $this->template->item = $this->facade->getItem($id);
    }

    public function createComponentEditItemForm() {
        $form = new Form();

        $item = $this->facade->getItem($this->getParam("id"));

        $form->addText("name", "Název: ")->setRequired("Název položky musíte vyplnit.")->setDefaultValue($item->getName());
        if($this->isOptionAllowed("Items", "description")) {
                $form->addTextArea("description", "Popis: ")->setDefaultValue ($item->getDescription());
                if($this->isOptionAllowed("Items","isDescriptionWYSIWYG"))
                    $form["description"]->setAttribute("class","ckeditor");
            }

        if($this->isOptionAllowed("Items", "other")) {
            $form->addText("other", $this->getSetting("Items", "otherName"))->setDefaultValue ($item->getOther());
            
            if($this->isOptionAllowed("Items", "otherRequired"))
                    $form["other"]->setRequired($this->getSetting("Items", "otherName"). " je povinná položka.");
        }

         $form->addHidden("id", $this->getParam("id"));


        $form->addSubmit("submit", "Upravit");
        $form->onSuccess[] = callback($this, "editItemFormSubmitted");

        return $form;
    }

    public function editItemFormSubmitted(Form $form) {
        if($this->isAllowed("Items", "editItems")) {

            $val = $form->getValues();
            $this->facade->editItem($val["id"], $val["name"], isset($val["description"]) ? $val["description"] : NULL, isset($val["other"]) ? $val["other"] : NULL);
            $item = $this->facade->getItem($val["id"]);

            $this->flashMessage("Položka byla úspěšně upravena.", "success");
            $this->addEvent("Uživatel " . $this->loggedUserName . " upravil položku " . $item->getName() . " v seznamu <a href=\"" . $this->link("this", $item->getList()->getId()) . "\">" . $item->getList()->getName() . "</a>.");
        } else
            $this->flashMessage ("Nemáte dostatečná oprávnění pro editaci položek.");
        $this->redirect("this");
    }
}