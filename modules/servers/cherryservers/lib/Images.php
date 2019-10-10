<?php

namespace CherryServers;

class Images{

    const ENDPOINT = "https://api.cherryservers.com/v1/";
    private $headers;

    public function __construct(string $apiKey){
        $this->headers = array('Accept' => 'application/json',"Authorization" => "Bearer $apiKey");
    }

    public function __destroy(){
    }

    /*
    *
    * Get the list of available plan images
    *
    * @return array
    *
    */

    public function getImages(int $planId){
        $action = "plans/{$planId}/images";
        $url = self::ENDPOINT . $action;
        $request = \Requests::get($url, $this->headers);
        return json_decode( $request->body );
    }

}
