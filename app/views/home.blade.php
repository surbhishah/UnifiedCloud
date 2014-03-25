{{	Form::label('Welcome')	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'search_files_route', 'as'=>'search_files','method'=>'get')) }}
{{ Form::label('userID','userID ')}}
{{ Form::text('userID')	}}<br>
{{ Form::submit('Search file ')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'unshare_group_route', 'as'=>'unshare_group','method'=>'get')) }}
{{ Form::label('groupID','GroupID ')}}
{{ Form::text('groupID')	}}<br>
{{ Form::label('fileID','fileID ')}}
{{ Form::text('fileID')	}}<br>
{{ Form::submit('Unshare group')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'delete_group_route', 'as'=>'delete_group','method'=>'delete')) }}
{{ Form::label('groupID','GroupID ')}}
{{ Form::text('groupID')	}}<br>
{{ Form::submit('Delete group')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'share_with_group_route', 'as'=>'share_group','method'=>'get')) }}
{{ Form::label('groupID','GroupID ')}}
{{ Form::text('groupID')	}}<br>
{{ Form::label('userID','userID')}}
{{ Form::text('userID')	}}<br>
{{ Form::label('fileID','fileID')}}
{{ Form::text('fileID')	}}<br>
{{ Form::submit('Share with group')	}}
{{ Form::close()	}}

<br>
<br>
<br>
{{ Form::open(array('route'=>'search_user_group_route', 'as'=>'search','method'=>'get')) }}
{{ Form::label('searchString','searchString:')}}
{{ Form::text('searchString')	}}<br>
{{ Form::submit('Search')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'delete_group_member_route', 'as'=>'delete_member','method'=>'delete')) }}
{{ Form::label('groupID','GroupID ')}}
{{ Form::text('groupID')	}}<br>
{{ Form::label('memberID','memberID ')}}
{{ Form::text('memberID')	}}<br>
{{ Form::label('userID','userID ')}}
{{ Form::text('userID')	}}<br>
{{ Form::submit('Delete member ')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'get_group_members_route', 'as'=>'get_members','method'=>'get')) }}
{{ Form::label('groupID','GroupID ')}}
{{ Form::text('groupID')	}}<br>
{{ Form::submit('Get group members')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'add_member_route', 'as'=>'add_member','method'=>'post')) }}
{{ Form::label('groupID','GroupID ')}}
{{ Form::text('groupID')	}}<br>
{{ Form::label('emamil','New Member email')}}
{{ Form::text('newMemberEmail')	}}<br>
{{ Form::submit('Add this user to this group')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'get_groups_route', 'as'=>'get_groups','method'=>'get')) }}
{{ Form::label('userID','1')}}
{{ Form::text('userID')	}}<br>
{{ Form::submit('Get Groups to which this user belongs')	}}
{{ Form::close()	}}
<br>
<br>
<br>
{{ Form::open(array('route'=>'create_group_route', 'as'=>'create_group','method'=>'get')) }}
{{ Form::label('Group Name','Group Name:')}}
{{ Form::hidden('userID','1')}}
{{ Form::text('groupName')	}}<br>
{{ Form::submit('Create Group')	}}
{{ Form::close()	}}




<br>
<br>
<br>
{{ Form::open(array('route'=>'get_full_file_structure_route', 'as'=>'get_full_file_structure','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox' )	}}
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::submit('Get full file structure')	}}
{{ Form::close()	}}
<br>
<br>
<br>

{{ Form::open(array('route'=>'download_shared_file_route', 'as'=>'download_shared_file','method'=>'get')) }}
{{ Form::label('sharedFileID','sharedfileID')}}
{{ Form::text('sharedFileID')	}}
{{ Form::hidden('userID','2')}}
{{ Form::submit('Download Shared File')	}}
{{ Form::close()	}}
<br>
<br>
<br>

{{ Form::open(array('route'=>'files_shared_with_user_route', 'as'=>'sharedwith','method'=>'get')) }}
{{ Form::label('sharerID','SharerID')}}
{{ Form::text('sharerID')	}}
{{ Form::submit('Get files shared with this Sharer')	}}
{{ Form::close()	}}

<br>
<br>
<br>

{{ Form::open(array('route'=>'files_shared_by_user_route', 'as'=>'sharedby','method'=>'get')) }}
{{ Form::label('ownerID','OwnerID')}}
{{ Form::text('ownerID')	}}
{{ Form::submit('Get files shared by this owner')	}}
{{ Form::close()	}}

