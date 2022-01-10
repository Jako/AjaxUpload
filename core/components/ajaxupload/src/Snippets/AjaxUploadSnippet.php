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
            'debug' => $this->modx->getOption('ajaxupload.debug', null, false)
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
        return $this->ajaxupload->output() . $this->ajaxupload->debugOutput();
    }
}
