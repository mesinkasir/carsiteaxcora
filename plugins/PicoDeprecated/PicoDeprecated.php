<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/PicoDeprecated.php>
 *
 * The file was previously part of the project's main repository; the version
 * control history of the original file applies accordingly, available from
 * the following original location:
 *
 * <https://github.com/picocms/Pico/blob/82a342ba445122182b898a2c1800f03c8d16f18c/plugins/00-PicoDeprecated.php>
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * Maintain backward compatibility to older Pico releases
 *
 * `PicoDeprecated`'s purpose is to maintain backward compatibility to older
 * versions of Pico, by re-introducing characteristics that were removed from
 * Pico's core.
 *
 * `PicoDeprecated` is basically a mandatory plugin for all Pico installs.
 * Without this plugin you can't use plugins which were written for other
 * API versions than the one of Pico's core, even when there was just the
 * slightest change.
 *
 * {@see http://picocms.org/plugins/deprecated/} for a full list of features.
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
class PicoDeprecated extends AbstractPicoPlugin
{
    /**
     * API version used by this plugin
     *
     * @var int
     */
    const API_VERSION = 3;

    /**
     * API version 0, used by Pico 0.9 and earlier
     *
     * @var int
     */
    const API_VERSION_0 = 0;

    /**
     * API version 1, used by Pico 1.0
     *
     * @var int
     */
    const API_VERSION_1 = 1;

    /**
     * API version 2, used by Pico 2.0
     *
     * @var int
     */
    const API_VERSION_2 = 2;

    /**
     * API version 3, used by Pico 2.1
     *
     * @var int
     */
    const API_VERSION_3 = 3;

    /**
     * Loaded plugins, indexed by API version
     *
     * @see PicoDeprecated::getPlugins()
     *
     * @var object[]
     */
    protected $plugins = array();

    /**
     * Loaded compatibility plugins
     *
     * @see PicoDeprecated::getCompatPlugins()
     *
     * @var PicoCompatPluginInterface[]
     */
    protected $compatPlugins = array();

    /**
     * {@inheritDoc}
     */
    public function __construct(Pico $pico)
    {
        parent::__construct($pico);

        if (is_file(__DIR__ . '/vendor/autoload.php')) {
            require(__DIR__ . '/vendor/autoload.php');
        }

        if (!class_exists('PicoMainCompatPlugin')) {
            die(
                "Cannot find PicoDeprecated's 'vendor/autoload.php'. If you're using a composer-based Pico install, "
                . "run `composer update`. If you're rather trying to use one of PicoDeprecated's pre-built release "
                . "packages, make sure to download PicoDeprecated's release package matching Pico's version named "
                . "'pico-deprecated-release-v*.tar.gz' (don't download a source code package)."
            );
        }

        if ($pico::API_VERSION !== static::API_VERSION) {
            throw new RuntimeException(
                'PicoDeprecated requires API version ' . static::API_VERSION . ', '
                . 'but Pico is running API version ' . $pico::API_VERSION
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handleEvent($eventName, array $params)
    {
        parent::handleEvent($eventName, $params);

        // trigger events on compatibility plugins
        if ($this->isEnabled() || ($eventName === 'onPluginsLoaded')) {
            $isCoreEvent = in_array($eventName, $this->getCoreEvents());
            foreach ($this->compatPlugins as $plugin) {
                if ($isCoreEvent) {
                    if ($plugin->getApiVersion() === static::API_VERSION) {
                        $plugin->handleEvent($eventName, $params);
                    }
                } elseif ($plugin instanceof PicoPluginApiCompatPluginInterface) {
                    $plugin->handleCustomEvent($eventName, $params);
                }
            }
        }
    }

    /**
     * Reads all loaded plugins and indexes them by API level, loads the
     * necessary compatibility plugins
     *
     * @see PicoDeprecated::loadPlugin()
     *
     * @param object[] $plugins loaded plugin instances
     */
    public function onPluginsLoaded(array $plugins)
    {
        $this->loadCompatPlugin('PicoMainCompatPlugin');

        foreach ($plugins as $plugin) {
            $this->loadPlugin($plugin);
        }

        $this->getPico()->triggerEvent('onPicoDeprecated', array($this));
    }

    /**
     * Adds a manually loaded plugin to PicoDeprecated's plugin index, loads
     * the necessary compatibility plugins
     *
     * @see PicoDeprecated::loadPlugin()
     *
     * @param object $plugin loaded plugin instance
     */
    public function onPluginManuallyLoaded($plugin)
    {
        $this->loadPlugin($plugin);
    }

    /**
     * Loads a compatibility plugin if Pico's theme uses a old theme API
     *
     * @param string $theme           name of current theme
     * @param int    $themeApiVersion API version of the theme
     * @param array  $themeConfig     config array of the theme
     */
    public function onThemeLoaded($theme, $themeApiVersion, array &$themeConfig)
    {
        $this->loadThemeApiCompatPlugin($themeApiVersion);
    }

    /**
     * Adds a plugin to PicoDeprecated's plugin index
     *
     * @see PicoDeprecated::onPluginsLoaded()
     * @see PicoDeprecated::onPluginManuallyLoaded()
     * @see PicoDeprecated::getPlugins()
     *
     * @param object $plugin loaded plugin instance
     */
    protected function loadPlugin($plugin)
    {
        $pluginName = get_class($plugin);

        $apiVersion = $this->getPluginApiVersion($plugin);
        if (!isset($this->plugins[$apiVersion])) {
            $this->plugins[$apiVersion] = array();
            $this->loadPluginApiCompatPlugin($apiVersion);
        }

        $this->plugins[$apiVersion][$pluginName] = $plugin;
    }

    /**
     * Returns a list of all loaded Pico plugins using the given API level
     *
     * @param int $apiVersion API version to match plugins
     *
     * @return object[] loaded plugin instances
     */
    public function getPlugins($apiVersion)
    {
        return isset($this->plugins[$apiVersion]) ? $this->plugins[$apiVersion] : array();
    }

    /**
     * Loads a compatibility plugin
     *
     * @param PicoCompatPluginInterface|string $plugin either the class name of
     *     a plugin to instantiate or a plugin instance
     *
     * @return PicoCompatPluginInterface instance of the loaded plugin
     */
    public function loadCompatPlugin($plugin)
    {
        if (!is_object($plugin)) {
            $className = (string) $plugin;
            if (class_exists($className)) {
                $plugin = new $className($this->getPico(), $this);
            } else {
                throw new RuntimeException(
                    "Unable to load PicoDeprecated compatibility plugin '" . $className . "': Class not found"
                );
            }
        }

        $className = get_class($plugin);
        if (isset($this->compatPlugins[$className])) {
            return $this->compatPlugins[$className];
        }

        if (!($plugin instanceof PicoCompatPluginInterface)) {
            throw new RuntimeException(
                "Unable to load PicoDeprecated compatibility plugin '" . $className . "': "
                . "Compatibility plugins must implement 'PicoCompatPluginInterface'"
            );
        }

        $apiVersion = $plugin->getApiVersion();
        $this->loadPluginApiCompatPlugin($apiVersion);

        $dependsOn = $plugin->getDependencies();
        foreach ($dependsOn as $pluginDependency) {
            $this->loadCompatPlugin($pluginDependency);
        }

        $this->compatPlugins[$className] = $plugin;

        return $plugin;
    }

    /**
     * Loads a plugin API compatibility plugin
     *
     * @param int $apiVersion API version to load the compatibility plugin for
     */
    protected function loadPluginApiCompatPlugin($apiVersion)
    {
        if ($apiVersion !== static::API_VERSION) {
            $this->loadCompatPlugin('PicoPluginApi' . $apiVersion . 'CompatPlugin');
        }
    }

    /**
     * Loads a theme API compatibility plugin
     *
     * @param int $apiVersion API version to load the compatibility plugin for
     */
    protected function loadThemeApiCompatPlugin($apiVersion)
    {
        if ($apiVersion !== static::API_VERSION) {
            $this->loadCompatPlugin('PicoThemeApi' . $apiVersion . 'CompatPlugin');
        }
    }

    /**
     * Returns all loaded compatibility plugins
     *
     * @return PicoCompatPluginInterface[] list of loaded compatibility plugins
     */
    public function getCompatPlugins()
    {
        return $this->compatPlugins;
    }

    /**
     * Triggers deprecated events on plugins of different API versions
     *
     * You can use this public method in other plugins to trigger custom events
     * on plugins using a particular API version. If you want to trigger a
     * custom event on all plugins, no matter their API version (except for
     * plugins using API v0, which can't handle custom events), use
     * {@see Pico::triggerEvent()} instead.
     *
     * @see Pico::triggerEvent()
     *
     * @param int    $apiVersion API version of the event
     * @param string $eventName  event to trigger
     * @param array  $params     optional parameters to pass
     */
    public function triggerEvent($apiVersion, $eventName, array $params = array())
    {
        foreach ($this->getPlugins($apiVersion) as $plugin) {
            $plugin->handleEvent($eventName, $params);
        }
    }

    /**
     * Returns the API version of a given plugin
     *
     * @param object $plugin plugin instance
     *
     * @return int API version used by the plugin
     */
    public function getPluginApiVersion($plugin)
    {
        $pluginApiVersion = self::API_VERSION_0;
        if ($plugin instanceof PicoPluginInterface) {
            $pluginApiVersion = self::API_VERSION_1;
            if (defined(get_class($plugin) . '::API_VERSION')) {
                $pluginApiVersion = $plugin::API_VERSION;
            }
        }

        return $pluginApiVersion;
    }

    /**
     * Returns a list of the names of Pico's core events
     *
     * @return string[] list of Pico's core events
     */
    public function getCoreEvents()
    {
        return array(
            'onPluginsLoaded',
            'onPluginManuallyLoaded',
            'onConfigLoaded',
            'onThemeLoading',
            'onThemeLoaded',
            'onRequestUrl',
            'onRequestFile',
            'onContentLoading',
            'on404ContentLoading',
            'on404ContentLoaded',
            'onContentLoaded',
            'onMetaParsing',
            'onMetaParsed',
            'onContentParsing',
            'onContentPrepared',
            'onContentParsed',
            'onPagesLoading',
            'onSinglePageLoading',
            'onSinglePageContent',
            'onSinglePageLoaded',
            'onPagesDiscovered',
            'onPagesLoaded',
            'onCurrentPageDiscovered',
            'onPageTreeBuilt',
            'onPageRendering',
            'onPageRendered',
            'onMetaHeaders',
            'onYamlParserRegistered',
            'onParsedownRegistered',
            'onTwigRegistered'
        );
    }
}