<br>
<br>
<br>

{{ Form::open(array('route'=>'share_file_route', 'as'=>'share','method'=>'get')) }}
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::label('Path', 'Path:')	}}
{{ Form::text('path')}}			
{{ Form::label('file name', 'File Name:')	}}
{{ Form::text('fileName')}}			
{{ Form::label('Share', 'Share with? Email ID ')	}}
{{ Form::text('sharerEmail')}}			
{{ Form::submit('Share')	}}
{{ Form::close()	}}
<br>
<br>
<br>

{{ Form::open(array('route'=>'upload_route', 'files' => true, 'as'=>'upload','method'=>'post')) }}
{{ Form::hidden('cloudName','Dropbox' )	}}
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::label('cloudDestination path', 'Cloud destination without trailing slash:')	}}
{{ Form::text('cloudDestinationPath')}}			
{{ Form::label('file', 'File:')	}}
{{ Form::file('files[]',array('multiple'=>true))	}}
{{ Form::submit('Upload Files ')	}}
{{ Form::close()	}}

<br>
<br>
<br>
{{ Form::open(array('route'=>'download_folder_route', 'as'=>'download_folder','method'=>'get')) }}
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::hidden('cloudName','Dropbox') }}
{{ Form::label('Folder Path', 'Folder Path:')	}}
{{ Form::text('folderPath')	}}<br>
{{ Form::submit('Download Folder')	}}
{{ Form::close()	}}

<br>
<br>
<br>
<br>


{{ Form::open(array('route'=>'download_route', 'files' => true, 'as'=>'download','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('cloudSource','Cloud SOurce Path eg. /Project/SubProject :::')}}
{{ Form::text('cloudSourcePath')	}}<br>
{{ Form::label('file','FileName: ')}}
{{ Form::text('fileName')	}}<br>
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::submit('Download')	}}
{{ Form::close()	}}
<br>
<br>
<br>


{{ Form::open(array('route'=>'folder_content_route',  'as'=>'folder_content','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('folder','Folder eg /Projects/Subproject :::')}}
{{ Form::text('folderPath')	}}<br>
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::label('cached','cached:')}}
{{ Form::text('cached')	}}<br>

{{ Form::submit('Get folder Contents')	}}
{{ Form::close()	}}

<br>
<br>
<br>


{{ Form::open(array('route'=>'create_folder_route', 'as'=>'create_folder','method'=>'get')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('folder','Folder eg /Projects/Subproject :::')}}
{{ Form::text('folderPath')	}}<br>
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::submit('Create folder ')	}}
{{ Form::close()	}}

<br>
<br>
<br>


{{ Form::open(array('route'=>'delete_route', 'as'=>'delete_folder','method'=>'delete')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('file/folder','Folder eg /Projects/Subproject File eg /Projects/file.txt:::')}}
{{ Form::text('path')	}}<br>
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::submit('Delete ')	}}
{{ Form::close()	}}

<br>
<br>
<br>
<br>
{{ Form::open(array('route'=>'upload_with_encryption', 'cloudName' => 'Dropbox' ,'files' => true, 'as'=>'upload','method'=>'post'))}}
{{ Form::hidden('cloudName','Dropbox' )	}}
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::label('cloudDestination path', 'Cloud destination without trailing slash:')	}}
{{ Form::text('cloudDestinationPath')}}			
{{ Form::label('passKey','passKey')}}
{{ Form::text('passKey')}}			
{{ Form::label('file', 'File:')	}}
{{ Form::file('files[]',array('multiple'=>true))	}}
{{ Form::submit('Upload Encrypted Files ')	}}
{{ Form::close()	}}

<br>
<br>
<br>
<br>


{{ Form::open(array('route'=>'download_encrypted_file', 'files' => true, 'as'=>'download','method'=>'post')) }}
{{ Form::hidden('cloudName','Dropbox')	}}
{{ Form::label('cloudSource','Cloud SOurce Path eg. /Project/SubProject :::')}}
{{ Form::text('cloudSourcePath')	}}<br>
{{ Form::label('file','FileName: ')}}
{{ Form::text('fileName')	}}<br>
{{ Form::text('passKey','User Pass Key')}}	
{{ Form::label('userCloudID','userCloudID:')}}
{{ Form::text('userCloudID')	}}<br>
{{ Form::submit('Download Encrypted File')	}}
{{ Form::close()	}}
<br>
<br>
<br>
