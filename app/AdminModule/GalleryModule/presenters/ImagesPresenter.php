<?php

namespace AdminModule\GalleryModule;

use \AdminModule\NetteExtension\Form;

class ImagesPresenter extends \AdminModule\Security\SecuredPresenter {

    /** @var \Facade\GalleryFacade */
    private $facade;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("Images") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        if ($this->isAllowed("Images", "addImages") == false) {
            $this->flashMessage("Nemáte dostatečná oprávnění pro přidávání obrázků.");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->galleryFacade;
    }

    public function renderDefault() {
        $cache = $this->getContext()->cache;
        $cacheName = "newImg" . $this->getUser()->getId();

        if ($cache->load($cacheName)) {
            $this->template->waitingImg = array_reverse($cache->load($cacheName));
        }
        $this->template->form = $this["fillImageInfoForm"];
    }

    public function createComponentPlupload() {
        // Main object
        $uploader = new \Plupload\Rooftop();

        // Use magic for loading Js and Css?
        // $uploader->disableMagic();
        // Configuring paths
        $uploader->setWwwDir(WWW_DIR) // Full path to your frontend directory
                ->setBasePath($this->template->basePath) // BasePath provided by Nette
                ->setTempLibsDir(WWW_DIR . '/DA'); // Full path to the location of plupload libs (js, css)
        // Configuring plupload
        $uploader->createSettings()
                ->setRuntimes(array('html5', 'flash')) // Available: gears, flash, silverlight, browserplus, html5
                ->setMaxFileSize('10mb')
                ->setMaxChunkSize('1mb'); // What is chunk you can find here: http://www.plupload.com/documentation.php
        // Configuring uploader
        $uploader->createUploader()
                ->setTempUploadsDir(WWW_DIR . '/upload') // Where should be placed temporaly files
                ->setToken("2") // Resolves file names collisions in temp directory
                ->setOnSuccess(array($this, 'uploadSuccesfull')); // Callback when upload is successful: returns Nette\Http\FileUpload

        return $uploader->getComponent();
    }

    public function uploadSuccesfull(\Nette\Http\FileUpload $file) {
        if ($file->isImage() && $file->isOk()) {
            $img = $file->toImage();
            $fileName = uniqid() . $file->getName();

            if ($this->isOptionAllowed("Images", "thumbnails")) {
                $thumbnail = clone $img;
                $x = ($this->getSetting("Images", "thumbnail-x") == 0) ? NULL : $this->getSetting("Images", "thumbnail-x");
                $y = ($this->getSetting("Images", "thumbnail-y") == 0) ? NULL : $this->getSetting("Images", "thumbnail-y");
                $thumbnail->resize($x, $y);
                $thumbnail->save(WWW_DIR . "/upload/min_" . $fileName);
            }

            if($this->isOptionAllowed("Images", "resizeFullImage")) {
                if ($this->getSetting("Images", "image-x") !== NULL && $this->getSetting("Images", "image-x") !== NULL) {
                    $x = ($this->getSetting("Images", "image-x") === 0) ? NULL : $this->getSetting("Images", "image-x");
                    $y = ($this->getSetting("Images", "image-y") === 0) ? NULL : $this->getSetting("Images", "image-y");
                    $img->resize($x, $y);
                }
            }
            $img->save(WWW_DIR . "/upload/" . $fileName);

            $cache = $this->getContext()->cache;
            $cacheName = "newImg" . $this->getUser()->getId();

            if ($cache->load($cacheName)) {
                $images = $cache->load($cacheName);
                $images[$fileName] = $fileName;
                $cache->save($cacheName, $images);
            } else {
                $cache->save($cacheName, array($fileName => $fileName));
            }
        }
    }

    public function createComponentFillImageInfoForm() {
        $form = new Form();
        $galleries = $this->facade->getGalleriesInPairsIdName();
        $form->addSelect("gallery", "Galerie: ", $galleries)->setPrompt("Vyberte galerii")->setRequired("Vyberte galerii, do které chcete obrázek zařadit.");
        if ($this->isOptionAllowed("Images", "imageName"))
            $form->addText("imageName", "Název: ")->setRequired("Musíte vyplnit název obrázku.");
        if ($this->isOptionAllowed("Images", "imageDescription"))
            $form->addTextArea("imageDescription", "Popis: ");
        $form->addHidden("imagePath", "");

        $form->addSubmit("submit", "Uložit obrázek");
        $form->onSuccess[] = callback($this, "fillImageInfoFormSubmitted");

        return $form;
    }

    public function fillImageInfoFormSubmitted(Form $form) {
        $val = $form->getValues();
        $info = array();
        if ($this->isOptionAllowed("Images", "imageName"))
            $info["name"] = $val->imageName;
        if ($this->isOptionAllowed("Images", "imageDescription"))
            $info["text"] = $val->imageDescription;
        $info["gallery"] = $this->facade->getGallery($val->gallery);
        $info["image"] = $val->imagePath;
        if ($this->isOptionAllowed("Images", "thumbnails"))
            $info["thumbnail"] = "min_" . $val->imagePath;
        $this->facade->addImage($info);

        $cache = $this->getContext()->cache;
        $cacheName = "newImg" . $this->getUser()->getId();

        $data = $cache->load($cacheName);
        if (count($data) == 1)
            $cache->remove($cacheName);
        else {
            unset($data[$val->imagePath]);
            $cache->save($cacheName, $data);
        }

        $this->flashMessage("Obrázek byl úspěšně přidán do galerie " . $info["gallery"]->getName() . ".", "success");
        $this->addEvent("Uživatel " . $this->loggedUserName . " přidal obrázek do galerie " . $info["gallery"]->getName() . ".");
        $this->redirect("this");
    }

    public function handleDeleteImage($imgName) {
        if ($this->isAllowed("Images", "deleteImages")) {
            unlink(WWW_DIR . "/upload/" . $imgName);
            if ($this->isOptionAllowed("Images", "thumbnails"))
                unlink(WWW_DIR . "/upload/min_" . $imgName);

            $cache = $this->getContext()->cache;
            $cacheName = "newImg" . $this->getUser()->getId();
            $data = $cache->load($cacheName);
            if (count($data) == 1)
                $cache->remove($cacheName);
            else {
                unset($data[$imgName]);
                $cache->save($cacheName, $data);
            }
        } else {
            $this->flashMessage("Nemáte dostatečná oprávnění pro mazání obrázků.","error");
        }
        if($this->isAjax()) {
            $this->invalidateControl("waitingImages");
            $this->invalidateControl("flashMessages");
        } else
            $this->redirect ("this");
    }

}
