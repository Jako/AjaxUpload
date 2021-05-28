<?php
/**
 * AjaxUploadAttachments
 *
 * @package ajaxupload
 * @subpackage hook
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var fiHooks $hook
 */
$ajaxuploadFieldname = $modx->getOption('ajaxuploadFieldname', $scriptProperties, '');
$ajaxuploadFieldformat = $modx->getOption('ajaxuploadFieldformat', $scriptProperties, 'csv');
$debug = (bool)$modx->getOption('ajaxuploadDebug', $scriptProperties, $modx->getOption('ajaxupload.debug', null, false));

$assetsPath = $modx->getOption('assets_path');
$assetsUrl = $modx->getOption('assets_url');
$assetsUrlLength = strlen($assetsUrl);

if ($ajaxuploadFieldname) {
    $attachments = $hook->getValue($ajaxuploadFieldname);
    if ($ajaxuploadFieldformat == 'json') {
        $attachments = json_decode($attachments, true);
    } else {
        $attachments = (!empty($attachments)) ? explode(',', $attachments) : array();
    }

    $hook->modx->getService('mail', 'mail.modPHPMailer');

    foreach ($attachments as $attachment) {
        $attachment = substr($attachment, $assetsUrlLength);
        if (file_exists($assetsPath . $attachment) && is_file($assetsPath . $attachment)) {
            $hook->modx->mail->mailer->AddAttachment($assetsPath . $attachment);
        } elseif ($debug) {
            if (!is_file($assetsPath . $attachment)) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'The attached file ' . $assetsPath . $attachment . ' is not a file!', '', 'AjaxUploadAttachments');
            } elseif (!file_exists($assetsPath . $attachment)) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'The attached file ' . $assetsPath . $attachment . ' does not exist!', '', 'AjaxUploadAttachments');
            }
        }
    }
}
return true;
