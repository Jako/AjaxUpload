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
$ajaxuploadCorePath = $modx->getOption('ajaxupload.core_path', null, $modx->getOption('core_path') . 'components/ajaxupload/');
$ajaxuploadAssetsPath = $modx->getOption('ajaxupload.assets_path', null, $modx->getOption('assets_path') . 'components/ajaxupload/');
$ajaxuploadAssetsUrl = $modx->getOption('ajaxupload.assets_url', null, $modx->getOption('assets_url') . 'components/ajaxupload/');

$ajaxuploadFieldname = $modx->getOption('ajaxuploadFieldname', $scriptProperties, '');
$ajaxuploadFieldformat = $modx->getOption('ajaxuploadFieldformat', $scriptProperties, 'csv');
$ajaxuploadTarget = $modx->getOption('ajaxuploadTarget', $scriptProperties, '');
$scriptProperties['debug'] = (bool)$modx->getOption('ajaxuploadDebug', $scriptProperties, $modx->getOption('ajaxupload.debug', null, false));
$scriptProperties['uid'] = $modx->getOption('ajaxuploadUid', $scriptProperties, '');
$scriptProperties['cacheExpires'] = $modx->getOption('ajaxuploadCacheExpires', $scriptProperties, $modx->getOption('ajaxupload.cache_expires', null, '4'));
$scriptProperties['clearQueue'] = (bool)$modx->getOption('ajaxuploadClearQueue', $scriptProperties, false);
$scriptProperties['allowOverwrite'] = (bool)$modx->getOption('ajaxuploadAllowOverwrite', $scriptProperties, true);

$debug = $scriptProperties['debug'];

// process $ajaxuploadTarget. Pick a value from the form
// Inspired from the email's hook of formit (fihooks.class.php)
if (is_string($ajaxuploadTarget)) {
    foreach ($fields as $k => $v) {
        if (is_scalar($k) && is_scalar($v)) {
            $ajaxuploadTarget = str_replace('[[+'.$k.']]',$v,$ajaxuploadTarget);
        }
    }
}

if (!$modx->loadClass('AjaxUpload', $ajaxuploadCorePath . 'model/ajaxupload/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load AjaxUpload class.', '', 'AjaxUpload2Formit');
    if ($debug) {
        return 'Could not load AjaxUpload class.';
    } else {
        return '';
    }
}

$scriptProperties['ajaxupload.core_path'] = $ajaxuploadCorePath;
$scriptProperties['ajaxupload.assets_path'] = $ajaxuploadAssetsPath;
$scriptProperties['ajaxupload.assets_url'] = $ajaxuploadAssetsUrl;
$ajaxUpload = new AjaxUpload($modx, $scriptProperties);
if (!$ajaxUpload->initialize()) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize AjaxUpload class.', '', 'AjaxUpload2Formit');
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
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadFieldname.', '', 'AjaxUpload2Formit');
        $success = false;
        break;
    case (empty($ajaxuploadTarget)) :
        $hook->addError($scriptProperties['uid'], 'Missing parameter ajaxuploadTarget.');
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing parameter ajaxuploadTarget.', '', 'AjaxUpload2Formit');
        $success = false;
        break;
    default :
        $errors = $ajaxUpload->saveUploads($ajaxuploadTarget, $scriptProperties['clearQueue']);
        if ($errors) {
            $hook->addError($scriptProperties['uid'], $errors);
            $success = false;
            break;
        }
        $ajaxUpload->deleteExisting();
        $ajaxuploadValue = $ajaxUpload->getValue($ajaxuploadFieldformat);
        $hook->setValue($ajaxuploadFieldname, $ajaxuploadValue);
        $success = true;
}
return $success;
