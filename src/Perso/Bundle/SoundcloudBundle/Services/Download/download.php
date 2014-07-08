<?php
namespace Perso\Bundle\SoundcloudBundle\Services\Download;

class download
{
    private $client_id;
    private $user_id;
    private $fileToZip = array();

    public function __construct($client_id)
    {
        $this->client_id = $client_id;
        ini_set('max_execution_time', 300);
    }

    /**
     * Download the zip file
     * @param  (array) data
     * @return (obj)   zip file
     */
    public function getZipFile($data, $user_id)
    {
        $this->user_id = $user_id;

        $zip_path  = __DIR__.'/../../../../../../web/zip/myzip.zip';

        foreach ($data as $key => $value) {
            $local_path = str_replace(" ", "_", $data[$key]['title'].".mp3");
            $local_path = str_replace("/", "_", $data[$key]['title'].".mp3");
            $path = $data[$key]['link'];
            
            $this->save_localy($path, $local_path);
        }

        $zipfile = $this->create_zip($this->fileToZip, $zip_path);

        // Dl le fichier
        header('Content-Disposition: attachment; filename=' . basename($zip_path));
        //readfile($zip_path);

        // Supprime les fichiers locaux
        foreach ($this->fileToZip as $file) {
            unlink($file);
        }

        return $zip_path;
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

        chmod($save_path, '777');
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