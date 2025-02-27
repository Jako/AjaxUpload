<?php
/**
 * AjaxUploadAttachments Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

class AjaxUploadAttachmentsHook extends AjaxUploadHook
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
            $this->hook->modx->getService('mail', 'mail.modPHPMailer');
            foreach ($files as $file) {
                $this->hook->modx->mail->mailer->AddAttachment($this->getProperty('targetRelativePath') . $file);
            }
        }
        return true;
    }
}
