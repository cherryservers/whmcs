<?php
/**
 * WHMCS cherryservers.com provisioning module
 *
 * Provisioning Modules, also referred to as Product or Server Modules, allow
 * you to create modules that allow for the provisioning and management of
 * products and services in WHMCS.
 *
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once 'vendor/autoload.php';
require_once 'lib/Servers.php';
require_once 'lib/Teams.php';
require_once 'lib/Plans.php';
require_once 'lib/Images.php';
require_once 'lib/Projects.php';

use WHMCS\Database\Capsule;


/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
 */
function cherryservers_MetaData()
{
    return array(
        'DisplayName' => 'Cherryservers',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '1111', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '1112', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @see https://developers.whmcs.com/provisioning-modules/config-options/
 *
 * @return array
 */
function cherryservers_ConfigOptions($params)
{
    $settings =  array(
        'API Key' => array(
            'Type' => 'password'
        )
     );

    try{
        $productId = $_REQUEST["id"];
        $product = Capsule::table("tblproducts")->where("id",$productId)->first();
        $apiKey = $product->configoption1;
        // Validate API key
        if($apiKey){
            $teams = new \CherryServers\Teams($apiKey);
            $response = $teams->getTeams();
            if($response->error){
                $settings['API Key']['Description'] = '<span class="label label-danger ">Invalid API key</span>';
            }else{
                $settings['API Key']['Description'] = '<span class="label label-success ">Valid API key</span>';
                $settings["Team"] = array(
                    'Type' => 'dropdown',
                    'Options' => []
                );
                foreach($response as $team){
                    $settings['Team']['Options'][$team->id] = $team->name;
                }
                if( $product->configoption2 ){
                    $plans = new \CherryServers\Plans($apiKey);
                    $response = $plans->getPlans($product->configoption2);
                    $settings["Plan"] = array(
                        'Type' => 'dropdown',
                        'Options' => []
                    );
                    foreach($response as $plan){
                        $settings['Plan']['Options'][$plan->id] = $plan->name;
                    }
                    $settings["Region"] = array(
                        'Type' => 'dropdown',
                        'Options' => [
                            'EU-East-1' => 'EU-East-1',
                            'EU-West-1' => 'EU-West-1'
                        ]
                    );

                }
            }
        }
        return $settings;
    }catch(Exception $e){
    }
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cherryservers_CreateAccount(array $params)
{
    try {
        if( ( $params['status'] == 'Active') ){
            return "Service already provisioned";
        }
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Teams($apiKey);
        $projects = $client->getTeamProjects($params['configoption2']);
        $project = '';
        foreach($projects as $v){
            if($v->name == $params['userid']){
                $project = $v;
            }
        }
        if( !$project ){
            $client = new \CherryServers\Projects($apiKey);
            $project = $client->createProject($params['configoption2'], $params['userid'] );

        }
        $settings = [
            'plan_id' => $params['configoption3'],
            'image' =>  $params['customfields']['os'],
            'region' => $params['configoption4'],
            'hostname' => $params['customfields']['hostname'],
            'ip_addresses' => []
        ];
        $client = new \CherryServers\Servers($apiKey);
        $response = $client->deployServer($project->id,$settings);
        if($response->error){
            return  $response->error;
        }else{
            $server = explode('/',$response->href);
            $row = Capsule::table('tblcustomfields')->where('fieldname', 'like', 'serverid%')->where('relid', $params['pid'])->first();
            Capsule::table('tblcustomfieldsvalues')->where('fieldid', $row->id)->where('relid', $params['serviceid'])->update(['value' => $server[2]]);
            return 'success';
        }

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cherryservers_SuspendAccount(array $params)
{
    try {
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Servers($apiKey);
        $response = $client->powerOff($params['customfields']['serverid']);
        if($response->error){
            return  $response->error;
        }else{
            return 'success';
        }

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );

        return $e->getMessage();
    }

}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cherryservers_UnsuspendAccount(array $params)
{
    try {
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Servers($apiKey);
        $response = $client->powerOn($params['customfields']['serverid']);
        if($response->error){
            return  $response->error;
        }else{
            return 'success';
        }

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );

        return $e->getMessage();
    }
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cherryservers_TerminateAccount(array $params)
{
    try {
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Servers($apiKey);
        $response = $client->deleteServer($params['customfields']['serverid']);
        if($response->error){
            return  $response->error;
        }else{
            return 'success';
        }

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}


/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see cherryservers_buttonOneFunction()
 *
 * @return array
 */
function cherryservers_AdminCustomButtonArray()
{
    return array(
        "Stop Server" => "stopServer",
        "Start Server" => "startServer",
    );
}

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function cherryservers_ClientAreaCustomButtonArray()
{
    return array(
    );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cherryservers_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function cherryservers_stopServer(array $params)
{
    try {
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Servers($apiKey);
        $response = $client->powerOff($params['customfields']['serverid']);
        if($response->error){
            return  $response->error;
        }else{
            return 'success';
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );
        return $e->getMessage();
    }

}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cherryservers_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function cherryservers_startServer(array $params)
{
    try {
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Servers($apiKey);
        $response = $client->powerOn($params['customfields']['serverid']);
        if($response->error){
            return  $response->error;
        }else{
            return 'success';
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );
        return $e->getMessage();
    }

}

/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cherryservers_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function cherryservers_AdminServicesTabFields(array $params)
{
    try {
        if( $params["status"] == "Active"){
            $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
            $apiKey = $product->configoption1;
            $client = new \CherryServers\Servers($apiKey);
            $response = $client->getServer($params["customfields"]["serverid"],true);
            if(!$response->error ){
                return [
                    "State" => $response->state,
                    "Power" => $response->power,
                    "Image" => $response->image,
                    "Region" => $response->region->name,
                    "Plan" => $response->plan->name
                ];
            }
        }else{
            return array(
            );
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cherryservers_AdminServicesTabFields()
 */
function cherryservers_AdminServicesTabFieldsSave(array $params)
{
    if ($originalFieldValue != $newFieldValue) {
        try {
        } catch (Exception $e) {
            logModuleCall(
                'cherryservers',
                __FUNCTION__,
                $params,
                $e->getMessage()
            );
        }
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function cherryservers_ClientArea(array $params)
{
    $lang = isset($_SESSION['Language']) ? $_SESSION['Language'] : $params['clientsdetails']['language'];
    if( file_exists ( dirname(__FILE__) . '/lang/' . $lang . '.php' ) ){
         require dirname(__FILE__) . '/lang/' . $lang . '.php';
    }else{
        require dirname(__FILE__) . '/lang/english.php';
    }
    if( ( $params['status'] != 'Active') ){
        return array(
             'tabOverviewReplacementTemplate' => 'templates/overview.tpl',
             'templateVariables' => array(
                 'lang' => $lang,
             ),
         );

    }


    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';
    try{
        $product = Capsule::table("tblproducts")->where("id",$params['pid'])->first();
        $apiKey = $product->configoption1;
        $client = new \CherryServers\Servers($apiKey);
        $error = '';
        $success = false;
        if ($requestedAction == 'poweroff') {
            $response = $client->powerOff($params['customfields']['serverid']);
            if($response->error){
                $error = $response->error;
            }else{
                $success = true;
            }
            $templateFile = 'templates/overview.tpl';
        }elseif( $requestedAction == 'poweron' ){
            $response = $client->powerOn($params['customfields']['serverid']);
            if($response->error){
                $error = $response->error;
            }else{
                $success = true;
            }
            $templateFile = 'templates/overview.tpl';
        }elseif( $requestedAction == 'restart' ){
            $response = $client->reboot($params['customfields']['serverid']);
            if($response->error){
                $error = $response->error;
            }else{
                $success = true;
            }
            $templateFile = 'templates/overview.tpl';
        }elseif( $requestedAction == 'rescue' ){
            $response = $client->rescue($params['customfields']['serverid'],$_POST['rescue-password']);
            if($response->error){
                $error = $response->error;
            }elseif( $response->message ){
                $error = $response->message;
            }else{
                $success = true;
            }
            $templateFile = 'templates/overview.tpl';
        }elseif( $requestedAction == 'rebuild' ){
            $response = $client->rebuild($params['customfields']['serverid'],$_REQUEST['hostname'],$_REQUEST['newpassword'],$_REQUEST['operatingsystem']);
            if($response->error){
                 $error = $response->error;
            }elseif( $response->message ){
                $error = $response->message;
            }else{
                $success = true;
            }
            $templateFile = 'templates/overview.tpl';
        }elseif( $requestedAction == 'exit-rescue' ){
            $response = $client->exitRescue($params['customfields']['serverid']);
            if($response->error){
                 $error = $response->error;
            }elseif( $response->message ){
                $error = $response->message;
            }else{
                $success = true;
            }
            $templateFile = 'templates/overview.tpl';
        } else {
            $serviceAction = 'get_stats';
            $templateFile = 'templates/overview.tpl';
        }
        $response = $client->getServer($params['customfields']['serverid'],true);
        $client = new \CherryServers\Images($apiKey);
        $images = $client->getImages($response->plan->id);
        logModuleCall('cherryservers','Server Info', $params,$response);
        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                'server' => $response,
                'images' => $images,
                'lang' => $lang,
                'error' => $error,
                'success' => $success
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cherryservers',
            __FUNCTION__,
            $params,
            $e->getMessage()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}
