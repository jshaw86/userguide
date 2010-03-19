<h1>Kodoc</h1>

<p>The following modules have userguide pages:</p>

<?php if( ! empty($modules)): ?>

	<?php foreach($modules as $url => $module): ?>
	
		<p>
			<strong><?php echo html::anchor($url,$module['name']) ?></strong> - 
			<?php echo $module['desc'] ?>
		</p>
	
	<?php endforeach; ?>
	
<?php else: ?>

	<p class="error">I couldn't find any modules with userguide pages.</p>

<?php endif; ?>
	