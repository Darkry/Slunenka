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

}
