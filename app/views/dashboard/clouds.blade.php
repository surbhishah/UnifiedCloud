{{--  $clouds = array('Drive','Dropbox','Skydrive');  --}}

@foreach($clouds as $cloud)
	{{-- */ $cloudName = $cloud->name /* --}}
        <li class="cloud selected" id="{{ $cloudName }}">
        	{{ $cloudName }}
        </li>

@endforeach
