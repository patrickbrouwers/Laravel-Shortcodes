<?php

namespace Brouwers\Shortcodes\Illuminate\View;

use Closure;
use Illuminate\Events\Dispatcher;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as IlluminateViewFactory;
use Brouwers\Shortcodes\Compilers\ShortcodeCompiler;

class Factory extends IlluminateViewFactory
{

    /**
     * Short code engine resolver
     *  @var \Brouwers\Shortcodes\Compilers\ShortcodeCompiler
     */
    public $shortcode;

    /**
     * Create a new view factory instance.
     *
     * @param  \Illuminate\View\Compilers\EngineResolver  $engines
     * @param  \Illuminate\View\ViewFinderInterface  $finder
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(EngineResolver $engines, ViewFinderInterface $finder, Dispatcher $events, ShortcodeCompiler $shortcode)
    {
        parent::__construct($engines, $finder, $events);
        $this->shortcode = $shortcode;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array())
    {
        if (isset($this->aliases[$view])) $view = $this->aliases[$view];

        $path = $this->finder->find($view);

        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $this->getEngineFromPath($path), $view, $path, $data, $this->shortcode));

        return $view;
    }
}
