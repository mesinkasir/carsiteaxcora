<?php
/**
 * This file is part of Pico. It's copyrighted by the contributors recorded
 * in the version control history of the file, available from the following
 * original location:
 *
 * <https://github.com/picocms/pico-deprecated/blob/master/plugins/PicoThemeApi1CompatPlugin.php>
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
 * Maintains backward compatibility with themes using API version 1, written
 * for Pico 1.0
 *
 * @author  Daniel Rudolf
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 2.1
 */
class PicoThemeApi1CompatPlugin extends AbstractPicoCompatPlugin
{
    /**
     * This plugin extends {@see PicoThemeApi2CompatPlugin}
     *
     * @var string[]
     */
    protected $dependsOn = array('PicoThemeApi2CompatPlugin');

    /**
     * Lowers the page's meta headers
     *
     * @see PicoThemeApi1CompatPlugin::lowerFileMeta()
     *
     * @param string[] &$meta parsed meta data
     */
    public function onMetaParsed(array &$meta)
    {
        $this->lowerFileMeta($meta);
    }

    /**
     * Lowers the page's meta headers
     *
     * @see PicoThemeApi1CompatPlugin::lowerFileMeta()
     *
     * @param array &$pageData data of the loaded page
     */
    public function onSinglePageLoaded(array &$pageData)
    {
        // don't lower the file meta of the requested page,
        // it was already lowered during the onMetaParsed event
        $contentDir = $this->getPico()->getConfig('content_dir');
        $contentExt = $this->getPico()->getConfig('content_ext');
        if ($contentDir . $pageData['id'] . $contentExt !== $this->getPico()->getRequestFile()) {
            $this->lowerFileMeta($pageData['meta']);
        }
    }

    /**
     * Handles .html Twig templates and re-introcudes the Twig variables
     * rewrite_url and is_front_page
     *
     * @param string &$templateName  file name of the template
     * @param array  &$twigVariables template variables
     */
    public function onPageRendering(&$templateName, array &$twigVariables)
    {
        if (!isset($twigVariables['rewrite_url'])) {
            $twigVariables['rewrite_url'] = $this->getPico()->isUrlRewritingEnabled();
        }

        if (!isset($twigVariables['is_front_page'])) {
            $contentDir = $this->getPico()->getConfig('content_dir');
            $contentExt = $this->getPico()->getConfig('content_ext');
            $requestFile = $this->getPico()->getRequestFile();
            $twigVariables['is_front_page'] = ($requestFile === $contentDir . 'index' . $contentExt);
        }

        // API v2 requires themes to use .twig as file extension
        // try to load the template and if this fails, try .html instead (as of API v1)
        $templateNameInfo = pathinfo($templateName) + array('extension' => '');
        $twig = $this->getPico()->getTwig();

        try {
            $twig->loadTemplate($templateName);
        } catch (Twig_Error_Loader $e) {
            if ($templateNameInfo['extension'] === 'twig') {
                try {
                    $twig->loadTemplate($templateNameInfo['filename'] . '.html');

                    $templateName = $templateNameInfo['filename'] . '.html';
                    $templateNameInfo['extension'] = 'html';
                } catch (Twig_Error_Loader $e) {
                    // template doesn't exist, Twig will very likely fail later
                }
            }
        }
    }

    /**
     * Lowers a page's meta headers as with Pico 1.0 and older
     *
     * This makes unregistered meta headers available using lowered array keys
     * and matches registered meta headers in a case-insensitive manner.
     *
     * @param array &$meta meta data
     */
    protected function lowerFileMeta(array &$meta)
    {
        $metaHeaders = $this->getPico()->getMetaHeaders();

        // get unregistered meta
        $unregisteredMeta = array();
        foreach ($meta as $key => $value) {
            if (!in_array($key, $metaHeaders)) {
                $unregisteredMeta[$key] = &$meta[$key];
            }
        }

        // Pico 1.0 lowered unregistered meta unsolicited...
        if ($unregisteredMeta) {
            $metaHeadersLowered = array_change_key_case($metaHeaders, CASE_LOWER);
            foreach ($unregisteredMeta as $key => $value) {
                $keyLowered = strtolower($key);
                if (isset($metaHeadersLowered[$keyLowered])) {
                    $registeredKey = $metaHeadersLowered[$keyLowered];
                    if ($meta[$registeredKey] === '') {
                        $meta[$registeredKey] = &$unregisteredMeta[$key];
                    }
                } elseif (!isset($meta[$keyLowered]) || ($meta[$keyLowered] === '')) {
                    $meta[$keyLowered] = &$unregisteredMeta[$key];
                }
            }
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
