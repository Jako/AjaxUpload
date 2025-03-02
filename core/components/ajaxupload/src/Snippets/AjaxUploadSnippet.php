<?php
/**
 * AjaxUpload Snippet
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use modMediaSource;
use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\FilePond\Helper\Post;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;
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
        $resourceId = ($this->modx->resource) ? $this->modx->resource->get('id') : 0;
        return [
            'debug' => $this->modx->getOption('ajaxupload.debug', null, false),
            'uid' => md5($this->modx->getOption('site_url') . '-' . $resourceId),
            'placeholderPrefix' => 'fi.',
            'fieldformat' => 'csv',
            'value' => '',
            'addCss::bool' => true,
            'addJscript::bool' => true,
            'scriptTpl' => 'tplAjaxuploadScript',
            'uploadSectionTpl' => 'tplAjaxuploadUploadSection',
            'acceptedFileTypes::explodeSeparated' => 'image/jpeg,image/gif,image/png,image/webp',
            'maxFiles' => 3,
            'maxFileSize' => "8MB",
            'showCredits::bool' => true,
            'targetMediasource::int' => 0,
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
        $cssUrl = $this->ajaxupload->getOption('cssUrl') . 'web/';
        $cssSourceUrl = $assetsUrl . '../../../source/css/web/';
        $nodeUrl = $assetsUrl . '../../../node_modules/';

        if ($this->ajaxupload->getOption('addCss')) {
            if ($this->ajaxupload->getOption('debug') && ($assetsUrl != MODX_ASSETS_URL . 'components/' . $this->ajaxupload->namespace . '/')) {
                $this->modx->regClientCSS($cssSourceUrl . 'ajaxupload.css?v=' . $this->ajaxupload->version);
            } else {
                $this->modx->regClientCSS($cssUrl . 'ajaxupload.min.css?v=' . $this->ajaxupload->version);
            }
        }
        if ($this->ajaxupload->getOption('addJscript')) {
            if ($this->ajaxupload->getOption('debug') && ($assetsUrl != MODX_ASSETS_URL . 'components/' . $this->ajaxupload->namespace . '/')) {
                $this->modx->regClientScript($nodeUrl . 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js?v=' . $this->ajaxupload->version);
                $this->modx->regClientScript($nodeUrl . 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js?v=' . $this->ajaxupload->version);
                $this->modx->regClientScript($nodeUrl . 'filepond/dist/filepond.js?v=' . $this->ajaxupload->version);
            } else {
                $this->modx->regClientScript($jsUrl . 'ajaxupload.min.js?v=' . $this->ajaxupload->version);
            }
        }

        $value = $this->getProperty('value');
        if ($value) {
            if (!$this->ajaxupload->prepareFilePond()) {
                return $this->modx->lexicon('ajaxupload.cacheNotCreatable');
            }

            $files = explode(',', $value);
            $ids = [];
            if ($this->getProperty('targetMediasource')) {
                /** @var modMediaSource $source */
                $source = $this->modx->getObject('modMediaSource', $this->getProperty('targetMediasource'));
                $source->initialize();
                $targetPath = $source->getBasePath();
            } else {
                $targetPath = $this->modx->getOption('assets_path');
            }
            foreach ($files as $file) {
                $transfer = new Transfer();
                $path = TRANSFER_DIR . DIRECTORY_SEPARATOR . $transfer->getId();
                FilePond::create_secure_directory($path);
                if (file_exists($targetPath . $file)) {
                    if (copy($targetPath . $file, $path . DIRECTORY_SEPARATOR . basename($file))) {
                        $ids[] = $transfer->getId();
                    }
                }
            }
            $value = json_encode($ids);
            $this->properties['fieldformat'] = 'json';
        } else {
            $value = $this->modx->getPlaceholder($this->getProperty('placeholderPrefix') . $this->getProperty('uid'));
        }

        if (count($_POST)) {
            // The form field is posted as JSON by FilePond and when the validation is failing this format is used in the placeholder
            $this->properties['fieldformat'] = 'json';
        }
        switch ($this->getProperty('fieldformat')) {
            case 'json' :
                $value = json_decode($value, true);
                break;
            case 'csv':
            default :
                $value = ($value) ? explode(',', $value) : [];
                break;
        }

        $files = [];
        if ($value && !Post::is_associative_array($value)) {
            foreach ($value as $v) {
                $files[] = [
                    'source' => $v,
                    'options' => ['type' => 'limbo']
                ];
            }
        }

        $properties = $this->ajaxupload->options;
        $properties['name'] = $properties['uid'] . '[]';
        $properties['credits'] = $this->getProperty('showCredits') ? '["https://pqina.nl/", "Powered by PQINA"]' : '[]';
        $properties['acceptedFileTypes'] = json_encode($this->getProperty('acceptedFileTypes'), JSON_UNESCAPED_SLASHES);
        $properties['files'] = json_encode($files, JSON_UNESCAPED_SLASHES);

        $this->modx->regClientScript($this->ajaxupload->parse->getChunk($this->getProperty('scriptTpl'), $properties), true);
        return $this->ajaxupload->parse->getChunk($this->getProperty('uploadSectionTpl'), $properties);
    }

    /**
     * Output debug information.
     *
     * @access public
     * @return string The debug output
     */
    public function debugOutput()
    {
        return implode('<br/>', $this->ajaxupload->debug);
    }
}
