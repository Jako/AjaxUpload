<?php
/**
 * FormIt2AjaxUpload Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use xPDO;

class Formit2AjaxUploadHook extends Hook
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'debug::bool' => $this->modx->getOption('ajaxupload.debug', null, false),
            'fieldname' => '',
            'fieldformat' => 'csv',
            'target' => '',
            'uid' => '',
            'cacheExpires::int' => $this->modx->getOption('ajaxupload.cache_expires', null, '4')
        ];
    }

    /**
     * Execute the hook and return success.
     *
     * @return bool
     * @throws /Exception
     */
    public function execute()
    {
        $uidConfig = $this->ajaxupload->session[$this->getProperty('uid') . 'config'] ?? $this->getProperties();
        
        if (!$this->ajaxupload->initialize($uidConfig)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload');
            return false;
        }
        if (empty($this->getProperty('fieldname'))) {
            $this->hook->addError($this->getProperty('uid'), 'Missing parameter ajaxuploadFieldname.');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadFieldname.', '', 'AjaxUpload2Formit');
            return false;
        }
        if (empty($this->getProperty('target'))) {
            $this->hook->addError($this->getProperty('uid'), 'Missing parameter ajaxuploadTarget.');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTarget.', '', 'AjaxUpload2Formit');
            return false;
        }

        if (!count($_POST)) {
            $ajaxuploadValue = $this->hook->getValue($this->getProperty('fieldname'));
            if ($ajaxuploadValue) {
                switch ($this->getProperty('fieldformat')) {
                    case 'json' :
                        $ajaxuploadValue = json_decode($ajaxuploadValue, true);
                        break;
                    case 'csv':
                    default :
                        $ajaxuploadValue = explode(',', $ajaxuploadValue);
                }
                $this->ajaxupload->retrieveUploads($ajaxuploadValue);
            }
        }
        return true;
    }
}
