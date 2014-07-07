<?php
namespace Perso\Bundle\SoundcloudBundle\Services\API;

class soundcloud
{
    private $client_id;
    private $user_id;

    public function __construct($client_id, $user_id)
    {
        $this->client_id = $client_id;
        $this->user_id   = $user_id;
    }

    /**
     * Télécharge les likes soundcloud d'un utilisateur
     * @param user_id
     */
    public function downloadLikes($user_id=null)
    {
        $this->user_id = $user_id == null ? $this->user_id : $user_id;
        $likes = $this->getLikes();

        $save_path = __DIR__.'/../../../../../../web/musiques/'.$likes[1]['title'].".mp3";
        $path = $likes[1]['dl_link'];
        
        //$this->save_localy($likes[1]['dl_link'], $save_path);
        $this->create_zip();

        print_r('Dl bien effectué');
        exit;
    }

    /**
     * Récupère tous les likes d'un user
     * @return likes
     */
    public function getLikes()
    {
        $jsonpath = 'http://api.soundcloud.com/users/'.$this->user_id.'/favorites.json?client_id='.$this->client_id;

        //open connection
        $ch = curl_init($jsonpath);

        //set the url, number of POST vars, POST data
        $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => array('Content-type: application/json'));
        curl_setopt_array( $ch, $options );

        // Getting results
        $result =  curl_exec($ch);
        $tracks = json_decode($result, true);

        curl_close($ch);
        
        foreach ($tracks as $k => $track) {
            $likes[$k]['title'] = $track['title'];
            $likes[$k]['dl_link'] = $track['stream_url'].'?client_id='.$this->client_id;
        }

        return $likes;
    }

    /**
     * Download song from url
     * @param url
     * @param save_path
     */
    function save_localy($url, $save_path)
    {
        // Enregistre le fichier en local
        // ! Supprimer les fichiers en local lorsque dl supprimer
        $f = fopen( $save_path , 'w+');
        $handle = fopen($url , "rb");
         
        while (!feof($handle)) {
            $contents = fread($handle, 8192);
            fwrite($f , $contents);
        }

        chmod($save_path, '777');
        fclose($handle);
        print_r("1 fichier telecharger \n");

        // Dl le fichier
        // header('Content-Disposition: attachment; filename=' . basename($save_path));
        // readfile($save_path);
        return;
    }

    /**
     * Crée un fichier zip à partir d'un dossier
     */
    public function create_zip()
    {
        $zip = new \ZipArchive;
        $zip->open(__DIR__.'/../../../../../../web/zip/myzip.zip', \ZipArchive::CREATE);
        foreach (glob(__DIR__.'/../../../../../../web/musiques/*') as $file) {
            $zip->addFile($file);
            if ($file != __DIR__.'/../../../../../../web/musiques/important.txt') unlink($file);
        }
        
        $zip->close();

        return;
    }
}