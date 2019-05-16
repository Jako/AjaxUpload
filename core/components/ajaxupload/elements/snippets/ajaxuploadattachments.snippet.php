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

$assetsPath = $modx->getOption('assets_path');
$assetsUrl = $modx->getOption('assets_url');
$assetsUrlLength = strlen($assetsUrl);

if ($ajaxuploadFieldname) {
    $attachments = $hook->getValue($ajaxuploadFieldname);
    if ($ajaxuploadFieldformat == 'json') {
        $attachments = json_decode($attachments);
    } else {
        $attachments = explode(',', $attachments);
    }

    $hook->modx->getService('mail', 'mail.modPHPMailer');

    foreach ($attachments as $attachment) {
        $attachment = substr($attachment, $assetsUrlLength);
        $hook->modx->mail->mailer->AddAttachment($assetsPath . $attachment);
    }
}
return true;