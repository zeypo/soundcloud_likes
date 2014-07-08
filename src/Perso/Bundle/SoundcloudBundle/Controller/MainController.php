<?php

namespace Perso\Bundle\SoundcloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('PersoSoundcloudBundle:Default:index.html.twig');
    }

    /**
     * Génère un zip
     */
    public function getZipAction()
    {
        $this->get('api.soundcloud')->downloadLikes();
        return $this->render('PersoSoundcloudBundle:Default:index.html.twig');   
    }
}
