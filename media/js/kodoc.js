$(document).ready(function()
{
	// Translation selector
	$('#topbar form select').change(function()
	{
		$(this).parents('form').submit();
	});

	// Striped tables
	$('#kodoc-content tbody tr:even').addClass('alt');

	// Toggle sub menus
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

	// Collapsable class contents
	$('#kodoc-content #toc').each(function()
	{
		var header  = $(this);
		var content = $('#kodoc-content div.toc').hide();

		$('<span class="toggle">[ + ]</span>').toggle(function()
		{
			$(this).html('[ &ndash; ]');
			content.stop().slideDown();
		},
		function()
		{
			$(this).html('[ + ]');
			content.stop().slideUp();
		})
		.appendTo(header);
	});
	
	// "Link to" headers
	$('#kodoc-content')
		.children('h1[id],h2[id],h3[id],h4[id],h5[id],h6[id]')
		.append(function(index, html){
			return '<a href="#' + $(this).attr('id') + '" class="heading-link">Link to this</a>';
		});
});
