<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/lib/PicoPluginApiCompatPluginInterface.php>
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * Common interface for PicoDeprecated plugin API compatibility plugins
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
interface PicoPluginApiCompatPluginInterface extends PicoCompatPluginInterface
{
    /**
     * Handles custom events for plugins of the supported API version
     *
     * @param string $eventName name of the triggered event
     * @param array  $params    passed parameters
     */
    public function handleCustomEvent($eventName, array $params = array());

    /**
     * Returns the API version this plugin maintains backward compatibility for
     *
     * @return int
     */
    public function getApiVersionSupport();
}
