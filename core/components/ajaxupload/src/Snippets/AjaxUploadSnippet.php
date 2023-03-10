<?php
/**
 * AjaxUpload Snippet
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use xPDO;

class AjaxUploadSnippet extends Snippet
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'debug' => $this->modx->getOption('ajaxupload.debug', null, false),
            'scriptTpl' => 'tplAjaxuploadScript',
            'uploadSectionTpl' => 'tplAjaxuploadUploadSection',
        ];
    }

    /**
     * Execute the snippet and return the result.
     *
     * @return string
     * @throws /Exception
     */
    public function execute()
    {
        $debug = $this->getProperty('debug');

        if (!$this->ajaxupload->initialize($this->getProperties())) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload');
            if ($debug) {
                return 'Could not load initialize AjaxUpload class.';
            } else {
                return '';
            }
        }

        return $this->output() . $this->debugOutput();
    }

    /**
     * Output the form inputs.
     *
     * @access public
     * @return string The output
     */
    public function output()
    {
        $assetsUrl = $this->ajaxupload->getOption('assetsUrl');
        $jsUrl = $this->ajaxupload->getOption('jsUrl') . 'web/';
        $jsSourceUrl = $assetsUrl . '../../../source/js/web/';
        $cssUrl = $this->ajaxupload->getOption('cssUrl') . 'web/';
        $cssSourceUrl = $assetsUrl . '../../../source/css/web/';

        if ($this->ajaxupload->getOption('addJquery')) {
            $this->modx->regClientScript('//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
        }
        if ($this->ajaxupload->getOption('addCss')) {
            if ($this->ajaxupload->getOption('debug') && ($assetsUrl != MODX_ASSETS_URL . 'components/' . $this->ajaxupload->namespace . '/')) {
                $this->modx->regClientCSS($cssSourceUrl . 'ajaxupload.css?v=' . $this->ajaxupload->version);
            } else {
                $this->modx->regClientCSS($cssUrl . 'ajaxupload.min.css?v=' . $this->ajaxupload->version);
            }
        }
        if ($this->ajaxupload->getOption('addJscript')) {
            if ($this->ajaxupload->getOption('debug') && ($assetsUrl != MODX_ASSETS_URL . 'components/' . $this->ajaxupload->namespace . '/')) {
                $this->modx->regClientScript($jsSourceUrl . 'fileuploader.js?v=' . $this->ajaxupload->version);
                $this->modx->regClientScript($jsSourceUrl . 'ajaxupload.js?v=' . $this->ajaxupload->version);
            } else {
                $this->modx->regClientScript($jsUrl . 'ajaxupload.min.js?v=' . $this->ajaxupload->version);
            }
        }
        $properties = $this->ajaxupload->options;
        $this->modx->regClientScript($this->modx->getChunk($this->getProperty('scriptTpl'), $properties), true);

        // preload files from $_SESSION
        $itemList = '';
        if (is_array($this->ajaxupload->session[$this->ajaxupload->getOption('uid')])) {
            $itemList = $this->ajaxupload->loadFiles($this->ajaxupload->session[$this->ajaxupload->getOption('uid')]);
        }
        $properties['items'] = $itemList;
        return $this->modx->getChunk($this->getProperty('uploadSectionTpl'), $properties);
    }

    /**
     * Output debug information.
     *
     * @access public
     * @return string The debug output
     */
    public function debugOutput()
    {
        if ($this->ajaxupload->getOption('debug')) {
            $this->ajaxupload->debug[] = '$_SESSION["ajaxupload"]:<pre>' . print_r($this->ajaxupload->session[$this->ajaxupload->getOption('uid')], true) . '</pre>';
        }
        return implode('<br/>', $this->ajaxupload->debug);
    }
}
