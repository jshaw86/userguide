<h3>API Reference</h3>
<ul>
	<li><?php echo html::anchor(Route::get('docs/api')->uri(),'API Reference') ?></li>
</ul>
<h3>Modules</h3>
<?php if( ! empty($modules)): ?>

	<ul>
	<?php foreach($modules as $url => $module): ?>
	
		<li><?php echo html::anchor(Route::get('docs/guide')->uri().'/'.$url,$module['name']) ?></li>
	
	<?php endforeach; ?>
	</ul>
	
<?php else: ?>

	<p class="error">I couldn't find any modules with userguide pages.</p>

<?php endif; ?>