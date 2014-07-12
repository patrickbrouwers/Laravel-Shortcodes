<?php namespace Brouwers\Shortcodes;

use Illuminate\Support\ServiceProvider;
use Brouwers\Shortcodes\Illuminate\View\Factory;
use Brouwers\Shortcodes\Compilers\ShortcodeCompiler;
use Brouwers\Shortcodes\Engines\ShortcodeEngineResolver;
use Brouwers\Shortcodes\Engines\ShortcodeCompilerEngine;

class ShortcodesServiceProvider extends ServiceProvider {

	/**
	 * Boot the package
	 * @return [type] [description]
	 */
	public function boot()
	{
		$this->package('brouwers/shortcodes');
		$this->enableCompiler();
	}

	/**
	 * Enable the compiler
	 * @return [type] [description]
	 */
	public function enableCompiler()
	{
		// Check if the compiler is auto enabled
		$state = $this->app['config']->get('shortcodes::enabled', false);

		// enable when needed
		if($state)
			$this->app['shortcode.compiler']->enable();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerShortcodeCompiler();
		$this->registerShortcode();
		$this->registerView();
	}

	/**
	 * Register short code compiler
	 * @return [type] [description]
	 */
	public function registerShortcodeCompiler()
	{
		$this->app->bindShared('shortcode.compiler', function($app)
		{
			return new ShortcodeCompiler();
		});
	}

	/**
	 * Register the shortcode
	 * @return [type] [description]
	 */
	public function registerShortcode()
	{
		$this->app->bindShared('shortcode', function($app) {
			return new Shortcode($app['shortcode.compiler']);
		});
	}

	/**
	 * Register Laravel view
	 * @return [type] [description]
	 */
	public function registerView()
	{
		$this->app->bindShared('view', function($app)
		{
			// Next we need to grab the engine resolver instance that will be used by the
			// environment. The resolver will be used by an environment to get each of
			// the various engine implementations such as plain PHP or Blade engine.
			$resolver = $app['view.engine.resolver'];

			$finder = $app['view.finder'];

			$env = new Factory($resolver, $finder, $app['events'], $app['shortcode.compiler']);

			// We will also set the container instance on this view environment since the
			// view composers may be classes registered in the container, which allows
			// for great testable, flexible composers for the application developer.
			$env->setContainer($app);

			$env->share('app', $app);

			return $env;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'shortcode',
			'shortcode.compiler',
			'view'
		);
	}
}