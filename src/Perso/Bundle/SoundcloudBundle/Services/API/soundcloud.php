<?php
namespace Perso\Bundle\SoundcloudBundle\Services\API;

class soundcloud
{
    private $client_id;
    private $user_id;
    private $fileToZip = array();

    public function __construct($client_id, $user_id)
    {
        $this->client_id = $client_id;
        $this->user_id   = $user_id;

        ini_set('max_execution_time', 300);
    }

    /**
     * Get user id
     * @param  userul
     * @return userid
     */
    public function getUserId($user_url)
    {
        $jsonpath = 'http://api.soundcloud.com/resolve.json?url='.$user_url.'&client_id='.$this->client_id;
        $ch = curl_init($jsonpath);

        //set the url, number of POST vars, POST data
        $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => array('Content-type: application/json'));
        curl_setopt_array( $ch, $options );

        // Getting results
        $result =  curl_exec($ch);
        $user = json_decode($result, true);
        curl_close($ch);

        $user_id = explode('users/', $user['location'])[1];
        $user_id = explode('.', $user_id)[0];

        return $user_id;
    }

    /**
     * Affiches tous les likes de l'utitlisateur
     * @param  (string) user_id
     * @return (array)  likes
     */
    public function show_likes($user_id)
    {
        $this->user_id = $user_id;
        $likes = $this->getLikes();

        return array($likes, $this->user_id);
    }

    /**
     * Télécharge les likes soundcloud d'un utilisateur
     * @param user_id
     */
    public function downloadLikes($user_id=null)
    {
        $this->user_id = $user_id == null ? $this->user_id : $user_id;
        $likes = $this->getLikes();

        $zip_path  = __DIR__.'/../../../../../../web/zip/myzip.zip';

        foreach ($likes as $key => $value) {
            $local_path = str_replace(" ", "_", $likes[$key]['title'].".mp3");
            $local_path = str_replace("/", "_", $likes[$key]['title'].".mp3");
            $path = $likes[$key]['link'];
            
            $this->save_localy($path, $local_path);
        }

        $zipfile = $this->create_zip($this->fileToZip, $zip_path);

        // Dl le fichier
        header('Content-Disposition: attachment; filename=' . basename($zip_path));
        readfile($zip_path);

        // Supprime les fichiers locaux
        foreach ($this->fileToZip as $file) {
            unlink($file);
        }
        unlink($zip_path);

        exit;
    }

    /**
     * Récupère tous les likes d'un user
     * @return likes
     */
    public function getLikes()
    {
        $i = 0;
        $offset = 0;
        
        do {
            $jsonpath = 'http://api.soundcloud.com/users/'.$this->user_id.'/favorites.json?client_id='.$this->client_id.'&limit=100&offset='.$offset;

            //open connection
            $ch = curl_init($jsonpath);

            //set the url, number of POST vars, POST data
            $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => array('Content-type: application/json'));
            curl_setopt_array( $ch, $options );

            // Getting results
            $result =  curl_exec($ch);
            $tracks = json_decode($result, true);

            curl_close($ch);
            
            $i = count($tracks);
            $offset = $offset + 90;
            
            foreach ($tracks as $k => $track) {
                $id = $track['id'];
                $img = isset($track['artwork_url']) ? str_replace('large', 't500x500', $track['artwork_url']) : '../../web/img/substitution.png';

                $likes[$id]['title']   = $track['title'];
                $likes[$id]['artist']  = $track['user']['username'];
                $likes[$id]['cover']   = $img;
                $likes[$id]['link']    = $track['stream_url'].'?client_id='.$this->client_id;
                $likes[$id]['format']  = $track['original_format'];
                $likes[$id]['views']   = isset($track['playback_count']) ? $track['playback_count'] : null;
            }
        } while ($i >= 90);

        return $likes;
    }

    /**
     * Download song from url
     * @param url
     * @param save_path
     */
    function save_localy($url, $save_path)
    {
        set_include_path('__DIR__."/../../../../../../web/musiques/');
        // Enregistre le fichier en local
        // ! Supprimer les fichiers en local lorsque dl terminer
        $f = fopen( $save_path , 'w+', true);
        $handle = fopen($url , "rb");
         
        while (!feof($handle)) {
            $contents = fread($handle, 8192);
            fwrite($f , $contents);
        }

        chmod($save_path, '0755');
        fclose($handle);
        array_push($this->fileToZip, $save_path);

        return;
    }

    /**
     * Crée un fichier zip à partir d'un dossier
     */
    public function create_zip($files = array(),$destination = '',$overwrite = false)
    {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($files)) {
            //cycle through each file
            foreach($files as $file) {
                //make sure the file exists
                if(file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new \ZipArchive();
            if($zip->open($destination,$overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach($valid_files as $file) {
                $zip->addFile($file,$file);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            
            //close the zip -- done!
            $zip->close();
            
            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }
}