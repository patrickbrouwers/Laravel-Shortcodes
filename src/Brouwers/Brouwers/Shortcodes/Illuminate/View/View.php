<?php

namespace Brouwers\ShortCodes\Illuminate\View;

use ArrayAccess;
use Closure;
use Illuminate\Support\MessageBag;
use Illuminate\View\View as IlluminateView;
use Illuminate\View\Engines\EngineInterface;
use Brouwers\Shortcodes\Compilers\ShortcodeCompiler;
use Illuminate\Contracts\View\View as ViewContract;

class View extends IlluminateView implements ArrayAccess, ViewContract
{

    /**
     * Short code engine resolver
     *  @var \Brouwers\Shortcodes\Illuminate\View\ShortcodeCompiler
     */
    public $shortcode;

    /**
     * Create a new view instance.
     *
     * @param  \Illuminate\View\Factory  $factory
     * @param  \Illuminate\View\Compilers\EngineInterface  $engine
     * @param  string  $view
     * @param  string  $path
     * @param  array   $data
     * @return void
     */
    public function __construct(Factory $factory, EngineInterface $engine, $view, $path, $data = array(), ShortcodeCompiler $shortcode)
    {
        parent::__construct($factory, $engine, $view, $path, $data);
        $this->shortcode = $shortcode;
    }

    /**
     * Enable the shortcodes
     * @return [type] [description]
     */
    public function withShortcodes()
    {
        $this->shortcode->enable();
        return $this;
    }

    /**
     * Disable the shortcodes
     * @return [type] [description]
     */
    public function withoutShortcodes()
    {
        $this->shortcode->disable();
        return $this;
    }

    /**
     * Get the contents of the view instance.
     *
     * @return string
     */
    protected function renderContents()
    {
        // We will keep track of the amount of views being rendered so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        $this->factory->incrementRender();

        $this->factory->callComposer($this);

        $contents = $this->getContents();

        // compile the shortcodes
        $contents = $this->shortcode->compile($contents);

        // Once we've finished rendering the view, we'll decrement the render count
        // so that each sections get flushed out next time a view is created and
        // no old sections are staying around in the memory of an environment.
        $this->factory->decrementRender();

        return $contents;
    }

}
