$(document).ready(function()
{
	// Translation selector
	$('#topbar form select').change(function()
	{
		$(this).parents('form').submit();
	});

	// Syntax highlighter
	
	$('.method pre code').each(function(){
		$(this).addClass('brush: php');
	});

	$('.description pre code').each(function(){
		$(this).addClass('brush: php');
	});

	SyntaxHighlighter.config.tagName = 'code';
	SyntaxHighlighter.defaults.toolbar = false;
	SyntaxHighlighter.defaults.gutter = false;
	SyntaxHighlighter.all();

	// Striped tables
	$('#kodoc-content tbody tr:odd').addClass('alt');

	// Collapsable categories
	$('#kodoc-menu li:has(ul), #kodoc-menu li:has(ol)').each(function()
	{
		var link = $(this).find(':first');
		var menu = $(this).find('ul:first,ol:first');
		
		link.prepend('<div class="toggler"></div>');
		link.addClass('section');
		
		var open  = function()
		{
			menu.slideDown();
			link.addClass('section-active');
		};

		var close = function()
		{
			menu.slideUp();
			link.removeClass('section-active');
		};

		if (menu.find('a[href="'+ window.location.pathname +'"]').length || $(this).find('a[href="'+ window.location.pathname +'"]').length)
		{
			// Currently active menu
			link.find('.toggler:first').toggle(close, open);
			link.addClass('section-active');
		}
		else
		{
			menu.hide();
			link.find('.toggler:first').toggle(open, close);
			link.removeClass('section-active');
		}
	});

	// "Link to" headers
	$('#kodoc-content')
		.find('h1[id],h2[id],h3[id],h4[id],h5[id],h6[id]').each(function(){
			$(this).append('<a href="#' + $(this).attr('id') + '" class="heading-link">Link to this</a>');
		});
	/*
	 
	 I'm not exactly sure what I did that broke this code... but I've replaced it with the above code.  ~bluehawk
	 
	$('#kodoc-content')
		.children('h1[id],h2[id],h3[id],h4[id],h5[id],h6[id]')
		.append(function(index, html){
			return '<a href="#' + $(this).attr('id') + '" class="heading-link">Link to this</a>';
		});
	*/
	
	// Show source links
	$('#kodoc-content .method-source').each(function(){
		$(this).find('h5').each(function(){ $(this).append(' <a class="toggler" href="#">[show]</a>') });
		var link = $(this).find('.toggler');
		var code = $(this).find('pre');
		
		var show = function()
		{
			code.slideDown();
			link.html('[hide]');
		};
		
		var hide = function()
		{
			code.slideUp();
			link.html('[show]');
		};
		
		link.toggle(show,hide);
		
		code.hide();
	});
});
