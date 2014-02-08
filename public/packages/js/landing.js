function step(){


        // Move the #stage div. Changing the top property will trigger
        // a css transition on the element. i-1 because we want the
        // steps to start from 1:

        features.css('top',(0*100)+'%');
}

$('.alert').alert();

$('.container').notify($('.notification').html(),{
			'arrowShow' : false,
			'elementPosition' : 'top center',
			'globalPosition' : 'top center',
			'className' : 'success',
			'autoHideDelay' : '2000',
			'showAnimation' : 'fadeIn',
			'hideAnimation' : 'fadeOut'
 		});

	$('.learn-more .glyphicon').click(function(){
	     $('html,body').animate({
	          scrollTop: $('.features').offset().top
	        }, 1000);
	});


