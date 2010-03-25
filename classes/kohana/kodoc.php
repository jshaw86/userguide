<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Kodoc {

	public static function factory($class)
	{
		return new Kodoc_Class($class);
	}

	/**
	 * Creates an html list of all classes sorted by category (or package if no category)
	 *
	 * @return   string   the html for the menu
	 */
	public static function menu()
	{
		$classes = Kodoc::classes();

		foreach ($classes as $class)
		{
			if (isset($classes['kohana_'.$class]))
			{
				// Remove extended classes
				unset($classes['kohana_'.$class]);
			}
		}

		ksort($classes);

		$menu = array();

		$route = Route::get('docs/api');

		foreach ($classes as $class)
		{
			$class = Kodoc::factory($class);

			$link = HTML::anchor($route->uri(array('class' => $class->class->name)), $class->class->name);

			// Find the category, use the package if no category specified
			if (isset($class->tags['category']))
			{
				// Only get the first if there are several
				$category = current($class->tags['category']);
			}
			else if (isset($class->tags['package']))
			{
				// Only get the first if there are several
				$category = current($class->tags['package']);
			}
			else
			{
				$category = "[No Package or Category]";
			}

			// If the category has a /, we need to do some nesting for the sub category
			if (strpos($category,'/'))
			{
				// First, loop through each piece and make sure that array exists
				$path =& $menu;
				foreach (explode('/',$category) as $piece)
				{
					// If this array doesn't exists, create it
					if ( ! isset($path[$piece]))
					{
						$path[$piece] = array('__NAME' => $piece);
					}
					$path =& $path[$piece];
				}
				
				// And finally, add this link to that subcategory
				$path[] = $link;
			}
			else
			{
				// Just add this class to that category
				$menu[$category][] = $link;
			}
		}

		// Return the output of _menu_print()
		ksort($menu);
		return "<h3>API Browser</h3>\n".self::_menu_print($menu);
	}
	
	/**
	 * This method takes the array built by self::menu and turns it into html
	 *
	 * @param   array   an array of categories and/or classes
	 * @return  string  the html
	 */
	protected static function _menu_print($list)
	{
		// Begin the output!
		$output = array('<ol>');

		foreach ($list as $key => $value)
		{
			// If this key is the name for this subcategory, skip it. (This is used for sorting)
			if  ($key === '__NAME')
				continue;
			
			// If $value is an array, than this is a category
			if (is_array($value))
			{
				// Sort the things in this category, according to self::sortcategory
				uasort($value,array(__CLASS__,'sort_category'));
				
				// Add this categories contents to the output
				$output[] = "<li><strong>$key</strong>".self::_menu_print($value).'</li>';
			}
			// Otherwise, this is just a normal element, just print it.
			else
			{
				$output[] = "<li>$value</li>";
			}
		}

		$output[] = '</ol>';

		return implode("\n", $output);
	}
	
	/**
	 * This function is used by self::_menu_print to organize the array, so that categories (arrays) are first then the classes.
	 *
	 */
	public static function sort_category($a,$b)
	{
		// If only one is an array (category), put that one before strings (class)
		if (is_array($a) AND ! is_array($b))
			return -1;
		elseif (! is_array($a) AND is_array($b))
			return 1;
		
		// If they are both arrays, use strcmp on the __Name key
		elseif (is_array($a) AND is_array($b))
			return strcmp($a['__NAME'],$b['__NAME']);
		
		// This means they are both strings, so compare the strings
		else
			return strcmp($a,$b);
	}

	/**
	 * Returns an array of all the classes available, built by listing all files in the classes folder and then trying to create that class.
	 * 
	 * This means any empty class files (as in complety empty) will cause an exception
	 *
	 * @param   array   array of files, obtained using Kohana::list_files
	 * @return   array   an array of all the class names
	 */
	public static function classes(array $list = NULL)
	{
		if ($list === NULL)
		{
			$list = Kohana::list_files('classes');
		}

		$classes = array();

		foreach ($list as $name => $path)
		{
			if (is_array($path))
			{
				$classes += Kodoc::classes($path);
			}
			else
			{
				// Remove "classes/" and the extension
				$class = substr($name, 8, -(strlen(EXT)));

				// Convert slashes to underscores
				$class = str_replace(DIRECTORY_SEPARATOR, '_', strtolower($class));

				$classes[$class] = $class;
			}
		}

		return $classes;
	}

	/**
	 * Get all classes and methods.  Used on index page.
	 *
	 * >  I personally don't the current index page, but this could be useful for namespacing/packaging
	 * >  For example:  class_methods( Kohana::list_files('classes/sprig') ) could make a nice index page for the sprig package in the api browser
	 * >     ~bluehawk
	 *
	 */
	public static function class_methods(array $list = NULL)
	{
		$list = Kodoc::classes($list);

		$classes = array();

		foreach ($list as $class)
		{
			$_class = new ReflectionClass($class);

			if (stripos($_class->name, 'Kohana') === 0)
			{
				// Skip the extension stuff stuff
				continue;
			}

			$methods = array();

			foreach ($_class->getMethods() as $_method)
			{
				$methods[] = $_method->name;
			}

			sort($methods);

			$classes[$_class->name] = $methods;
		}

		return $classes;
	}

	/**
	 * Parse a comment to extract the description and the tags
	 *
	 * @param   string  the comment retreived using ReflectionClass->getDocComment()
	 * @return  array   array(string $description, array $tags)
	 */
	public static function parse($comment)
	{
		// Normalize all new lines to \n
		$comment = str_replace(array("\r\n", "\n"), "\n", $comment);

		// Remove the phpdoc open/close tags and split
		$comment = array_slice(explode("\n", $comment), 1, -1);

		// Tag content
		$tags = array();

		foreach ($comment as $i => $line)
		{
			// Remove all leading whitespace
			$line = preg_replace('/^\s*\* ?/m', '', $line);

			// Search this line for a tag
			if (preg_match('/^@(\S+)(?:\s*(.+))?$/', $line, $matches))
			{
				// This is a tag line
				unset($comment[$i]);

				$name = $matches[1];
				$text = isset($matches[2]) ? $matches[2] : '';

				switch ($name)
				{
					case 'license':
						if (strpos($text, '://') !== FALSE)
						{
							// Convert the lincense into a link
							$text = HTML::anchor($text);
						}
					break;
					case 'copyright':
						if (strpos($text, '(c)') !== FALSE)
						{
							// Convert the copyright sign
							$text = str_replace('(c)', '&copy;', $text);
						}
					break;
					case 'throws':
						if (preg_match('/^(\w+)\W(.*)$/',$text,$matches))
						{
							$text = HTML::anchor(Route::get('docs/api')->uri(array('class' => $matches[1])), $matches[1]).' '.$matches[2];
						}
						else
						{
							$text = HTML::anchor(Route::get('docs/api')->uri(array('class' => $text)), $text);
						}
					break;
					case 'uses':
						if (preg_match('/^([a-z_]+)::([a-z_]+)$/i', $text, $matches))
						{
							// Make a class#method API link
							$text = HTML::anchor(Route::get('docs/api')->uri(array('class' => $matches[1])).'#'.$matches[2], $text);
						}
					break;
					// don't show @access lines, cause they are redundant
					case 'access':
					continue 2;
				}

				// Add the tag
				$tags[$name][] = $text;
			}
			else
			{
				// Overwrite the comment line
				$comment[$i] = (string) $line;
			}
		}

		// Concat the comment lines back to a block of text
		if ($comment = trim(implode("\n", $comment)))
		{
			// Parse the comment with Markdown
			$comment = Markdown($comment);
		}

		return array($comment, $tags);
	}

	/**
	 * Get the source of a function
	 *
	 * @param  string   the filename
	 * @param  int      start line?
	 * @param  int      end line?
	 */
	public static function source($file, $start, $end)
	{
		if ( ! $file)
		{
			return FALSE;
		}

		$file = file($file, FILE_IGNORE_NEW_LINES);

		$file = array_slice($file, $start - 1, $end - $start + 1);

		if (preg_match('/^(\s+)/', $file[0], $matches))
		{
			$padding = strlen($matches[1]);

			foreach ($file as & $line)
			{
				$line = substr($line, $padding);
			}
		}

		return implode("\n", $file);
	}

} // End Kodoc
