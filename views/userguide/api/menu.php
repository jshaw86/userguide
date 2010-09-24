
<h2>Modules</h2>
<ol class="menu">
<?php foreach ($menu as $package => $categories): ksort($categories); ?>
<li><span><strong><?php echo $package ?></strong></span>
	<ol>
	<?php foreach ($categories as $category => $classes): sort($classes); ?>
		<?php FB::log($classes); ?>
		<?php if ($category !== 'Base'): ?>
			<li><span><?php echo $category ?></span>
				<ol>
				<?php foreach ($classes as $class): ?>
					<li><?php echo $class ?></li>
				<?php endforeach ?>
				</ol>
			</li>
		<?php else: ?>
		 	<?php foreach ($classes as $class): ?>
				<li><?php echo $class ?></li>
			<?php endforeach ?>
		<?php endif ?>
	<?php endforeach ?>
	</ol>
<?php endforeach ?>
</ol>
