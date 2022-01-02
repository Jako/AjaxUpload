<?php
/**
 * AjaxUpload Snippet
 *
 * @package ajaxupload
 * @subpackage snippet
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

use TreehillStudio\AjaxUpload\Snippets\AjaxUploadSnippet;

$corePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
/** @var AjaxUpload $ajaxupload */
$ajaxupload = $modx->getService('ajaxupload', 'AjaxUpload', $corePath . 'model/ajaxupload/', [
    'core_path' => $corePath
]);

$snippet = new AjaxUploadSnippet($modx, $scriptProperties);
if ($snippet instanceof TreehillStudio\AjaxUpload\Snippets\AjaxUploadSnippet) {
    return $snippet->execute();
}
return 'TreehillStudio\AjaxUpload\Snippets\AjaxUploadSnippet class not found';