<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $l = substr(I18n::$lang, 2) ?>" lang="<?php echo $l ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<title><?php echo $title ?> | Kodoc</title>

<?php foreach ($styles as $style => $media) echo HTML::style($style, array('media' => $media), TRUE), "\n" ?>

<?php foreach ($scripts as $script) echo HTML::script($script, NULL, TRUE), "\n" ?>

</head>
<body>

<div id="topline">
	<ul id="quicklinks">
		<li class="first"><?php echo HTML::anchor('http://kohanaframework.org', '&nbsp;') ?></li>
		<li class="active"><?php echo HTML::anchor('/en/userguide', 'User Guide') ?></li>
		<li><?php echo HTML::anchor('http://forum.kohanaframework.org', 'Forums') ?></li>
		<li><?php echo HTML::anchor('http://dev.kohanaframework.org', 'Development') ?></li>
		<li><?php echo HTML::anchor('http://www.kohanajobs.com', 'Kohana Jobs') ?></li>
	</ul>
</div>

<div id="header">
	<div class="container">
		<div class="translations">
			<?php echo form::open(NULL, array('method' => 'get')) ?>
				<?php echo form::select('lang', $translations, I18n::$lang) ?>
			<?php echo form::close() ?>
		</div>
		<ul class="breadcrumb">
		<?php foreach ($breadcrumb as $link => $title): ?>
			<li><?php echo is_int($link) ? $title : HTML::anchor($link, $title) ?></li>
		<?php endforeach ?>
		</ul>
	</div>
</div>

<div id="content">
	<div class="wrapper">
		<div id="docs" class="container">
			<div id="kodoc-content">
				<?php echo $content ?>
			</div>
		
			<div id="kodoc-menu">
				<?php echo $menu ?>
			</div>
		</div>
	</div>
</div>

<div id="footer">
	<div class="container">
		<div class="span-17 suffix-1">
			<p class="copyright">&copy; 2008-2009 Kohana Team</p>
		</div>
		<div class="span-6 last">
			<p class="powered">Powered by <?php echo HTML::anchor('http://kohanaphp.com/', 'Kohana') ?> v<?php echo Kohana::VERSION ?></p>
		</div>
	</div>
</div>

</body>
</html>
