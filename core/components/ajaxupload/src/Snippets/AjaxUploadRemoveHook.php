<?php
/**
 * AjaxUploadRemove Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

class AjaxUploadRemoveHook extends AjaxUploadHook
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
            'fieldformat' => 'csv',
            'targetRelativePath' => MODX_ASSETS_PATH,
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
            $files = $this->getUidValues($uid);
            foreach ($files as $file) {
                if (file_exists($this->getProperty('targetRelativePath') . $file)) {
                    unlink($this->getProperty('targetRelativePath') . $file);
                }
            }
        }
        return true;
    }
}
