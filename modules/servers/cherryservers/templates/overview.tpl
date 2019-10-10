<h3>{$LANG.clientareaproductdetails}</h3>

<hr>

<div class="row">
    <div class="col-sm-5">
        {$LANG.clientareahostingregdate}
    </div>
    <div class="col-sm-7">
        {$regdate}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderproduct}
    </div>
    <div class="col-sm-7">
        {$groupname} - {$product}
    </div>
</div>

{if $type eq "server"}
    {if $domain}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.serverhostname}
            </div>
            <div class="col-sm-7">
                {$domain}
            </div>
        </div>
    {/if}
    {if $dedicatedip}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.primaryIP}
            </div>
            <div class="col-sm-7">
                {$dedicatedip}
            </div>
        </div>
    {/if}
    {if $assignedips}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.assignedIPs}
            </div>
            <div class="col-sm-7">
                {$assignedips|nl2br}
            </div>
        </div>
    {/if}
    {if $ns1 || $ns2}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.domainnameservers}
            </div>
            <div class="col-sm-7">
                {$ns1}<br />{$ns2}
            </div>
        </div>
    {/if}
{else}
    {if $domain}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.orderdomain}
            </div>
            <div class="col-sm-7">
                {$domain}
                <a href="http://{$domain}" target="_blank" class="btn btn-default btn-xs">{$LANG.visitwebsite}</a>
            </div>
        </div>
    {/if}
    {if $serverdata}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.servername}
            </div>
            <div class="col-sm-7">
                {$serverdata.hostname}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                {$LANG.domainregisternsip}
            </div>
            <div class="col-sm-7">
                {$serverdata.ipaddress}
            </div>
        </div>
        {if $serverdata.nameserver1 || $serverdata.nameserver2 || $serverdata.nameserver3 || $serverdata.nameserver4 || $serverdata.nameserver5}
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.domainnameservers}
                </div>
                <div class="col-sm-7">
                    {if $serverdata.nameserver1}{$serverdata.nameserver1} ({$serverdata.nameserver1ip})<br />{/if}
                    {if $serverdata.nameserver2}{$serverdata.nameserver2} ({$serverdata.nameserver2ip})<br />{/if}
                    {if $serverdata.nameserver3}{$serverdata.nameserver3} ({$serverdata.nameserver3ip})<br />{/if}
                    {if $serverdata.nameserver4}{$serverdata.nameserver4} ({$serverdata.nameserver4ip})<br />{/if}
                    {if $serverdata.nameserver5}{$serverdata.nameserver5} ({$serverdata.nameserver5ip})<br />{/if}
                </div>
            </div>
        {/if}
    {/if}
{/if}

{if $dedicatedip}
    <div class="row">
        <div class="col-sm-5">
            {$LANG.domainregisternsip}
        </div>
        <div class="col-sm-7">
            {$dedicatedip}
        </div>
    </div>
{/if}

{foreach from=$configurableoptions item=configoption}
    <div class="row">
        <div class="col-sm-5">
            {$configoption.optionname}
        </div>
        <div class="col-sm-7">
            {if $configoption.optiontype eq 3}
                {if $configoption.selectedqty}
                    {$LANG.yes}
                {else}
                    {$LANG.no}
                {/if}
            {elseif $configoption.optiontype eq 4}
                {$configoption.selectedqty} x {$configoption.selectedoption}
            {else}
                {$configoption.selectedoption}
            {/if}
        </div>
    </div>
{/foreach}

{if $lastupdate}
    <div class="row">
        <div class="col-sm-5">
            {$LANG.clientareadiskusage}
        </div>
        <div class="col-sm-7">
            {$diskusage}MB / {$disklimit}MB ({$diskpercent})
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5">
            {$LANG.clientareabwusage}
        </div>
        <div class="col-sm-7">
            {$bwusage}MB / {$bwlimit}MB ({$bwpercent})
        </div>
    </div>
{/if}

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderpaymentmethod}
    </div>
    <div class="col-sm-7">
        {$paymentmethod}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.firstpaymentamount}
    </div>
    <div class="col-sm-7">
        {$firstpaymentamount}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.recurringamount}
    </div>
    <div class="col-sm-7">
        {$recurringamount}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.clientareahostingnextduedate}
    </div>
    <div class="col-sm-7">
        {$nextduedate}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderbillingcycle}
    </div>
    <div class="col-sm-7">
        {$billingcycle}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.clientareastatus}
    </div>
    <div class="col-sm-7">
        {$status}
    </div>
</div>

{if $suspendreason}
    <div class="row">
        <div class="col-sm-5">
            {$LANG.suspendreason}
        </div>
        <div class="col-sm-7">
            {$suspendreason}
        </div>
    </div>
{/if}

<hr>

{if $systemStatus eq "Active"}
<h3>{$lang.service_management}</h3>

{if $success}
    <div class="alert alert-success">{$lang.action_success}</div>
{/if}

{if $error}
    <div class="alert alert-danger">{$error}</div>
{/if}

