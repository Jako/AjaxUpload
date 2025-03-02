<?php
/**
 * AjaxUploadAttachments Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

use modMediaSource;

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
            $source = $this->modx->getObject('modMediaSource', $this->getProperty('targetMediasource'));
            $source->initialize();
            $targetPath = $source->getBasePath();
        } else {
            $targetPath = $this->modx->getOption('assets_path');
        }
        foreach ($this->getProperty('uid') as $uid) {
            $files = $this->getUidValues($uid);
            $this->hook->modx->getService('mail', 'mail.modPHPMailer');
            foreach ($files as $file) {
                $this->hook->modx->mail->mailer->AddAttachment($targetPath . $file);
            }
        }
        return true;
    }
}
