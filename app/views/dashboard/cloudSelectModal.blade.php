<div class="modal fade select-cloud" id="SelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Select Clouds</h4>
      </div>
      <div class="modal-body">
        <!-- cloud icons -->
        <div class="row">
          <div class="col-md-6">
<!--             <a href="{{ route('authenticate_route',
array('cloudName' => 'Dropbox','userCloudName' => 'mycloud')) }}"><img src={{ asset('packages/img/dropbox-icon.png') }}></a> -->
  <a href="#" data-toggle="modal" data-target="#dropboxAuthModal"><img src={{ asset('packages/img/dropbox-icon.png') }}></a>
            Dropbox</div>
        <!-- dropbox icon -->
          <div class="col-md-6">
            <a href="#" data-toggle="modal" data-target="#googleDriveAuthModal"><img src={{ asset('packages/img/google_drive_icon.png') }}></a>
            Drive
          </div>
        </div>
        <!-- dropbox icon -->
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Second modal to name the user cloud name. Specific to Dropbox-->
<div class="modal fade select-cloud" id="dropboxAuthModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Dropbox</h4>
      </div>
      <div class="modal-body">
        <!-- cloud icons -->
        <div class="row">
          <div class="col-md-12 col-md-offset-5">
            <img src={{ asset('packages/img/dropbox-icon.png') }}>
          </div>          
          <div class="col-md-12 col-md-offset-5">
            <h4>Dropbox</h4>
          </div>
          <div class="col-md-12 col-md-offset-3">
            <input type="text" name="userCloudName" class="form-control" placeholder="Name your cloud" value="">            
          </div>
          <div class="col-md-12 col-md-offset-5">
            <button type="submit" id="Dropbox-auth" class="btn btn-custom-small">Go!</button>       
          </div>

        </div>
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Second modal to name the user cloud name. Specific to Dropbox-->
<div class="modal fade select-cloud" id="googleDriveAuthModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">GoogleDrive</h4>
      </div>
      <div class="modal-body">
        <!-- cloud icons -->
        <div class="row">
          <div class="col-md-12 col-md-offset-5">
            <img src={{ asset('packages/img/google_drive_icon.png') }}>
          </div>          
          <div class="col-md-12 col-md-offset-5">
            <h4>Google Drive</h4>
          </div>
          <div class="col-md-12 col-md-offset-3">
            <input type="text" name="userCloudName" class="form-control" placeholder="Name your cloud" value="">            
          </div>
          <div class="col-md-12 col-md-offset-5">
            <button type="submit" id="Drive-auth" class="btn btn-custom-small">Go!</button>       
          </div>

        </div>
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
