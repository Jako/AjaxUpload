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

use TreehillStudio\AjaxUpload\Snippets\AjaxUploadAttachmentsHook;

$corePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
/** @var AjaxUpload $ajaxupload */
$ajaxupload = $modx->getService('ajaxupload', 'AjaxUpload', $corePath . 'model/ajaxupload/', [
    'core_path' => $corePath
]);

$snippet = new AjaxUploadAttachmentsHook($modx, $hook, $scriptProperties);
if ($snippet instanceof TreehillStudio\AjaxUpload\Snippets\AjaxUploadAttachmentsHook) {
    return $snippet->execute();
}
return 'TreehillStudio\AjaxUpload\Snippets\AjaxUploadAttachmentsHook class not found';