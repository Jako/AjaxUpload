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
            'targetMediasource::int' => 0,
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
        if ($this->getProperty('targetMediasource')) {
            /** @var modMediaSource $source */
            $source = $this->modx->getObject('sources.modMediaSource', $this->getProperty('targetMediasource'));
            $source->initialize();
            $targetPath = $source->getBasePath();
        } else {
            $targetPath = $this->modx->getOption('assets_path');
        }

        foreach ($this->getProperty('uid') as $uid) {
            $files = $this->getUidValues($uid);
            foreach ($files as $file) {
                if (file_exists($targetPath . $file)) {
                    unlink($targetPath . $file);
                }
            }
        }
        return true;
    }
}
