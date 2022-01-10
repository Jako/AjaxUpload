<?php
/**
 * AjaxUploadAttachments Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use xPDO;

class AjaxUploadAttachmentsHook extends Hook
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
            'cacheExpires::int' => $this->modx->getOption('ajaxupload.cache_expires', null, '4'),
            'clearQueue::bool' => false,
            'allowOverwrite::bool' => true,
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
            $assetsPath = $this->modx->getOption('assets_path');
            $assetsUrl = $this->modx->getOption('assets_url');
            $assetsUrlLength = strlen($assetsUrl);

            $attachments = $this->hook->getValue($this->getProperty('fieldname'));
            if ($this->getProperty('fieldformat') == 'json') {
                $attachments = json_decode($attachments, true);
            } else {
                $attachments = (!empty($attachments)) ? explode(',', $attachments) : [];
            }

            $this->hook->modx->getService('mail', 'mail.modPHPMailer');

            foreach ($attachments as $attachment) {
                $attachment = substr($attachment, $assetsUrlLength);
                if (file_exists($assetsPath . $attachment) && is_file($assetsPath . $attachment)) {
                    $this->hook->modx->mail->mailer->AddAttachment($assetsPath . $attachment);
                } elseif ($this->getProperty('debug')) {
                    if (!is_file($assetsPath . $attachment)) {
                        $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'The attached file ' . $assetsPath . $attachment . ' is not a file!', '', 'AjaxUploadAttachments');
                    } elseif (!file_exists($assetsPath . $attachment)) {
                        $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'The attached file ' . $assetsPath . $attachment . ' does not exist!', '', 'AjaxUploadAttachments');
                    }
                }
            }
        }
        return true;
    }
}