{if $server->password}
    <div class="alert alert-info alert-dismissible show">
        <strong>{$lang.root_password}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button><br/>
        <i class="fas fa-info-circle"></i> {$lang.password_info} {$server->password}
    </div>
{/if}
<div class="row">
    <!-- Hardware panel -->
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                {$lang.hardware}
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.cpu}
                    </div>
                    <div class="col-sm-7">
                       {$server->plan->specs->cpus->name} {$server->plan->specs->cpu->cores} x {$server->plan->specs->cpus->frequency} {$server->plan->specs->cpus->unit}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.memory}
                    </div>
                    <div class="col-sm-7">
                        {$server->plan->specs->memory->name}
                   </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.storage}
                    </div>
                    <div class="col-sm-7">
                        {foreach from=$server->plan->specs->storage item=$storage}
                           {$storage->count} x {$storage->name} <br>
                        {/foreach} 
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.operatingsystem}
                    </div>
                    <div class="col-sm-7">
                        {$server->image}
                   </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.region}
                    </div>
                    <div class="col-sm-7">
                        {$server->region->name}
                   </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.power}
                    </div>
                    <div class="col-sm-7">
                        {$server->power}
                   </div>
                </div>



            </div>
        </div>
    </div>
    <!-- Network panel -->
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                {$lang.network}
            </div>
            <div class="panel-body">
                {foreach from=$server->ip_addresses item=ip}
                    <div class="row">
                        <div class="col-sm-5">
                           {$lang.address} ( {$ip->type} )
                        </div>
                        <div class="col-sm-7">
                           {$ip->address}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                           {$lang.network}
                        </div>
                        <div class="col-sm-7">
                           {$ip->cidr}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                           {$lang.gateway}
                        </div>
                        <div class="col-sm-7">
                           {$ip->gateway}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                           {$lang.ptr}
                        </div>
                        <div class="col-sm-7">
                           {$ip->ptr_record}
                        </div>
                    </div>
                {/foreach}
                <div class="row">
                    <div class="col-sm-5">
                        {$lang.bandwidth}
                    </div>
                    <div class="col-sm-7">
                        {$server->plan->specs->nics->name}( {$server->plan->specs->bandwidth->name} {$lang.included} )                      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>
{if $server->state eq "pending"}
    <div class="alert alert-warning">{$lang.deploy_pending}</div>
{elseif  $server->state eq "deploying"}
    <div class="alert alert-warning">{$lang.rebuild_pending}</div>
{elseif  $server->state eq "provisioning"}
     <div class="alert alert-warning">{$lang.deploy_pending}</div>
{elseif  $server->status eq "entering rescue mode"}
     <div class="alert alert-warning">{$lang.rescue_pending}</div>
{elseif  $server->status eq "exiting rescue mode"}
      <div class="alert alert-warning">{$lang.exit_rescue_pending}</div>
{else}
{if  $server->status eq "rescue mode"}
    <div class="alert alert-warning">{$lang.rescue_mode}</div>
{/if}
<div class="row">
    <!--
    <div class="col-sm-3">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="manage" />
            <button type="submit" class="btn btn-info btn-block">
                {$lang.kvm_button}
            </button>
        </form>
    </div>
    -->
    {if $server->status != "rescue mode" }
    {if $server->power != "off"}
    <div class="col-sm-3">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="poweroff" />
            <button type="submit" class="btn btn-danger btn-block">
                {$lang.power_off_button}
            </button>
        </form>
    </div>
    {/if}
    {if $server->power eq "off"}
    <div class="col-sm-3">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="poweron" />
            <button type="submit" class="btn btn-success btn-block">
                {$lang.power_on_button}
            </button>
        </form>
    </div>
    {/if}

    <div class="col-sm-3">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="restart" />
            <button type="submit" class="btn btn-danger btn-block">
                {$lang.restart_button}
            </button>
        </form>
    </div>

    {if $server->rescue_available && ( $server->status != "rescue mode") }
    <div class="col-sm-2">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="rescue" />
            <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#rescue-modal">
                {$lang.rescue_button}
            </button>
        </form>
    </div>
    {/if}
    <div class="col-sm-3">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="rebuild" />
            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rebuild-modal">
                {$lang.rebuild_button}
            </button>
        </form>
    </div>
    {else}
        <div class="col-sm-2">
            <form method="post" action="clientarea.php?action=productdetails">
                <input type="hidden" name="id" value="{$serviceid}" />
                <input type="hidden" name="customAction" value="exit-rescue" />
                <button type="submit" class="btn btn-success btn-block">
                    {$lang.exit_rescue_button}
                </button>
            </form>
        </div>
    {/if}
</div>
<hr>
<div class="row">
    {if $packagesupgrade}
        <div class="col-sm-4">
            <a href="upgrade.php?type=package&amp;id={$id}" class="btn btn-success btn-block">
                {$LANG.upgrade}
            </a>
        </div>
    {/if}

    <div class="col-sm-4">
        <a href="clientarea.php?action=cancel&amp;id={$id}" class="btn btn-danger btn-block{if $pendingcancellation}disabled{/if}">
            {if $pendingcancellation}
                {$LANG.cancellationrequested}
            {else}
                {$LANG.cancel}
            {/if}
        </a>
    </div>
</div>

{include file="modules/servers/cherryservers/templates/modals/rebuild.tpl"}
{include file="modules/servers/cherryservers/templates/modals/rescue.tpl"}
{/if}
{/if}
