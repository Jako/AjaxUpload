<?php
/**
 * Formit2AjaxUpload
 *
 * @package ajaxupload
 * @subpackage prehook
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var fiHooks $hook
 */

use TreehillStudio\AjaxUpload\Snippets\Formit2AjaxUploadHook;

$corePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
/** @var AjaxUpload $ajaxupload */
$ajaxupload = $modx->getService('ajaxupload', 'AjaxUpload', $corePath . 'model/ajaxupload/', [
    'core_path' => $corePath
]);

$snippet = new Formit2AjaxUploadHook($modx, $hook, $scriptProperties);
if ($snippet instanceof TreehillStudio\AjaxUpload\Snippets\Formit2AjaxUploadHook) {
    return $snippet->execute();
}
return 'TreehillStudio\AjaxUpload\Snippets\Formit2AjaxUploadHook class not found';