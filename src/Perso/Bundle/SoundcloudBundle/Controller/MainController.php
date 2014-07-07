<?php

namespace Perso\Bundle\SoundcloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        $test = $this->get('api.soundcloud')->downloadLikes();
        return $this->render('PersoSoundcloudBundle:Default:index.html.twig');
    }
}
