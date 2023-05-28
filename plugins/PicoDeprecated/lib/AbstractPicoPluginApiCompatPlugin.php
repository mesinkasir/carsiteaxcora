<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/lib/AbstractPicoPluginApiCompatPlugin.php>
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * Abstract class to extend from when implementing a PicoDeprecated plugin API
 * compatibility plugin
 *
 * Please refer to {@see PicoPluginApiCompatPluginInterface} for more information about
 * how to develop a PicoDeprecated plugin API compatibility plugin.
 *
 * @see     PicoPluginApiCompatPluginInterface
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
abstract class AbstractPicoPluginApiCompatPlugin extends AbstractPicoCompatPlugin implements
    PicoPluginApiCompatPluginInterface
{
    /**
     * Map of core events matching event signatures of older API versions
     *
     * @see AbstractPicoPluginApiCompatPlugin::handleEvent()
     *
     * @var array<string,string>
     */
    protected $eventAliases = array();

    /**
     * {@inheritDoc}
     */
    public function handleEvent($eventName, array $params)
    {
        parent::handleEvent($eventName, $params);

        // trigger core events matching the event signatures of older API versions
        if (isset($this->eventAliases[$eventName])) {
            foreach ($this->eventAliases[$eventName] as $eventAlias) {
                $this->triggerEvent($eventAlias, $params);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handleCustomEvent($eventName, array $params = array())
    {
        $this->getPicoDeprecated()->triggerEvent($this->getApiVersionSupport(), $eventName, $params);
    }

    /**
     * Triggers deprecated events on plugins of the supported API version
     *
     * @param string $eventName name of the event to trigger
     * @param array  $params    optional parameters to pass
     */
    protected function triggerEvent($eventName, array $params = array())
    {
        $apiVersion = $this->getApiVersionSupport();
        $picoDeprecated = $this->getPicoDeprecated();

        if ($apiVersion !== $picoDeprecated::API_VERSION) {
            foreach ($picoDeprecated->getCompatPlugins() as $compatPlugin) {
                if ($compatPlugin->getApiVersion() === $apiVersion) {
                    $compatPlugin->handleEvent($eventName, $params);
                }
            }
        }

        $picoDeprecated->triggerEvent($apiVersion, $eventName, $params);
    }
}
