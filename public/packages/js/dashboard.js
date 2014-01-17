$(function(){

cloud ="";

function testFunction(str) {
	$(".cloud-controls").click(function(){
		alert(str);
	});
}

function getFolderContents(cloud,fPath) {
	$.ajax({
		type:"GET",
		url:"folder_content/",
		data: {cloudName: cloud , folderPath: fPath}
	})
	.done(function(jsonData){
		//console.log(jsonData);

		//server sends json as string
		//parsing json string to json object
		jsonData = $.parseJSON(jsonData);
		console.log(jsonData);

		var table = $('#file-explorer');
		var tbody = table.find('tbody');

		//we need the cloud name to make further ajax calls
		//therefore appending cloud name as class name to tbody
		//tbody.addClass(cloud);
		tbody.empty();
		$.each(jsonData,function(i,file){
			if(file.is_directory == '1') {
				var tr=$("<tr class='folder'></tr>");
				tbody.append(tr);
				var td = $("<td class='directory'>" + file.file_name +"</td>" );
				tr.append(td);
			} else {
				var tr=$("<tr></tr>");
				tbody.append(tr);
				var td = $("<td>" + file.file_name +"</td>" );
				tr.append(td);
			}

			//getting extension of file
			var ext = file.file_name.split('.').pop();

			//using jqery-dateformat plugin to get more readable date data.
			var td = $("<td>" + $.format.prettyDate(file.last_modified_time) +"</td>" );
			tr.append(td);
			
			if(file.is_directory == '1') {
				var td = $("<td>-</td>" );
				tr.append(td);
				var td = $("<td>Folder</td>" );
				tr.append(td);
			} else {
				var td = $("<td>" + file.size +"</td>" );
				tr.append(td);
				var td = $("<td>" + ext +"</td>" );
				tr.append(td);

			}
		});
	}); 

}

$('.cloud').click(function(){

	cloud = this.id;
	var fPath = '/';

	//populate breadcrumb
	$('.breadcrumb').html('<li>'+cloud+'</li>');
	$('#cwd').html(fPath);
	/*$.ajax({
		type:"GET",
		url:"user/folder_content/",
		data: {cloudName: cloud , folderPath: fPath}
	})
	.done(function(jsonData){
		//console.log(jsonData);

		//server sends json as string
		//parsing json string to json object
		jsonData = $.parseJSON(jsonData);
		//alert(jsonData[3].file_name);

		var table = $('#file-explorer');
		var tbody = table.find('tbody');

		//we need the cloud name to make further ajax calls
		//therefore appending cloud name as class name to tbody
		//tbody.addClass(cloud);
		tbody.empty();
		$.each(jsonData,function(i,file){
			if(file.is_directory == '1') {
				var tr=$("<tr class='folder'></tr>");
				tbody.append(tr);
				var td = $("<td class='directory'>" + file.file_name +"</td>" );
				tr.append(td);
			} else {
				var tr=$("<tr></tr>");
				tbody.append(tr);
				var td = $("<td>" + file.file_name +"</td>" );
				tr.append(td);
			}

			//getting extension of file
			var ext = file.file_name.split('.').pop();

			//using jqery-dateformat plugin to get more readable date data.
			var td = $("<td>" + $.format.prettyDate(file.last_modified_time) +"</td>" );
			tr.append(td);
			
			if(file.is_directory == '1') {
				var td = $("<td>-</td>" );
				tr.append(td);
				var td = $("<td>Folder</td>" );
				tr.append(td);
			} else {
				var td = $("<td>" + file.size +"</td>" );
				tr.append(td);
				var td = $("<td>" + ext +"</td>" );
				tr.append(td);

			}
		});
	});*/ 

	getFolderContents(cloud,fPath);
});

$("#file-explorer tbody").on("click","tr.folder",function(){
	//alert("working!" + $(this).find('td.directory').html());
	var nextPath =	$(this).find('td.directory').html();
	var cwd = $('#cwd').html();

	//get breadcrumb list
	var breadcrumb = $('.breadcrumb');
	if(cwd == '/')
		var fPath = cwd  + nextPath;
	else
		var fPath = cwd + '/' + nextPath;
	
	breadcrumb.append('<li>'+nextPath+'</li>');
	$('#cwd').html(fPath);

	//TODO change this hardcoded Dropbox.
	getFolderContents(cloud,fPath);
});

//register click on table row.
$('#file-explorer tbody').on('click','tr',function(){
	//alert('clicked');
	$('tr').not(this).removeClass('clicked-row');
    $(this).toggleClass('clicked-row'); 
});

//breacrumb controls
$('.breadcrumb').on('click','li',function(){
	//alert($(this).html());
	var val = $(this);
    fPathArray = new Array();
    
    while(!(typeof(val.prev().html()) === 'undefined')){
        fPathArray.push(val.html());
        val = val.prev();
    }
    
    val = $(this);
    val = val.nextAll().remove();
    
    fPathArray.push('r');
    fPathArray.reverse();
    var fPath = fPathArray.join('/').substr(1);
	if(fPath == '')
		fPath = '/';
	$('#cwd').html(fPath);
	getFolderContents(cloud,fPath);
	//alert(fPath);
});

//download
$('#download').on('click',function(){
	//alert("working!");

	//set variables for ajax call
	var cwd = $('#cwd').html();
	var file = $('#file-explorer tbody tr.clicked-row').find('td').html(); 
	
	//alert(cwd + " : "+ file);
	window.location.href = "http://localhost/UnifiedCloud/public/index.php/user/download/?cloudName=" + cloud + "&cloudSourcePath=" + cwd + "&fileName=" + file; 
});


//upload
$('#fileUploadForm').submit(function(e) {
       	e.preventDefault();

       	$('[name="cloudDestinationPath"]').attr('value',$('#cwd').html());
        data = new FormData($('#fileUploadForm')[0]);
        console.log('Submitting');
        $.ajax({
            type: 'POST',
            url: 'upload/Dropbox',
            data: data,
            cache: false,
            contentType: false,
            processData: false
        }).done(function(data) {
            console.log(data);
        }).fail(function(jqXHR,status, errorThrown) {
            console.log(errorThrown);
            console.log(jqXHR.responseText);
            console.log(jqXHR.status);
        });
});


//refresh
$('#refresh').on('click',function(){
	$.ajax({
		type:"GET",
		url:"refresh/"+cloud
	})
	.done(function(data){
		console.log(data);
	});

	getFolderContents(cloud,$('#cwd').html());
});

});//end of document
