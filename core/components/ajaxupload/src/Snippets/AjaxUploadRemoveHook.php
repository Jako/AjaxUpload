<?php
/**
 * AjaxUploadRemove Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use modMediaSource;

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
            'uid::explodeSeparatedUid' => '',
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
            $targetPath = realpath($source->getBasePath());
        } else {
            $targetPath = realpath($this->modx->getOption('assets_path'));
        }

        foreach ($this->getProperty('uid') as $uid) {
            $files = $this->getUidValues($uid);
            foreach ($files as $file) {
                // validate the file path for directory traversal
                $path = realpath($targetPath . $file);
                if ($path !== false && strpos($path, $targetPath) === 0 && file_exists($targetPath . $file)) {
                    unlink($targetPath . $file);
                }
            }
        }
        return true;
    }
}
