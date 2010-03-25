<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Kodoc {

	public static function factory($class)
	{
		return new Kodoc($class);
	}

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
		return self::_menu_print($menu);
	}
	
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
				uasort($value,array(__CLASS__,'sortcategory'));
				
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
	
	public static function sortcategory($a,$b)
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
						$text = HTML::anchor(Route::get('docs/api')->uri(array('class' => $text)), $text);
					break;
					case 'uses':
						if (preg_match('/^([a-z_]+)::([a-z_]+)$/i', $text, $matches))
						{
							// Make a class#method API link
							$text = HTML::anchor(Route::get('docs/api')->uri(array('class' => $matches[1])).'#'.$matches[2], $text);
						}
					break;
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

	public $class;

	public $modifiers;

	public $description;

	public $tags = array();

	public $constants = array();

	public function __construct($class)
	{
		$this->class = $parent = new ReflectionClass($class);

		if ($modifiers = $this->class->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		if ($constants = $this->class->getConstants())
		{
			foreach ($constants as $name => $value)
			{
				$this->constants[$name] = Kohana::debug($value);
			}
		}

		do
		{
			if ($comment = $parent->getDocComment())
			{
				// Found a description for this class
				break;
			}
		}
		while ($parent = $parent->getParentClass());

		list($this->description, $this->tags) = Kodoc::parse($comment);
	}

	public function properties()
	{
		$props = $this->class->getProperties();

		sort($props);

		foreach ($props as $key => $property)
		{
			if ($property->isPublic())
			{
				$props[$key] = new Kodoc_Property($this->class->name, $property->name);
			}
			else
			{
				unset($props[$key]);
			}
		}

		return $props;
	}

	public function methods()
	{
		$methods = $this->class->getMethods();

		sort($methods);

		foreach ($methods as $key => $method)
		{
			$methods[$key] = new Kodoc_Method($this->class->name, $method->name);
		}

		return $methods;
	}

} // End Kodoc
