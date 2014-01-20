$('[name=first_name]').popover({
	"content": $('.first_name_error').html(),
	"placement": "left"
}).popover('show');

$('[name=last_name]').popover({
	"content": $('.last_name_error').html(),
	"placement": "left"
}).popover('show');

$('[name=email]').popover({
	"content": $('.email_error').html(),
	"placement": "left"
}).popover('show');

$('[name=password]').popover({
	"content": $('.password_error').html(),
	"placement": "left"
}).popover('show');