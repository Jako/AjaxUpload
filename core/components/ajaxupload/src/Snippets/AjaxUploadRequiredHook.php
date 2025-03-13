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
            'uid::explodeSeparated' => '',
            'requiredUid::explodeSeparated' => '',
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
        $uids = $this->getProperty('uid');
        $requiredUids = $this->getProperty('requiredUid');

        if (empty($requiredUids)) {
            $uidsToCheck = $uids;
        } else {
            $uidsToCheck = array_intersect($uids, $requiredUids);
        }

        foreach ($uidsToCheck as $uid) {
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
