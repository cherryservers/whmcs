<?php

namespace CherryServers;

class Servers{

    const ENDPOINT = "https://api.cherryservers.com/v1/";
    private $headers;

    public function __construct(string $apiKey){
         $this->headers = array('Accept' => 'application/json',"Authorization" => "Bearer $apiKey");
    }

    public function __destroy(){
    }

    /*
    *
    * Retrieve a server
    * @param int $serverId
    * @return array
    *
    */

    public function getServer(int $serverId,bool $details=false){
        $action = "servers/{$serverId}";
        if($details){
            $action .= '?fields=all,status,power,bmc';
        }
        $url = self::ENDPOINT . $action;
        $request = \Requests::get($url, $this->headers);
        return json_decode( $request->body );
    }

    /*
    *
    * Update server settings
    * @param int $serverId
    * @param array $params
    * @return array
    *
    */


    public function updateServer(int $serverId, array $params){
        $action = "/servers/{$serverId}";
        $request = \Requests::put($url, $this->headers,$params);
        return json_decode( $request->body );
    }

    /*
    *
    * Delete a server
    * @param int $serverId
    * @return array
    *
    */

    public function deleteServer(int $serverId){
        $action = "servers/{$serverId}";
        $url = self::ENDPOINT . $action;
        $params = [
        ];
        $request = \Requests::delete($url, $this->headers,$params);
        return json_decode( $request->body );

    }

    /*
     *
    * Retrieve a project server list
    * @param int $projectId
    * @return array
    *
    */

    public function getServers(int $projectId){
        $action = "/projects/{$projectId}/servers";
        $request = \Requests::get($url, $this->headers,$params);
        return json_decode( $request->body );
    }

    /*
    *
    * Request a server
    * @param int $projectId
    * @param array $params
    * @return array
    *
    */

    public function deployServer(int $projectId, array $params){
        $action = "projects/{$projectId}/servers";
        $url = self::ENDPOINT . $action;
        $request = \Requests::post($url, $this->headers,$params);
        return json_decode( $request->body );

    }

    /*
    *
    * Perform an action
    * @param int $serverId
    * @param array $params
    * @return array
    *
    */

    public function performAction(int $serverId, array $params){
        $action = "servers/{$serverId}/actions";
        $url = self::ENDPOINT . $action;
        $request = \Requests::post($url, $this->headers,$params);
        return json_decode( $request->body );
    }


    public function powerOff(int $serverId){
        $params = [
            'type' => 'power_off'
        ];
        return $this->performAction($serverId,$params);
    }

    public function powerOn(int $serverId){
        $params = [
            'type' => 'power_on'
        ];
        return $this->performAction($serverId,$params);
    }

    public function reboot(int $serverId){
        $params = [
            'type' => 'reboot'
        ];
        return $this->performAction($serverId,$params);
    }

    public function rescue(int $serverId,string $password){
        $params = [
            'type' => 'rescue',
            'password' => $password
        ];
        return $this->performAction($serverId,$params);
    }

    public function rebuild(int $serverId,string $hostname,string $password, string $image, array $sshKeys = []){
        $params = [
            'type' => 'rebuild',
            'image' => $image,
            'hostname' => $hostname,
            'password' => $password
        ];
        if( !empty( $sshKeys ) ){
            $params['ssh_keys'] = $sshKeys;
        }
        return $this->performAction($serverId,$params);
    }


    public function getConsole(int $serverId){
        $params = [
            'type' => 'reset-kvm-password'
        ];
        return $this->performAction($serverId,$params);
    }

    public function exitRescue(int $serverId){
        $params = [
            'type' => 'rescue',
        ];
        return $this->performAction($serverId,$params);
    }


}
