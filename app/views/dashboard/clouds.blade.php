{{--  $clouds = array('Drive','Dropbox','Skydrive');  --}}

@foreach($clouds as $cloud)
	{{-- */ $cloudName = $cloud->name /* --}}
    <button class="btn btn-default-new">
        <span class="myicons myicons-drive pull-left"> </span>
        <span class="pull-left cloud" id="{{ $cloudName }}">
        	{{ $cloudName }}
        </span>
    </button>
@endforeach
