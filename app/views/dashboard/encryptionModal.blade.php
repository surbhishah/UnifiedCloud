<div class="modal fade" id="encryptedFileDownloadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Enter Passkey</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="fileDownloadForm" method="post" 
        action="downloadEncryptedFile">
          <div class="passKeyInput">
            <label for="passkeyid">Enter pass key</label>
            <input type="password" id="passkeyid" class="form-control" name="passKey">
          </div>
          <!-- <div>
            <input type="hidden" name="cloudSourcePath" value="">
          </div>
          <div>
            <input type="hidden" name="fileName" value="">
          </div>
          <div>
            <input type="hidden" name="userCloudID" value="">
          </div>
          <div>
            <input type="hidden" name="cloudName" value="">
          </div> -->
          <button type="submit" class="btn btn-custom-small">Download</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->