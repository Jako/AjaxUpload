<?php
/**
 * AjaxUploadRequired Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use xPDO;

class AjaxUploadRequiredHook extends Hook
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'fieldname' => '',
            'requiredMessage' => $this->modx->lexicon('ajaxupload.uploadRequired'),
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
        if ($this->getProperty('fieldname')) {
            $files = $this->hook->getValue($this->getProperty('fieldname'));
            if (empty($files)) {
                $this->hook->addError($this->getProperty('fieldname'), $this->getProperty('requiredMessage'));
            }
        }
        return !$this->hook->hasErrors();
    }
}
