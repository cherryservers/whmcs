<?php

namespace CherryServers;

class Teams{

    const ENDPOINT = "https://api.cherryservers.com/v1/";
    private $headers;

    public function __construct(string $apiKey){
        $this->headers = array('Accept' => 'application/json',"Authorization" => "Bearer $apiKey");
    }

    public function __destroy(){
    }

    /*
    *
    * Get the list of available team ids
    *
    * @return array
    *
    */

    public function getTeams(){
        $action = "teams";
        $url = self::ENDPOINT . $action;
        $request = \Requests::get($url, $this->headers);
        return json_decode( $request->body );
    }

    public function getTeamProjects(int $teamId){
        $action = "teams/{$teamId}/projects";
        $url = self::ENDPOINT . $action;
        $request = \Requests::get($url, $this->headers);
        return json_decode( $request->body );
    }
}
