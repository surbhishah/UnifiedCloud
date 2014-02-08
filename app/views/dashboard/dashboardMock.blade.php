<html>
<head>
	<title>

	</title>

    {{ HTML::script('packages/bootstrap/js/jquery-1.10.js')}}
	{{ HTML::script('packages/bootstrap/js/bootstrap.min.js')}}
	{{ HTML::style('packages/bootstrap/css/bootstrap-yeti.css') }}
	{{ HTML::style('packages/css/stylesheets/mock.css') }}
</head>
<body>
<div class="navbar navbar-default">
</div>

<div class="container">
	<div class="row">
		<div class="col-md-2" id="side-pane">
			<ul class="nav nav-stacked">
				<li id="dashboard">Dashboard</li>
				<li class="sp-button selected">Cloud</li>
				<li>Settings</li>
				<li>Help</li>
			</ul>
		</div>
		<div class="col-md-10" id="file-explorer"></div>
	</div>
</div>
</body>
</html>