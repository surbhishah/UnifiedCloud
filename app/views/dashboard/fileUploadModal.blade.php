<div class="modal fade upload-file" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Select file for upload</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="fileUploadForm">
          <div class="form-group">
            <label>Select a file to upload</label>
            <input type="file" name="files[]" id="userfile" multiple="1">
          </div>
          <div>
            <input type="checkbox" id="encryptCheck">
            <label>Encrypt file before upload</label>
          </div>
          <div>
            <input type="hidden" name="cloudDestinationPath" value="">
          </div>
          <div>
            <input type="hidden" name="userCloudID" value="">
          </div>
          <button type="submit" class="btn btn-custom-small">Upload</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->