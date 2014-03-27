<div class="modal fade share-file" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="share" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Select user or group to share file</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="shareForm" autocomplete="off">
          <div class="form-group">
            <label for="share-search">Share with...</label>
            <input type="text" id="share-search" class="form-control" name="share_with">
          </div>
          <div id="shareUserList">
           <!-- show selected users -->
          </div>
          <div>
            <input type="hidden" name="cloudDestinationPath" value="">
          </div>
          <div>
            <input type="hidden" name="userCloudID" value="">
          </div>
            <button type="submit" class="btn btn-custom-small pull-right">Share</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->