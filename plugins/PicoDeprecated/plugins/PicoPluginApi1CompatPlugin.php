<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/plugins/PicoPluginApi1CompatPlugin.php>
 *
 * This file was created by splitting up an original file into multiple files,
 * which in turn was previously part of the project's main repository. The
 * version control history of these files apply accordingly, available from
 * the following original locations:
 *
 * <https://github.com/picocms/pico-deprecated/blob/90ea3d5a9767f1511f165e051dd7ffb8f1b3f92e/PicoDeprecated.php>
 * <https://github.com/picocms/Pico/blob/82a342ba445122182b898a2c1800f03c8d16f18c/plugins/00-PicoDeprecated.php>
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * Maintains backward compatibility with plugins using API version 1, written
 * for Pico 1.0
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
class PicoPluginApi1CompatPlugin extends AbstractPicoPluginApiCompatPlugin
{
    /**
     * This plugin extends {@see PicoPluginApi2CompatPlugin} and
     * {@see PicoThemeApi1CompatPlugin}
     *
     * @var string[]
     */
    protected $dependsOn = array('PicoPluginApi2CompatPlugin', 'PicoThemeApi1CompatPlugin');

    /**
     * Map of core events matching event signatures of older API versions
     *
     * @see AbstractPicoPluginApiCompatPlugin::handleEvent()
     *
     * @var array<string,string>
     */
    protected $eventAliases = array(
        'onConfigLoaded'      => array('onConfigLoaded'),
        'onRequestUrl'        => array('onRequestUrl'),
        'onRequestFile'       => array('onRequestFile'),
        'on404ContentLoaded'  => array('on404ContentLoaded'),
        'onContentLoaded'     => array('onContentLoaded'),
        'onContentPrepared'   => array('onContentPrepared'),
        'onContentParsed'     => array('onContentParsed'),
        'onPagesLoading'      => array('onPagesLoading'),
        'onSinglePageLoaded'  => array('onSinglePageLoaded'),
        'onPageRendered'      => array('onPageRendered')
    );

    /**
     * Pico's request file
     *
     * @see Pico::$requestFile
     * @see PicoPluginApi1CompatPlugin::onRequestFile()
     *
     * @var string|null
     */
    protected $requestFile;

    /**
     * Pico's raw contents
     *
     * @see Pico::$rawContent
     * @see PicoPluginApi1CompatPlugin::onContentLoaded()
     *
     * @var string|null
     */
    protected $rawContent;

    /**
     * Pico's meta headers array
     *
     * @see Pico::$metaHeaders
     * @see PicoPluginApi1CompatPlugin::onMetaHeaders()
     *
     * @var array<string,string>|null
     */
    protected $metaHeaders;

    /**
     * Pico's pages array
     *
     * @see Pico::$pages
     * @see PicoPluginApi1CompatPlugin::onPagesLoaded()
     *
     * @var array[]|null
     */
    protected $pages;

    /**
     * Pico's Twig instance
     *
     * @see Pico::$twig
     * @see PicoPluginApi1CompatPlugin::onTwigRegistered()
     *
     * @var Twig_Environment|null
     */
    protected $twig;

    /**
     * Triggers the onPluginsLoaded event
     *
     * Prior to API v2 the event `onPluginsLoaded` passed the `$plugins` array
     * by reference. This is no longer the case. We still pass the parameter by
     * reference and use {@see Pico::loadPlugin()} to load additional plugins,
     * however, unloading or replacing plugins was removed without a
     * replacement. This might be a BC-breaking change for you!
     *
     * @param object[] $plugins loaded plugin instances
     */
    public function onPluginsLoaded(array $plugins)
    {
        $originalPlugins = $plugins;

        $this->triggerEvent('onPluginsLoaded', array(&$plugins));

        foreach ($plugins as $pluginName => $plugin) {
            if (!isset($originalPlugins[$pluginName])) {
                $this->getPico()->loadPlugin($plugin);
            } elseif ($plugin !== $originalPlugins[$pluginName]) {
                throw new RuntimeException(
                    "A Pico plugin using API version 1 tried to replace Pico plugin '" . $pluginName . "' using the "
                    . "onPluginsLoaded() event, however, replacing plugins was removed with API version 2"
                );
            }

            unset($originalPlugins[$pluginName]);
        }

        if ($originalPlugins) {
            $removedPluginsList = implode("', '", array_keys($originalPlugins));
            throw new RuntimeException(
                "A Pico plugin using API version 1 tried to unload the Pico plugin(s) '" . $removedPluginsList . "' "
                . "using the onPluginsLoaded() event, however, unloading plugins was removed with API version 2"
            );
        }
    }

    /**
     * Sets PicoPluginApi1CompatPlugin::$requestFile
     *
     * @see PicoPluginApi1CompatPlugin::$requestFile
     *
     * @param string &$file absolute path to the content file to serve
     */
    public function onRequestFile(&$file)
    {
        $this->requestFile = &$file;
    }

    /**
     * Triggers the onContentLoading event
     */
    public function onContentLoading()
    {
        $this->triggerEvent('onContentLoading', array(&$this->requestFile));
    }

