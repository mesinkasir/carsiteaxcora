<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/plugins/PicoMainCompatPlugin.php>
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
 * Maintains backward compatibility with older Pico versions
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
class PicoMainCompatPlugin extends AbstractPicoCompatPlugin
{
    /**
     * Load's config.php from Pico's root and config dir
     *
     * Since we want to utilize Pico's own code dealing with particular config
     * settings (like making paths and URLs absolute), we must call this before
     * {@see Pico::loadConfig()}. `onConfigLoaded` is triggered later, thus we
     * use the `onPluginsLoaded` event.
     *
     * @see PicoMainCompatPlugin::loadScriptedConfig()
     *
     * @param object[] $plugins loaded plugin instances
     */
    public function onPluginsLoaded(array $plugins)
    {
        // deprecated since Pico 1.0
        if (is_file($this->getPico()->getRootDir() . 'config.php')) {
            $this->loadScriptedConfig($this->getPico()->getRootDir() . 'config.php');
        }

        // deprecated since Pico 2.0
        if (is_file($this->getPico()->getConfigDir() . 'config.php')) {
            $this->loadScriptedConfig($this->getPico()->getConfigDir() . 'config.php');
        }
    }

    /**
     * Reads a Pico PHP config file and injects the config into Pico
     *
     * This method injects the config into Pico using PHP's Reflection API
     * (i.e. {@see ReflectionClass}). Even though the Reflection API was
     * created to aid development and not to do things like this, it's the best
     * solution. Otherwise we'd have to copy all of Pico's code dealing with
     * special config settings (like making paths and URLs absolute).
     *
     * @see PicoMainCompatPlugin::onConfigLoaded()
     * @see Pico::loadConfig()
     *
     * @param string $configFile path to the config file to load
     */
    protected function loadScriptedConfig($configFile)
    {
        // scope isolated require()
        $includeConfigClosure = function ($configFile) {
            require($configFile);
            return (isset($config) && is_array($config)) ? $config : array();
        };
        if (PHP_VERSION_ID >= 50400) {
            $includeConfigClosure = $includeConfigClosure->bindTo(null);
        }

        $scriptedConfig = $includeConfigClosure($configFile);

        if (!empty($scriptedConfig)) {
            $picoReflector = new ReflectionObject($this->getPico());
            $picoConfigReflector = $picoReflector->getProperty('config');
            $picoConfigReflector->setAccessible(true);

            $config = $picoConfigReflector->getValue($this->getPico()) ?: array();
            $config += $scriptedConfig;

            $picoConfigReflector->setValue($this->getPico(), $config);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion()
    {
        return PicoDeprecated::API_VERSION_3;
    }
}
