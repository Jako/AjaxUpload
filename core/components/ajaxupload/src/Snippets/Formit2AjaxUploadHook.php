<?php
/**
 * FormIt2AjaxUpload Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use modMediaSource;
use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;
use xPDO;

class Formit2AjaxUploadHook extends AjaxUploadHook
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
            $source = $this->modx->getObject('modMediaSource', $this->getProperty('targetMediasource'));
            $source->initialize();
            $targetPath = $source->getBasePath();
        } else {
            $targetPath = $this->modx->getOption('assets_path');
        }

        foreach ($this->getProperty('uid') as $uid) {
            if (!$this->ajaxupload->prepareFilePond()) {
                $this->hook->addError($uid, $this->modx->lexicon('ajaxupload.cacheNotCreatable'));
                return false;
            }
            if (!count($_POST)) {
                $files = $this->getUidValues($uid);
                $value = [];
                foreach ($files as $file) {
                    $transfer = new Transfer();
                    $path = TRANSFER_DIR . DIRECTORY_SEPARATOR . $transfer->getId();
                    FilePond::create_secure_directory($path);
                    if (file_exists($targetPath . $file)) {
                        if (copy($targetPath . $file, $path . DIRECTORY_SEPARATOR . basename($file))) {
                            $value[] = $transfer->getId();
                        }
                    } else {
                        $this->hook->addError($uid, $this->modx->lexicon('ajaxupload.filledFileNotFound'));
                    }
                }

                $this->setUidValues($uid, $value);
                $this->modx->setPlaceholder($this->getProperty('placeholderPrefix') . $uid, $value);
            }
        }
        return true;
    }
}
