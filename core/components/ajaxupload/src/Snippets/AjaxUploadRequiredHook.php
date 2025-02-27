<?php
/**
 * AjaxUploadRequired Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

class AjaxUploadRequiredHook extends AjaxUploadHook
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'uid' => '',
            'uid::explodeSeparated' => '',
            'fieldformat' => 'csv',
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
        foreach ($this->getProperty('uid') as $uid) {
            if ($uid) {
                $files = $this->getUidValues($uid);
                if (empty($files)) {
                    $this->hook->addError($uid, $this->getProperty('requiredMessage'));
                }
            }
        }
        return !$this->hook->hasErrors();
    }
}
