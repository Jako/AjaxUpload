<?php
/**
 * AjaxUpload2FormIt Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use modMediaSource;
use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;
use xPDO;

class AjaxUpload2FormitHook extends AjaxUploadHook
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
            'uid::explodeSeparated' => '',
            'target' => '',
            'fieldformat' => 'csv',
            'cacheExpires::int' => $this->modx->getOption('ajaxupload.cache_expires', null, '4'),
            'allowOverwrite::bool' => true,
            'sanitizeFilename::bool' => false,
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
        if (!$this->ajaxupload->initialize($this->getProperties())) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload');
            return false;
        }
        if (empty($this->getProperty('uid'))) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadUid.', '', 'AjaxUpload2Formit');
            return false;
        }
        if ($this->getProperty('targetMediasource')) {
            /** @var modMediaSource $source */
            $source = $this->modx->getObject('sources.modMediaSource', $this->getProperty('targetMediasource'));
            $source->initialize();
            $targetPath = $source->getBasePath();
        } else {
            $targetPath = $this->modx->getOption('assets_path');
        }
        foreach ($this->getProperty('uid') as $uid) {
            if (empty($this->getProperty('target'))) {
                $this->hook->addError($uid, $this->modx->lexicon('ajaxupload.missingParameterAjaxuploadTarget'));
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTarget.', '', 'AjaxUpload2Formit');
                return false;
            }
            if (!$this->ajaxupload->prepareFilePond()) {
                $this->hook->addError($uid, $this->modx->lexicon('ajaxupload.cacheNotCreatable'));
                return false;
            }
            if (!file_exists($targetPath . $this->getProperty('target'))) {
                $cacheManager = $this->modx->getCacheManager();
                if (!$cacheManager->writeTree($targetPath . $this->getProperty('target'))) {
                    $this->hook->addError($uid, $this->modx->lexicon('ajaxupload.targetNotCreatable'));
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not create the target folder!', '', 'AjaxUpload');
                }
            }

            $ids = $this->hook->getValue($uid);
            $ids = isset($ids['name']) ? [] : $ids;
            $filenames = [];
            $target = rtrim($targetPath . $this->getProperty('target'), '/');
            foreach ($ids as $id) {
                // create transfer wrapper around upload
                /** @var Transfer $transfer */
                $transfer = FilePond::get_transfer(TRANSFER_DIR, $id);

                // transfer not found
                if (!$transfer) continue;

                // move files
                $files = $transfer->getFiles();
                if ($files) {
                    foreach ($files as $file) {
                        $filename = FilePond::move_file($file, $target, false, $this->getProperty('allowOverwrite'));
                        if ($this->getProperty('sanitizeFilename')) {
                            $pathinfo = pathinfo($filename);
                            $newFilename = $this->modx->filterPathSegment($pathinfo['filename']);
                            $newExtension = $this->modx->filterPathSegment($pathinfo['extension']);
                            $newFilename = $pathinfo['dirname'] . '/' . $newFilename . '.' . $newExtension;
                            rename($filename, $newFilename);
                            $filename = $newFilename;
                        }
                        if ($filename) {
                            $filename = $this->ajaxupload->relativePath($targetPath, $filename);
                            $filenames[] = $filename;
                        }
                    }
                }

                // remove transfer directory
                FilePond::remove_transfer_directory(TRANSFER_DIR, $id);
            }

            $this->setUidValues($uid, $filenames);
        }
        return true;
    }
}
