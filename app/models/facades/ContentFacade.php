<?php

namespace Facade;

class ContentFacade {

    /** @var \Doctrine\ORM\EntityRepository */
    private $pageRep;

    /** @var \Doctrine\ORM\EntityRepository */
    private $frameRep;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->pageRep = $em->getRepository("\Entity\WebPages");
        $this->frameRep = $em->getRepository("\Entity\ContentFrames");
        $this->em = $em;
    }

    public function getPages() {
        return $this->pageRep->findAll();
    }

    public function getPage($id) {
        return $this->pageRep->find($id);
    }

    public function doesExistFrameWithId($contentId) {
        $exist = $this->frameRep->findOneBy(array("contentId" => $contentId));
        if($exist)
            return true;
        else
            return false;
    }

    public function getFrameByFrameId($id) {
        return $this->frameRep->findOneBy(array("contentId" => $id));
    }

    public function getPageByLink($link) {
        return $this->pageRep->findOneBy(array("link" => $link));
    }

    public function addFrame($contentId, $text, $pageId) {
        $frame = new \Entity\ContentFrames();
        $frame->setContentId($contentId);
        $frame->setText($text);
        $frame->setPage($this->getPage($pageId));

        $this->em->persist($frame);
        $this->em->flush();
    }

    public function updateFrame($id, $text) {
        $frame = $this->getFrameByFrameId($id);
        $frame->text = $text;

        $this->em->flush();
    }

    public function getEditablesForPage($pageId) {
        $frames = $this->pageRep->find($pageId)->getFrames();
        $sorted = array();
        foreach($frames as $frame) {
            $sorted[$frame->getContentId()] = $frame->getText();
        }
        return $sorted;
    }

}
