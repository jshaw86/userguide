<div id="class-menu">
	<h3 id="toc"><?php echo $doc->class->name ?></h3>

	<h4>Constants</h4>
	<ul>
	<?php if ($doc->constants): ?>
	<?php foreach ($doc->constants as $name => $value): ?>
		<li><a href="#constant:<?php echo $name ?>"><?php echo $name ?></a></li>
	<?php endforeach ?>
	<?php else: ?>
		<li><em>None</em></li>
	<?php endif ?>
	</ul>
	
	
	<h4>Properties</h4>
	<ul>
	<?php if ($properties = $doc->properties()): ?>
	<?php foreach ($properties as $prop): ?>
		<li><a href="#property:<?php echo $prop->property->name ?>">$<?php echo $prop->property->name ?></a></li>
	<?php endforeach ?>
	<?php else: ?>
		<li><em>None</em></li>
	<?php endif ?>
	</ul>
	
	<h4>Methods</h4>
	<ul>
	<?php if ($methods = $doc->methods()): ?>
	<?php foreach ($methods as $method): ?>
		<li><a href="#<?php echo $method->method->name ?>"><?php echo $method->method->name ?>()</a></li>
	<?php endforeach ?>
	<?php else: ?>
		<li><em>None</em></li>
	<?php endif ?>
	</ul>
</div>