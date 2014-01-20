<div class="modal fade select-cloud" id="SelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Select Clouds</h4>
      </div>
      <div class="modal-body">
        <a href="{{ route('authenticate_route',array('cloudName' => 'Dropbox')) }}"><img src={{ asset('packages/img/dropbox-icon.png') }}></a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