    /**
     * Sets PicoPluginApi1CompatPlugin::$rawContent
     *
     * @see PicoPluginApi1CompatPlugin::$rawContent
     *
     * @param string &$rawContent raw file contents
     */
    public function onContentLoaded(&$rawContent)
    {
        $this->rawContent = &$rawContent;
    }

    /**
     * Triggers the on404ContentLoading event
     */
    public function on404ContentLoading()
    {
        $this->triggerEvent('on404ContentLoading', array(&$this->requestFile));
    }

    /**
     * Triggers the onMetaParsing event
     *
     * @see PicoPluginApi1CompatPlugin::onMetaHeaders()
     */
    public function onMetaParsing()
    {
        $headersFlipped = $this->getFlippedMetaHeaders();
        $this->triggerEvent('onMetaParsing', array(&$this->rawContent, &$headersFlipped));
        $this->updateFlippedMetaHeaders($headersFlipped);
    }

    /**
     * Triggers the onMetaParsed and onParsedownRegistration events
     *
     * @param string[] &$meta parsed meta data
     */
    public function onMetaParsed(array &$meta)
    {
        $this->triggerEvent('onMetaParsed', array(&$meta));
        $this->triggerEvent('onParsedownRegistration');
    }

    /**
     * Triggers the onContentParsing event
     */
    public function onContentParsing()
    {
        $this->triggerEvent('onContentParsing', array(&$this->rawContent));
    }

    /**
     * Sets PicoPluginApi1CompatPlugin::$pages
     *
     * @see PicoPluginApi1CompatPlugin::$pages
     *
     * @param array[] &$pages sorted list of all known pages
     */
    public function onPagesLoaded(array &$pages)
    {
        $this->pages = &$pages;
    }

    /**
     * Triggers the onPagesLoaded and onTwigRegistration events
     *
     * @param array|null &$currentPage  data of the page being served
     * @param array|null &$previousPage data of the previous page
     * @param array|null &$nextPage     data of the next page
     */
    public function onCurrentPageDiscovered(
        array &$currentPage = null,
        array &$previousPage = null,
        array &$nextPage = null
    ) {
        $this->triggerEvent('onPagesLoaded', array(&$this->pages, &$currentPage, &$previousPage, &$nextPage));

        $this->triggerEvent('onTwigRegistration');
        $this->getPico()->getTwig();
    }

    /**
     * Triggers the onPageRendering event
     *
     * @param string &$templateName  file name of the template
     * @param array  &$twigVariables template variables
     */
    public function onPageRendering(&$templateName, array &$twigVariables)
    {
        $this->triggerEvent('onPageRendering', array(&$this->twig, &$twigVariables, &$templateName));
    }

    /**
     * Triggers the onMetaHeaders event with flipped meta headers and sets
     * PicoPluginApi1CompatPlugin::$metaHeaders
     *
     * @see PicoPluginApi1CompatPlugin::$metaHeaders
     *
     * @param string[] &$headers list of known meta header fields; the array
     *     key specifies the YAML key to search for, the array value is later
     *     used to access the found value
     */
    public function onMetaHeaders(array &$headers)
    {
        $this->metaHeaders = &$headers;

        $headersFlipped = $this->getFlippedMetaHeaders();
        $this->triggerEvent('onMetaHeaders', array(&$headersFlipped));
        $this->updateFlippedMetaHeaders($headersFlipped);
    }

    /**
     * Sets PicoPluginApi1CompatPlugin::$twig
     *
     * @see PicoPluginApi1CompatPlugin::$twig
     *
     * @param Twig_Environment &$twig Twig instance
     */
    public function onTwigRegistered(Twig_Environment &$twig)
    {
        $this->twig = $twig;
    }

    /**
     * Returns the flipped meta headers array
     *
     * Pico 1.0 and earlier were using the values of the meta headers array to
     * match registered meta headers in a page's meta data, and used the keys
     * of the meta headers array to store the meta value in the page's meta
     * data. However, starting with Pico 2.0 it is the other way round. This
     * allows us to specify multiple "search strings" for a single registered
     * meta value (e.g. "Nyan Cat" and "Tac Nayn" can be synonmous).
     *
     * @return array flipped meta headers
     */
    protected function getFlippedMetaHeaders()
    {
        if ($this->metaHeaders === null) {
            // make sure to trigger the onMetaHeaders event
            $this->getPico()->getMetaHeaders();
        }

        return array_flip($this->metaHeaders ?: array());
    }

    /**
     * Syncs PicoPluginApi1CompatPlugin::$metaHeaders with a flipped headers array
     *
     * @param array $headersFlipped flipped headers array
     */
    protected function updateFlippedMetaHeaders(array $headersFlipped)
    {
        foreach ($this->metaHeaders as $name => $key) {
            if (!isset($headersFlipped[$key])) {
                unset($this->metaHeaders[$name]);
            }
        }

        foreach ($headersFlipped as $key => $name) {
            $this->metaHeaders[$name] = $key;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion()
    {
        return PicoDeprecated::API_VERSION_2;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersionSupport()
    {
        return PicoDeprecated::API_VERSION_1;
    }
}
