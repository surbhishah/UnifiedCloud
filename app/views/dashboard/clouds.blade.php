{{-- */ $clouds = array('Drive','Dropbox','Skydrive'); /* --}}

@foreach($clouds as $cloud)
    <button class="btn btn-default">
        <span class="myicons myicons-drive pull-left"> </span>
        <span class="pull-left">{{$cloud}}</span>
    </button>
@endforeach
