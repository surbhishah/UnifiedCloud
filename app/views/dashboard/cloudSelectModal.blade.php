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
            <a href="{{ route('authenticate_route',
            array('cloudName' => 'Dropbox','userCloudName' => 'mycloud')) }}"><img src={{ asset('packages/img/dropbox-icon.png') }}></a>
            Dropbox</div>
        <!-- dropbox icon -->
          <div class="col-md-6">
            <a href="{{ route('authenticate_route',array('cloudName' => 'Drive')) }}"><img src={{ asset('packages/img/google_drive_icon.png') }}></a>
            Drive
          </div>
        </div>
        <!-- dropbox icon -->
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
