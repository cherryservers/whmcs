<?php

namespace CherryServers;

class Plans{

    const ENDPOINT = "https://api.cherryservers.com/v1/";
    private $headers;

    public function __construct(string $apiKey){
        $this->headers = array('Accept' => 'application/json',"Authorization" => "Bearer $apiKey");
    }

    public function __destroy(){
    }

    /*
    *
    * Get the list of available plans
    *
    * @return array
    *
    */

    public function getPlans(int $teamId){
        $action = "teams/{$teamId}/plans";
        $url = self::ENDPOINT . $action;
        $request = \Requests::get($url, $this->headers);
        return json_decode( $request->body );
    }

}
