<?php

namespace Perso\Bundle\SoundcloudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('PersoSoundcloudBundle:Default:index.html.twig');
    }

    /**
     * Génère les likes
     * @param  (obj)   request
     * @return (array) zip
     */
    public function getLikesAction(Request $request)
    {
        //Check si le formulaire est reçu
        if($request->getMethod() == 'POST')
        {
            $user_url = $_POST["userurl"];
            $user_id = $this->get('api.soundcloud')->getUserId($user_url);
        } else {
            return $this->render('PersoSoundcloudBundle:Default:index.html.twig');
        }
        
        $data = $this->get('api.soundcloud')->show_likes($user_id);
        
        return $this->render('PersoSoundcloudBundle:Default:wall.html.twig', array('likes'=>$data[0], 'user_id'=>$data[1], 'urlzip'=>''));
    }

    /**
     * Génére le zip
     * @param  (obj) request
     * @return (obj) zip
     */
    public function getZipAction(Request $request, $temp)
    {
        //Check si le formulaire est reçu
        if($request->getMethod() == 'POST')
        {
            $zip_path = $this->get('main.download')->getZipFile($_POST['likes'], $_POST['user_id'], $temp);
        }

        return $this->render('PersoSoundcloudBundle:Default:index.html.twig');

    }

    public function getFileAction($temp)
    {
        $this->get('main.download')->getZipurlCache($temp);
        return;
    }
}
