<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/plugins/PicoThemeApi2CompatPlugin.php>
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
 * Maintains backward compatibility with themes using API version 2, written
 * for Pico 2.0
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
class PicoThemeApi2CompatPlugin extends AbstractPicoCompatPlugin
{
    /**
     * Manually configured Twig escape strategy
     *
     * @var mixed|null
     */
    protected $twigEscapeStrategy;

    /**
     * Directory paths of plugins
     *
     * @var string[]
     */
    protected $pluginPaths = array();

    /**
     * Sets PicoThemeApi2CompatPlugin::$twigEscapeStrategy
     *
     * @see PicoThemeApi2CompatPlugin::$twigEscapeStrategy
     *
     * @param array &$config array of config variables
     */
    public function onConfigLoaded(array &$config)
    {
        if (isset($config['twig_config']['autoescape'])) {
            $this->twigEscapeStrategy = $config['twig_config']['autoescape'];
        }
    }

    /**
     * Re-introduces the Twig variables prev_page, base_dir and theme_dir
     *
     * @param string &$templateName  file name of the template
     * @param array  &$twigVariables template variables
     */
    public function onPageRendering(&$templateName, array &$twigVariables)
    {
        $twigVariables['prev_page'] = &$twigVariables['previous_page'];
        $twigVariables['base_dir'] = rtrim($this->getPico()->getRootDir(), '/');
        $twigVariables['theme_dir'] = $this->getPico()->getThemesDir() . $this->getPico()->getTheme();
    }

    /**
     * Registers PicoPluginApi2CompatPlugin::twigEscapeStrategy() as Twig's
     * default escape strategy
     *
     * @see PicoPluginApi2CompatPlugin::twigEscapeStrategy()
     *
     * @param Twig_Environment &$twig Twig instance
     */
    public function onTwigRegistered(Twig_Environment &$twig)
    {
        if ($twig->hasExtension('Twig_Extension_Escaper')) {
            /** @var Twig_Extension_Escaper $escaperExtension */
            $escaperExtension = $twig->getExtension('Twig_Extension_Escaper');
            $escaperExtension->setDefaultStrategy(array($this, 'twigEscapeStrategy'));
        }
    }

    /**
     * Returns Twig's default escaping strategy for the given template
     *
     * This escape strategy takes a template name and decides whether Twig's
     * global default escape strategy should be used, or escaping should be
     * disabled. Escaping is disabled for themes using API v2 and below as well
     * as for templates of plugins using API v2 and below. If a escape strategy
     * has been configured manually, this method always returns this explicitly
     * configured escape strategy.
     *
     * @param string $templateName template name
     *
     * @return string|false escape strategy for this template
     */
    public function twigEscapeStrategy($templateName)
    {
        $twigConfig = $this->getPico()->getConfig('twig_config');
        $escapeStrategy = $twigConfig['autoescape'];

        if (($this->twigEscapeStrategy !== null) && ($escapeStrategy === $this->twigEscapeStrategy)) {
            return $escapeStrategy;
        }

        if (!is_string($escapeStrategy) && ($escapeStrategy !== false)) {
            $escapeStrategy = call_user_func($escapeStrategy, $templateName);
        }

        if ($escapeStrategy === false) {
            return false;
        }

        /** @var Twig_SourceContextLoaderInterface $twigLoader */
        $twigLoader = $this->getPico()->getTwig()->getLoader();
        if (!$twigLoader instanceof Twig_SourceContextLoaderInterface) {
            throw new RuntimeException(
                "PicoDeprecated compat plugin '" . __CLASS__ . "' requires a 'Twig_SourceContextLoaderInterface' "
                . "Twig loader, '" . get_class($twigLoader) . "' given"
            );
        }

        try {
            $templatePath = $twigLoader->getSourceContext($templateName)->getPath();
        } catch (\Twig\Error\LoaderError $e) {
            $templatePath = '';
        }

        if ($templatePath) {
            $themePath = realpath($this->getPico()->getThemesDir() . $this->getPico()->getTheme()) . '/';
            if (substr($templatePath, 0, strlen($themePath)) === $themePath) {
                $themeApiVersion = $this->getPico()->getThemeApiVersion();
                return ($themeApiVersion >= PicoDeprecated::API_VERSION_3) ? $escapeStrategy : false;
            }

            $plugin = $this->getPluginFromPath($templatePath);
            if ($plugin) {
                $pluginApiVersion = $this->getPicoDeprecated()->getPluginApiVersion($plugin);
                return ($pluginApiVersion >= PicoDeprecated::API_VERSION_3) ? $escapeStrategy : false;
            }
        }

        // unknown template path
        // to preserve BC we must assume that the template uses an old API version
        return false;
    }

    /**
     * Returns the matching plugin instance when the given path is within a
     * plugin's base directory
     *
     * @param string $path file path to search for
     *
     * @return object|null either the matching plugin instance or NULL
     */
    protected function getPluginFromPath($path)
    {
        $plugins = $this->getPico()->getPlugins();
        foreach ($this->pluginPaths as $pluginName => $pluginPath) {
            if ($pluginPath && (substr($path, 0, strlen($pluginPath)) === $pluginPath)) {
                return $plugins[$pluginName];
            }
        }

        $rootDir = realpath($this->getPico()->getRootDir()) . '/';
        $vendorDir = realpath($this->getPico()->getVendorDir()) . '/';
        $pluginsDir = realpath($this->getPico()->getPluginsDir()) . '/';
        $themesDir = realpath($this->getPico()->getThemesDir()) . '/';
        foreach ($plugins as $pluginName => $plugin) {
            if (isset($this->pluginPaths[$pluginName])) {
                continue;
            }

            $pluginReflector = new ReflectionObject($plugin);

            $pluginPath = dirname($pluginReflector->getFileName() ?: '') . '/';
            if (in_array($pluginPath, array('/', $rootDir, $vendorDir, $pluginsDir, $themesDir), true)) {
                $pluginPath = '';
            }

            $this->pluginPaths[$pluginName] = $pluginPath;

            if ($pluginPath && (substr($path, 0, strlen($pluginPath)) === $pluginPath)) {
                return $plugins[$pluginName];
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion()
    {
        return PicoDeprecated::API_VERSION_3;
    }
}
