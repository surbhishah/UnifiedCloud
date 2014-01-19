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
            <label for="InputFile">File input</label>
            <input type="file" name="userfile" id="userfile">
          </div>
          <div>
            <input type="hidden" name="cloudDestinationPath" value="">
          </div>
          <button type="submit" class="btn btn-default">Upload</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->