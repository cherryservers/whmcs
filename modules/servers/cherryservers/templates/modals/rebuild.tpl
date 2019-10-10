<div id="rebuild-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{$lang.rebuild}</h4>
      </div>
      <form method="post" action="clientarea.php?action=productdetails">
      <div class="modal-body">
              <div class="form-group">
                  <input type="hidden" name="id" value="{$serviceid}" />
                  <input type="hidden" name="customAction" value="rebuild" />
              </div>
              <div class="form-group">
                  <label>{$lang.hostname}</label>
                  <input type="text" name="hostname" class="form-control" placeholder="server-hostname" value="{$server->hostname}" required>
              </div>
              <div class="form-group">
                  <label>{$lang.operatingsystem}</label>
                  <select name="operatingsystem" class="form-control" required>
                      {foreach $images as $image}
                          {if !$image->pricing}
                              <option value="{$image->name}" {if $image->name eq $server->image } SELECTED {/if}>{$image->name}</option>
                          {/if}
                      {/foreach}
                  </select>
              </div>
              <div class="form-group">
                  <label>{$lang.password}</label>
                  <input type="password" name="newpassword"  class="form-control" required>
              </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">{$lang.rebuild}</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
    </form>
  </div>
</div>
