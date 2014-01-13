<br>
<br>
<br>
<br>

{{ Form::open(array('route'=>'refresh_route', 'as'=>'refresh_cloud','method'=>'get')) }}
{{ Form::hidden('userID','1' )	}}
{{ Form::label('Cloud', 'Cloud:')	}}
{{ Form::text('cloudName')}}			
{{ Form::submit('Refresh')	}}
{{ Form::close()	}}

<br>
<br>
<br>

{{ Form::open(array('route'=>'add_cloud_route', 'as'=>'add_cloud','method'=>'get')) }}
{{ Form::hidden('userID','1' )	}}
{{ Form::label('Cloud', 'Cloud:')	}}
{{ Form::text('cloudName')}}			
{{ Form::submit('Add Cloud')	}}
{{ Form::close()	}}

<br>
<br>
<br>

{{ Form::open(array('route'=>'upload_route', 'files' => true, 'as'=>'upload','method'=>'post')) }}
{{ Form::hidden('cloudName','Dropbox' )	}}
{{ Form::hidden('userID','1' )	}}
{{ Form::label('cloudDestination path', 'Cloud destination without trailing slash:')	}}
{{ Form::text('cloudDestinationPath')}}			
{{ Form::label('file', 'File:')	}}
{{ Form::file('userfile')	}}
{{ Form::submit('Upload')	}}
{{ Form::close()	}}


<br>
<br>
<br>


{{ Form::open(array('route'=>'download_route', 'files' => true, 'as'=>'download','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('cloudSource','Cloud SOurce Path eg. /Project/SubProject :::')}}
{{ Form::text('cloudSourcePath')	}}<br>
{{ Form::label('file','FileName: ')}}
{{ Form::text('fileName')	}}<br>

{{ Form::submit('Download')	}}
{{ Form::close()	}}
<br>
<br>
<br>


{{ Form::open(array('route'=>'folder_content_route',  'as'=>'folder_content','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('folder','Folder eg /Projects/Subproject :::')}}
{{ Form::text('folderPath')	}}<br>
{{ Form::submit('Get folder Contents')	}}
{{ Form::close()	}}

<br>
<br>
<br>


{{ Form::open(array('route'=>'create_folder_route', 'as'=>'create_folder','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('folder','Folder eg /Projects/Subproject :::')}}
{{ Form::text('folderPath')	}}<br>
{{ Form::submit('Create folder ')	}}
{{ Form::close()	}}

<br>
<br>
<br>


{{ Form::open(array('route'=>'delete_route', 'as'=>'delete_folder','method'=>'delete')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('file/folder','Folder eg /Projects/Subproject File eg /Projects/file.txt:::')}}
{{ Form::text('path')	}}<br>
{{ Form::submit('Delete ')	}}
{{ Form::close()	}}

