{{-- */ $cwd = array('path' => array('Home','someFolder'), 'cwd' => 'cFolder'); /*--}}

<ol class='breadcrumb'>
    @foreach($cwd['path'] as $dir)
        <li>
            <a href="#">{{ $dir }}</a>
        </li>
    @endforeach
    <li class="active">{{ $cwd['cwd'] }}</li>
</ol>

