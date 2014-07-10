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
                $img = isset($track['artwork_url']) ? str_replace('large', 't500x500', $track['artwork_url']) : '../img/logo_sound_500x500.png';

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
}