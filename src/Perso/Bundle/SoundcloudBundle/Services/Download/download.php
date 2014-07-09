<?php
namespace Perso\Bundle\SoundcloudBundle\Services\Download;

class download
{
    private $client_id;
    private $user_id;
    private $fileToZip;
    private $tempfolder;

    public function __construct($client_id)
    {
        $this->client_id   = $client_id;
        $this->fileToZip   = array();
        
        ini_set('max_execution_time', 300);
    }

    /**
     * Download the zip file
     * @param  (array) data
     * @return (obj)   zip file
     */
    public function getZipFile($data, $user_id, $temp)
    {
        $this->user_id    = $user_id;
        $this->tempfolder = 'temp'.$temp;

        $zip_path      = __DIR__.'/../../../../../../web/zip/'.$this->tempfolder.'/';
        $musiques_path = __DIR__.'/../../../../../../web/musiques/'.$this->tempfolder.'/';

        mkdir($zip_path, 0777);
        mkdir($musiques_path, 0777);

        foreach ($data as $key => $value) {
            $title = str_replace(" ", "_", $data[$key]['title'].".mp3");
            $title = str_replace("/", "_", $data[$key]['title'].".mp3");
            $sc_path = $data[$key]['link'];
            
            $this->save_localy($sc_path, $title, $musiques_path);
        }

        $zipfile = $this->create_zip($this->fileToZip, $zip_path.'mysoundcloud.zip');

        // Supprime les fichiers locaux
        foreach ($this->fileToZip as $file) {
            unlink($file);
        }

        //Ecrit l'url du zip en cache
        $this->setZipurlCache($zip_path);

        return $zip_path;
    }

    /**
     * Download song from url
     * @param url
     * @param save_path
     */
    function save_localy($sc_path, $title, $musiques_path)
    {
        set_include_path($musiques_path);
        $f = fopen( 'musiques/'.$title , 'w+', true);
        $handle = fopen($sc_path , "rb");
         
        while (!feof($handle)) {
            $contents = fread($handle, 8192);
            fwrite($f , $contents);
        }
        
        fclose($handle);

        $old = umask(0);
        chmod('musiques/'.$title, '0777');
        umask($old);
        
        array_push($this->fileToZip, 'musiques/'.$title);
        return;
    }

    /**
     * Crée un fichier zip à partir d'un dossier
     */
    public function create_zip($files = array(),$destination = '',$overwrite = false)
    {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }

        $valid_files = array();

        if(is_array($files)) {
            foreach($files as $file) {
                if(file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        
        if(count($valid_files)) {
            $zip = new \ZipArchive();
            if($zip->open($destination,$overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            foreach($valid_files as $file) {
                $zip->addFile($file,$file);
            }
            
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            
            $zip->close();
            
            return file_exists($destination);
        } else {
            return false;
        }
    }

    /**
     * Sauvegarde l'url du zip en cache
     */
    public function setZipurlCache($zip_path)
    {
        $fp = fopen( $zip_path.'cache.txt' , 'a+');
        fwrite($fp, $zip_path.'mysoundcloud.zip');
        fclose($fp);

        chmod($zip_path.'cache.txt', '0777');
        return;
    }

    public function getZipurlCache($temp)
    {
        $cache = __DIR__.'/../../../../../../web/zip/temp'.$temp.'/cache.txt';
        $file  = file_get_contents($cache);
        
        $old = umask(0);
        chmod($file, '0777');
        umask($old);

        header('Content-Disposition: attachment; filename=' . basename($file));
        readfile($file);

        unlink($file);
        unlink($cache);

        return;
    }

}