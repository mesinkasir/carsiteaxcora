<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/lib/PicoCompatPluginInterface.php>
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * Common interface for PicoDeprecated compatibility plugins
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
interface PicoCompatPluginInterface
{
    /**
     * Handles a Pico event
     *
     * @param string $eventName name of the triggered event
     * @param array  $params    passed parameters
     */
    public function handleEvent($eventName, array $params);

    /**
     * Returns a list of names of compat plugins required by this plugin
     *
     * @return string[] required plugins
     */
    public function getDependencies();

    /**
     * Returns the plugin's instance of Pico
     *
     * @see Pico
     *
     * @return Pico the plugin's instance of Pico
     */
    public function getPico();

    /**
     * Returns the plugin's main PicoDeprecated plugin instance
     *
     * @see PicoDeprecated
     *
     * @return PicoDeprecated the plugin's instance of Pico
     */
    public function getPicoDeprecated();

    /**
     * Returns the version of the API this plugin uses
     *
     * @return int the API version used by this plugin
     */
    public function getApiVersion();
}
