 {{-- var_dump($clouds) --}}
 

@foreach($clouds as $cloud)
	{{--  $userCloudName = $cloud->user_cloud_name  --}}
        <li class="cloud" id="{{ $cloud->name }}">
        	<span id="{{ $cloud->user_cloudID }}">{{ $cloud->user_cloud_name }}</span>
        </li>

@endforeach
