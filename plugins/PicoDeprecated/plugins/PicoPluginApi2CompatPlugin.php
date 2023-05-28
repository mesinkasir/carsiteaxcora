<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/plugins/PicoPluginApi2CompatPlugin.php>
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
 * Maintains backward compatibility with plugins using API version 2, written
 * for Pico 2.0
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
class PicoPluginApi2CompatPlugin extends AbstractPicoPluginApiCompatPlugin
{
    /**
     * This plugin extends {@see PicoThemeApi2CompatPlugin}
     *
     * @var string[]
     */
    protected $dependsOn = array('PicoThemeApi2CompatPlugin');

    /**
     * Map of core events matching event signatures of older API versions
     *
     * @see AbstractPicoPluginApiCompatPlugin::handleEvent()
     *
     * @var array<string,string>
     */
    protected $eventAliases = array(
        'onPluginsLoaded'         => array('onPluginsLoaded'),
        'onPluginManuallyLoaded'  => array('onPluginManuallyLoaded'),
        'onRequestUrl'            => array('onRequestUrl'),
        'onRequestFile'           => array('onRequestFile'),
        'onContentLoading'        => array('onContentLoading'),
        'on404ContentLoading'     => array('on404ContentLoading'),
        'on404ContentLoaded'      => array('on404ContentLoaded'),
        'onContentLoaded'         => array('onContentLoaded'),
        'onMetaParsing'           => array('onMetaParsing'),
        'onMetaParsed'            => array('onMetaParsed'),
        'onContentParsing'        => array('onContentParsing'),
        'onContentPrepared'       => array('onContentPrepared'),
        'onContentParsed'         => array('onContentParsed'),
        'onPagesLoading'          => array('onPagesLoading'),
        'onSinglePageLoading'     => array('onSinglePageLoading'),
        'onSinglePageContent'     => array('onSinglePageContent'),
        'onSinglePageLoaded'      => array('onSinglePageLoaded'),
        'onPagesDiscovered'       => array('onPagesDiscovered'),
        'onPagesLoaded'           => array('onPagesLoaded'),
        'onCurrentPageDiscovered' => array('onCurrentPageDiscovered'),
        'onPageTreeBuilt'         => array('onPageTreeBuilt'),
        'onPageRendering'         => array('onPageRendering'),
        'onPageRendered'          => array('onPageRendered'),
        'onMetaHeaders'           => array('onMetaHeaders'),
        'onYamlParserRegistered'  => array('onYamlParserRegistered'),
        'onParsedownRegistered'   => array('onParsedownRegistered'),
        'onTwigRegistered'        => array('onTwigRegistered')
    );

    /**
     * Pico's config array
     *
     * @see Pico::$config
     * @see PicoPluginApi2CompatPlugin::onConfigLoaded()
     *
     * @var array|null
     */
    protected $config;

    /**
     * Sets PicoPluginApi1CompatPlugin::$config and handles the theme_url
     * config param
     *
     * @see PicoPluginApi2CompatPlugin::$config
     *
     * @param array $config
     */
    public function onConfigLoaded(array &$config)
    {
        $this->config = &$config;

        if (!empty($config['theme_url'])) {
            $config['themes_url'] = $this->getPico()->getAbsoluteUrl($config['theme_url']);
            $config['theme_url'] = &$config['themes_url'];
        }
    }

    /**
     * Triggers the onConfigLoaded event
     *
     * @param string $theme           name of current theme
     * @param int    $themeApiVersion API version of the theme
     * @param array  &$themeConfig    config array of the theme
     */
    public function onThemeLoaded($theme, $themeApiVersion, array &$themeConfig)
    {
        $this->triggerEvent('onConfigLoaded', array(&$this->config));
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion()
    {
        return PicoDeprecated::API_VERSION_3;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersionSupport()
    {
        return PicoDeprecated::API_VERSION_2;
    }
}
