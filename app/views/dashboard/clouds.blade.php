 {{-- var_dump($clouds) --}}
 
<li class="cloud" id="all">
	<span class="glyphicon glyphicon-hdd float-left">
	</span>
	<span class="cloud-name hidden-xs hidden-sm" id="0">
		All Clouds
	</span>
	<span class="clear-both"></span>
</li>
@foreach($clouds as $cloud)
	{{--  $userCloudName = $cloud->user_cloud_name  --}}

        <li class="cloud user-clouds" id="{{ $cloud->name }}">
        	<span class="glyphicon glyphicon-hdd float-left">
        	</span>
        	<span class="cloud-name hidden-xs hidden-sm" id="{{ $cloud->user_cloudID }}">
        		{{ $cloud->user_cloud_name }}
        	</span>
        	<span class="clear-both"></span>
        </li>

@endforeach
