# Profiling

Kohana provides a very simple way to display statistics about your application:

1. Common [Kohana] method calls
2. Requests
3. [Database] queries
4. Average execution times for your application

## Example

You can display or collect the current [profiler] statistics at any time:

~~~
<div id="kohana-profiler">
<?php echo View::factory('profiler/stats') ?>
</div>
~~~

## Preview

{{profiler/stats}}

## Create your own benchmark

To see how long certain areas of your application are taking, you can add your own benchmark to the profiler.  See the [Profiler](../api/Profiler) class for more info.

~~~
if (Kohana::$profiling === TRUE)
{
	// Start a new benchmark
	$benchmark = Profiler::start('Category','benchmark name');
}

// Do some stuff

if (isset($benchmark))
{
	// Stop the benchmark
	Profiler::stop($benchmark);
}
~~~