<?php

namespace AdminModule\GalleryModule;

use \AdminModule\NetteExtension\Form;

class GalleriesPresenter extends \AdminModule\Security\SecuredPresenter {

    /** @var \Facade\GalleryFacade */
    public $facade;
    private $editImageFormData = NULL;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("Galleries") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }
        $this->facade = $this->getContext()->galleryFacade;
    }

    public function renderDefault() {
        $this->template->galleries = $this->facade->getGalleries();
    }

    public function createComponentAddGalleryForm() {
        $form = new Form();
        $_this = $this;
        $form->addText("name", "Název: ")->addRule(
                                            function ($control) use ($_this) {
                                                return !$_this->facade->doesGalleryNameExist($control->getValue());
                                            },
                                            "Galerie se zadaným názvem již existuje."
                                        )->setRequired("Musíte vyplnit název galerie.");
        if ($this->isOptionAllowed("Galleries", "galleryDescription"))
            $form->addTextArea("description", "Popis: ");
        $form->addSubmit("submit");
        
        $form->onSuccess[] = callback($this, "addGalleryFormSubmitted");
        return $form;
    }

    public function addGalleryFormSubmitted(Form $form) {
        if ($this->isAllowed("Galleries", "addGalleries")) {
            $val = $form->getValues();
            $gal = $this->facade->addGallery($val->name, (isset($val->description) ? $val->description : NULL));
            $this->flashMessage("Galerie byla úspěšně přidána.", "success");
            $this->addEvent("Uživatel " . $this->loggedUserName . " přidal galerii s názvem <a href=\"" . $this->link("Galleries:galleryDetail", array($gal->getId())) . "\">" . $gal->getName() . "</a>.");
        } else {
            $this->flashMessage("Nemáte dostatečná oprávnění pro přidávání galerií.", "error");
        }
        $this->redirect("this");
    }

    public function handleDeleteGallery($id) {
        if ($this->isAllowed("Galleries", "deleteGalleries") && $this->isAllowed("Images", "deleteImages")) {
            $galName = $this->facade->getGallery($id)->getName();
            $images = $this->facade->getGalleryImages($id);
            foreach ($images as $image) {
                unlink(WWW_DIR . "/upload/" . $image->getImage());
                if ($this->isOptionAllowed("Images", "thumbnails"))
                    unlink(WWW_DIR . "/upload/" . $image->getThumbnail());
            }
            $this->facade->deleteGalleryWithImages($id);
            $this->flashMessage("Galerie byla úspěšně smazána i se svými obrázky.", "success");
            $this->addEvent("Uživatel " . $this->loggedUserName . " smazal galerii " . $galName . " i se všemi jejími obrázky.");
        } else
            $this->flashMessage("Na smazání galerie nemáte dostatečná oprávnění.", "error");

        if ($this->isAjax()) {
            $this->invalidateControl("flashMessages");
            $this->invalidateControl("galleriesList");
        } else
            $this->redirect("this");
    }

    public function renderGalleryDetail($id) {
        $this->template->gallery = $this->facade->getGallery($id);
        $this->template->images = $this->facade->getGalleryImages($id);
        $this->template->form = $this["editImageForm"];
    }

    public function createComponentEditGalleryForm() {
        $form = new Form();
        $gal = $this->facade->getGallery($this->getParam("id"));

        $form->addText("name", "Název: ")->setRequired("Název galerie musí být vyplněn.")->setDefaultValue($gal->getName());
        if ($this->isOptionAllowed("Galleries", "galleryDescription"))
            $form->addTextArea("description", "Popis: ")->setDefaultValue($gal->getText());
        $form->addHidden("id", $this->getParam("id"));
        $form->addSubmit("submit");

        $form->getElementPrototype()->class = "ajax";

        $form->onSuccess[] = callback($this, "editGalleryFormSubmitted");

        return $form;
    }

    public function editGalleryFormSubmitted(Form $form) {
        $val = $form->getValues();
        $this->facade->editGallery($val["id"], $val["name"], (isset($val["description"]) ? $val["description"] : NULL));
        $this->addEvent("Uživatel " . $this->loggedUserName . " změnil informace o galerii <a href=\"" . $this->link("galleryDetail", array($val["id"])) . "\">" . $val["name"] . "</a>.");
    }

    public function handleDeleteImage($imageId) {
        if ($this->isAllowed("Images", "deleteImages")) {
            $img = $this->facade->getImage($imageId);
            unlink(WWW_DIR . "/upload/" . $img->getImage());
            if ($this->isOptionAllowed("Images", "thumbnails"))
                unlink(WWW_DIR . "/upload/" . $img->getThumbnail());

            $this->facade->deleteImageFromGallery($imageId);

            if ($this->isAjax())
                $this->invalidateControl("imageList");
            else
                $this->redirect("this");
        }
    }

    public function createComponentEditImageForm() {
        $form = new Form();
        if ($this->isOptionAllowed("Images", "imageName")) {
            $form->addText("name", "Jméno: ")->setRequired("Musíte vyplnit jméno obrázku.");
        }
        if ($this->isOptionAllowed("Images", "imageDescription")) {
            $form->addTextArea("description", "Popis: ");
        }

        $form->addHidden("id");

        if ($this->editImageFormData != NULL) {
            $defaults = array();

            if ($this->isOptionAllowed("Images", "imageName")) $defaults["name"] = $this->editImageFormData->getName();
            if ($this->isOptionAllowed("Images", "imageDescription")) $defaults["description"] = $this->editImageFormData->getText();
            $defaults["id"] = $this->editImageFormData->getId();

            $form->setDefaults($defaults);
        }

        $form->getElementPrototype()->class = "ajax";

        $form->addSubmit("submit", "Uložit změny");
        $form->onSuccess[] = callback($this, "editImageFormSubmitted");

        return $form;
    }

    public function editImageFormSubmitted(Form $form) {
        $val = $form->getValues();

        $this->facade->editImage($val["id"], (isset($val["name"]) ? $val["name"] : NULL), (isset($val["description"]) ? $val["description"] : NULL));
        $this->flashMessage("Obrázek byl úspěšně upraven.", "success");

        if($this->isAjax()) {
            $this->invalidateControl("imageList");
            $this->invalidateControl("flashMessages");
        } else {
            $this->redirect("this");
        }
    }

    public function handleSetDefaultsEditImageForm($imgId) {
        $this->editImageFormData = $this->facade->getImage($imgId);
        if($this->isAjax()) {
            $this->invalidateControl("editImageDialog");
        }
    }

}