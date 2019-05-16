<?php
/**
 * AjaxUpload
 *
 * @package ajaxupload
 * @subpackage snippet
 *
 * @var modX $modx
 * @var array $scriptProperties
 */
$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
$ajaxuploadAssetsPath = $modx->getOption('ajaxupload.assets_path', null, $modx->getOption('assets_path') . 'components/ajaxupload/');
$ajaxuploadAssetsUrl = $modx->getOption('ajaxupload.assets_url', null, $modx->getOption('assets_url') . 'components/ajaxupload/');
$debug = $modx->getOption('debug', $scriptProperties, $modx->getOption('ajaxupload.debug', null, false), true);

if (!$modx->loadClass('AjaxUpload', $ajaxuploadCorePath . 'model/ajaxupload/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load AjaxUpload class.', '', 'AjaxUpload');
    if ($debug) {
        return 'Could not load AjaxUpload class.';
    } else {
        return '';
    }
}

$scriptProperties['core_path'] = $ajaxuploadCorePath;
$scriptProperties['assets_path'] = $ajaxuploadAssetsPath;
$scriptProperties['assets_url'] = $ajaxuploadAssetsUrl;
$ajaxUpload = new AjaxUpload($modx, $scriptProperties);
if (!$ajaxUpload->initialize($scriptProperties)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload');
    if ($debug) {
        return 'Could not load initialize AjaxUpload class.';
    } else {
        return '';
    }
}
return $ajaxUpload->output() . $ajaxUpload->debugOutput();
