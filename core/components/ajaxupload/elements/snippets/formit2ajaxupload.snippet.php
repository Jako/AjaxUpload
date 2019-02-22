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
$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
$ajaxuploadAssetsPath = $modx->getOption('ajaxupload.assets_path', null, $modx->getOption('assets_path') . 'components/ajaxupload/');
$ajaxuploadAssetsUrl = $modx->getOption('ajaxupload.assets_url', null, $modx->getOption('assets_url') . 'components/ajaxupload/');

$ajaxuploadFieldname = $modx->getOption('ajaxuploadFieldname', $scriptProperties, '');
$ajaxuploadFieldformat = $modx->getOption('ajaxuploadFieldformat', $scriptProperties, 'csv');
$ajaxuploadTarget = $modx->getOption('ajaxuploadTarget', $scriptProperties, '');
$scriptProperties['debug'] = $modx->getOption('ajaxuploadDebug', $scriptProperties, $modx->getOption('ajaxupload.debug', null, false));
$scriptProperties['uid'] = $modx->getOption('ajaxuploadUid', $scriptProperties, '');
$scriptProperties['cacheExpires'] = $modx->getOption('ajaxuploadCacheExpires', $scriptProperties, $modx->getOption('ajaxupload.cache_expires', null, '4'));

$debug = $scriptProperties['debug'];

if (!$modx->loadClass('AjaxUpload', $ajaxuploadCorePath . 'model/ajaxupload/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load AjaxUpload class.', '', 'Formit2AjaxUpload');
    if ($debug) {
        return 'Could not load AjaxUpload class.';
    } else {
        return '';
    }
}

$uidConfig = isset($_SESSION['ajaxupload'][$scriptProperties['uid'] . 'config']) ? $_SESSION['ajaxupload'][$scriptProperties['uid'] . 'config'] : array();

$scriptProperties['core_path'] = $ajaxuploadCorePath;
$scriptProperties['assets_path'] = $ajaxuploadAssetsPath;
$scriptProperties['assets_url'] = $ajaxuploadAssetsUrl;
$ajaxUpload = new AjaxUpload($modx, $scriptProperties);
if (!$ajaxUpload->initialize($uidConfig)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'Formit2AjaxUpload');
    if ($debug) {
        return 'Could not load initialize AjaxUpload class.';
    } else {
        return '';
    }
}

$success = true;
switch (true) {
    case (empty($ajaxuploadFieldname)) :
        $hook->addError($scriptProperties['uid'], 'Missing parameter ajaxuploadFieldname.');
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadFieldname.', '', 'Formit2AjaxUpload');
        $success = false;
        break;
    case (empty($ajaxuploadTarget)) :
        $hook->addError($scriptProperties['uid'], 'Missing parameter ajaxuploadTarget.');
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTarget.', '', 'Formit2AjaxUpload');
        $success = false;
        break;
    default :
        if (!count($_POST)) {
            $ajaxuploadValue = $hook->getValue($ajaxuploadFieldname);
            if ($ajaxuploadValue) {
                switch ($ajaxuploadFieldformat) {
                    case 'json' :
                        $ajaxuploadValue = json_decode($ajaxuploadValue);
                        break;
                    case 'csv':
                    default :
                        $ajaxuploadValue = explode(',', $ajaxuploadValue);
                }
                $ajaxUpload->retrieveUploads($ajaxuploadValue);
            }
        }
        $success = true;
}
return $success;
