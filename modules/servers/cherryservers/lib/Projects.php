<?php

namespace CherryServers;

class Projects{

    const ENDPOINT = "https://api.cherryservers.com/v1/";
    private $headers;

    public function __construct(string $apiKey){
        $this->headers = array('Accept' => 'application/json',"Authorization" => "Bearer $apiKey");
    }

    public function __destroy(){
    }

    /*
    *
    * Get the list of available team projects
    *
    * @param int $teamId
    * @return array
    *
    */

    public function getProjects(int $teamId){
        $action = "teams/{$teamId}/projects";
        $url = self::ENDPOINT . $action;
        $request = \Requests::get($url, $this->headers);
        return json_decode( $request->body );

    }

    /*
    *
    * Add a project
    *
    * @param int $teamId
    * @param string $projectName
    * @return array
     */
    public function createProject(int $teamId, string $projectName){
        $action = "teams/{$teamId}/projects";
        $url = self::ENDPOINT . $action;
        $params = [
            'name' => $projectName
        ];
        $request = \Requests::post($url, $this->headers,$params);
        return json_decode( $request->body );

    }

}
