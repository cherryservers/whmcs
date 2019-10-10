<div id="rescue-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{$lang.rescue}</h4>
      </div>
      <form method="post" action="clientarea.php?action=productdetails">
      <div class="modal-body">
              <div class="form-group">
                  <input type="hidden" name="id" value="{$serviceid}" />
                  <input type="hidden" name="customAction" value="rescue" />
              </div>
              <div class="form-group">
                  <label>{$lang.password}</label>
                  <input type="password" name="rescue-password"  class="form-control" required>
              </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">{$lang.rescue}</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
    </form>
  </div>
</div>
