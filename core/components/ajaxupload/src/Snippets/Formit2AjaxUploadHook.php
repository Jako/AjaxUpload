<?php
/**
 * FormIt2AjaxUpload Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

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
        if (!$this->ajaxupload->initialize($this->getProperties())) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload');
            return false;
        }
        if (empty($this->getProperty('uid'))) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadUid.', '', 'AjaxUpload2Formit');
            return false;
        }
        foreach ($this->getProperty('uid') as $uid) {
            if (empty($this->getProperty('targetRelativePath'))) {
                $this->hook->addError($uid, $this->modx->lexicon('ajaxupload.missingParameterAjaxuploadTargetRelativePath'));
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTargetRelativePath.', '', 'AjaxUpload2Formit');
                return false;
            }

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
                    if (copy($this->getProperty('targetRelativePath') . $file, $path . DIRECTORY_SEPARATOR . basename($file))) {
                        $value[] = $transfer->getId();
                    }
                }

                $this->setUidValues($uid, $value);
                $this->modx->setPlaceholder($this->getProperty('placeholderPrefix') . $uid, $value);
            }
        }
        return true;
    }
}
