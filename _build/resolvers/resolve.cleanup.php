<?php
/**
 * Resolve cleanup
 *
 * @package ajaxupload
 * @subpackage build
 *
 * @var array $options
 * @var xPDOObject $object
 */

$success = false;

if ($object->xpdo) {
    /** @var xPDO $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $c = $modx->newQuery('transport.modTransportPackage');
            $c->where(
                array(
                    'workspace' => 1,
                    "(SELECT
            `signature`
            FROM {$modx->getTableName('transport.modTransportPackage')} AS `latestPackage`
            WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
            ORDER BY
                `latestPackage`.`version_major` DESC,
                `latestPackage`.`version_minor` DESC,
                `latestPackage`.`version_patch` DESC,
                IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
                `latestPackage`.`release_index` DESC
                LIMIT 1,1) = `modTransportPackage`.`signature`",
                )
            );
            $c->where(
                array(
                    'modTransportPackage.signature:LIKE' => $options['namespace'] . '-%',
                    'modTransportPackage.installed:IS NOT' => null
                )
            );
            $c->limit(1);

            /** @var modTransportPackage $oldPackage */
            $oldPackage = $modx->getObject('transport.modTransportPackage', $c);

            $oldVersion = '';
            if ($oldPackage) {
                $oldVersion = $oldPackage->get('version_major') .
                    '.' . $oldPackage->get('version_minor') .
                    '.' . $oldPackage->get('version_patch') .
                    '-' . $oldPackage->get('release');
            }

            if ($oldPackage && $oldPackage->compareVersion('1.6.0', '>')) {
                // Cleanup Folders
                $paths = array(
                    'assets' => $modx->getOption('assets_path', null, MODX_ASSETS_PATH),
                    'core' => $modx->getOption('core_path', null, MODX_CORE_PATH),
                );

                $cleanup = array(
                    'core' => array(
                        'components/ajaxupload/templates'
                    )
                );

                if (!function_exists('recursiveRemoveFolder')) {
                    function recursiveRemoveFolder($dir)
                    {
                        $files = array_diff(scandir($dir), array('.', '..'));
                        foreach ($files as $file) {
                            (is_dir("$dir/$file")) ? recursiveRemoveFolder($dir . '/' . $file) : unlink($dir . '/' . $file);
                        }
                        return rmdir($dir);
                    }
                }

                $countFiles = 0;
                $countFolders = 0;

                foreach ($cleanup as $folder => $files) {
                    foreach ($files as $file) {
                        $legacyFile = $paths[$folder] . $file;
                        if (file_exists($legacyFile)) {
                            if (is_dir($legacyFile)) {
                                recursiveRemoveFolder($legacyFile);
                                $countFolders++;
                            } else {
                                unlink($legacyFile);
                                $countFiles++;
                            }
                        }
                    }
                }

                if ($countFolders || $countFiles) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Removed ' . $countFiles . ' legacy files and ' . $countFolders . ' legacy folders of AjaxUpload before version 1.6.x.');
                }
            }

            $success = true;
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }
}
return $success;
