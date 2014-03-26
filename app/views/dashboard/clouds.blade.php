 {{-- var_dump($clouds) --}}
 

@foreach($clouds as $cloud)
	{{--  $userCloudName = $cloud->user_cloud_name  --}}
        <li class="cloud" id="{{ $cloud->name }}">
        	<span class="glyphicon glyphicon-hdd float-left">
        	</span>
        	<span class="cloud-name hidden-xs hidden-sm" id="{{ $cloud->user_cloudID }}">
        		{{ $cloud->user_cloud_name }}
        	</span>
        	<span class="clear-both"></span>
        </li>

@endforeach
