<?php
/**
 * AjaxUpload2Formit
 *
 * @package ajaxupload
 * @subpackage hook
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var fiHooks $hook
 */

use TreehillStudio\AjaxUpload\Snippets\AjaxUpload2FormitHook;

$corePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
/** @var AjaxUpload $ajaxupload */
$ajaxupload = $modx->getService('ajaxupload', 'AjaxUpload', $corePath . 'model/ajaxupload/', [
    'core_path' => $corePath
]);

$snippet = new AjaxUpload2FormitHook($modx, $hook, $scriptProperties);
if ($snippet instanceof TreehillStudio\AjaxUpload\Snippets\AjaxUpload2FormitHook) {
    return $snippet->execute();
}
return 'TreehillStudio\AjaxUpload\Snippets\AjaxUpload2FormitHook class not found';