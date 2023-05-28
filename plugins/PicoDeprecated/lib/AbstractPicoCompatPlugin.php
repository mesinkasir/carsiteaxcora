<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/lib/AbstractPicoCompatPlugin.php>
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * Abstract class to extend from when implementing a PicoDeprecated
 * compatibility plugin
 *
 * Please refer to {@see PicoCompatPluginInterface} for more information about
 * how to develop a PicoDeprecated compatibility plugin.
 *
 * @see PicoCompatPluginInterface
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
abstract class AbstractPicoCompatPlugin implements PicoCompatPluginInterface
{
    /**
     * Current instance of Pico
     *
     * @see PicoCompatPluginInterface::getPico()
     *
     * @var Pico
     */
    protected $pico;

    /**
     * Instance of the main PicoDeprecated plugin
     *
     * @see PicoCompatPluginInterface::getPicoDeprecated()
     *
     * @var PicoDeprecated
     */
    protected $picoDeprecated;

    /**
     * List of plugins which this plugin depends on
     *
     * @see PicoCompatPluginInterface::getDependencies()
     *
     * @var string[]
     */
    protected $dependsOn = array();

    /**
     * Constructs a new instance of a PicoDeprecated compatibility plugin
     *
     * @param Pico           $pico           current instance of Pico
     * @param PicoDeprecated $picoDeprecated current instance of PicoDeprecated
     */
    public function __construct(Pico $pico, PicoDeprecated $picoDeprecated)
    {
        $this->pico = $pico;
        $this->picoDeprecated = $picoDeprecated;
    }

    /**
     * {@inheritDoc}
     */
    public function handleEvent($eventName, array $params)
    {
        if (method_exists($this, $eventName)) {
            call_user_func_array(array($this, $eventName), $params);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPico()
    {
        return $this->pico;
    }

    /**
     * {@inheritDoc}
     */
    public function getPicoDeprecated()
    {
        return $this->picoDeprecated;
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return (array) $this->dependsOn;
    }
}
