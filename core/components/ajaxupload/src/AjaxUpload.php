<?php
/**
 * AjaxUpload
 *
 * Copyright 2013-2023 by Thomas Jakobi <office@treehillstudio.com>
 *
 * @package ajaxupload
 * @subpackage classfile
 */

namespace TreehillStudio\AjaxUpload;

use modPhpThumb;
use modX;
use xPDO;

/**
 * class AjaxUpload
 */
class AjaxUpload
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'ajaxupload';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'AjaxUpload';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.6.6-rc1';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * An array of debug information
     * @var array $debug
     */
    public $debug;

    /**
     * The AjaxUpload session
     * @var array $session
     */
    public $session;

    /**
     * AjaxUpload constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'cachePath' => $assetsPath . 'cache/',
            'cacheUrl' => $assetsUrl . 'cache/'
        ], $options);

        // Add default options
        $resourceId = ($this->modx->resource) ? $this->modx->resource->get('id') : 0;
        $this->options = array_merge($this->options, [
            'debug' => $this->getBooleanOption('debug', $options, false),
            'modxversion' => $modxversion['version'],
            'cacheExpires' => intval($this->getOption('cache_expires', $options, 4)),
            'uid' => $this->getOption('uid', $options, md5($this->modx->getOption('site_url') . '-' . $resourceId)),
            'imageTpl' => $this->getOption('image_tpl', $options, 'tplAjaxUploadImage'),
            'uploadAction' => $this->getOption('connectorUrl'),
            'newFilePermissions' => '0664',
            'maxConnections' => 1,
            'language' => $this->modx->getOption('language', $options, $this->modx->cultureKey, true)
        ]);
        $this->debug = [];

        if (!isset($_SESSION['ajaxupload'])) {
            $_SESSION['ajaxupload'] = [];
        }
        $this->session =& $_SESSION['ajaxupload'];

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }

    /**
     * Get Boolean Option
     *
     * @param string $key
     * @param array $options
     * @param mixed $default
     * @return bool
     */
    public function getBooleanOption($key, $options = [], $default = null)
    {
        $option = $this->getOption($key, $options, $default);
        return ($option === 'true' || $option === true || $option === '1' || $option === 1);
    }

    /**
     * Load all class files and init defaults.
     *
     * @param array $properties properties to override the default config (if set)
     * @access public
     * @return boolean success state of initialization
     */
    public function initialize($properties = [])
    {
        // Override uid with properties;
        $this->options['uid'] = $this->getOption('uid', $properties, $this->options['uid']);

        switch ($this->getOption('modxversion')) {
            case 3:
                if (!$this->modx->getService('phpthumb', \MODX\Revolution\modPhpThumb::class)) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not load modPhpThumb class.', '', 'AjaxUpload');
                    $this->debug[] = 'Could not load modPhpThumb class.';
                    return false;
                }
                break;
            default:
                if (!$this->modx->loadClass('modPhpThumb', $this->modx->getOption('core_path') . 'model/phpthumb/', true, true)) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not load modPhpThumb class.', '', 'AjaxUpload');
                    $this->debug[] = 'Could not load modPhpThumb class.';
                    return false;
                }
                break;
        }
        if (!class_exists('qqFileUploader')) {
            include_once $this->getOption('modelPath') . 'fileuploader/fileuploader.class.php';
        }
        $language = $this->getOption('language') ? $this->getOption('language') . ':' : '';
        $this->modx->lexicon->load($language . 'ajaxupload:default');
        if (!isset($this->session[$this->getOption('uid')])) {
            $this->session[$this->getOption('uid')] = [];
        }
        if (!isset($this->session[$this->getOption('uid') . 'delete'])) {
            $this->session[$this->getOption('uid') . 'delete'] = [];
        }
        if (count($properties)) {
            $allowedExtensions = $this->modx->getOption('allowedExtensions', $properties, 'jpg,jpeg,png,gif');
            $allowedExtensions = (!is_array($allowedExtensions)) ? explode(',', $allowedExtensions) : $allowedExtensions;
            $options = [
                'debug' => (bool)$this->getOption('debug', $properties, false),
                'cacheExpires' => intval($this->getOption('cacheExpires', $properties, 4)),
                'allowedExtensions' => $allowedExtensions,
                'allowedExtensionsString' => (!empty($allowedExtensions)) ? "['" . implode("','", $allowedExtensions) . "']" : '',
                'sizeLimit' => $this->modx->getOption('sizeLimit', $properties, $this->modx->getOption('maxFilesizeMb', $properties, 8) * 1024 * 1024),
                'maxFiles' => (integer)$this->modx->getOption('maxFiles', $properties, 3),
                'thumbX' => (integer)$this->modx->getOption('thumbX', $properties, 100),
                'thumbY' => (integer)$this->modx->getOption('thumbY', $properties, 100),
                'addJquery' => (bool)$this->modx->getOption('addJquery', $properties, false),
                'addJscript' => $this->modx->getOption('addJscript', $properties, true),
                'addCss' => $this->modx->getOption('addCss', $properties, true),
            ];
            $this->options = array_merge($this->options, $options);
            $this->session[$this->getOption('uid') . 'config'] = $this->options;
        }
        if (!@is_dir($this->getOption('cachePath'))) {
            if (!@mkdir($this->getOption('cachePath'), 0755)) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not create the cache path.', '', 'AjaxUpload');
            }
        }
        $this->clearCache($this->getOption('cacheExpires'));
        return true;
    }

    /**
     * Preload file list for display if uploaded files exist.
     *
     * @access private
     * @param array $files An array of already uploaded files.
     * @return string html file list to prefill the template
     */
    public function loadFiles(&$files = [])
    {
        $itemList = [];

        foreach ($files as $id => $fileInfo) {
            if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
                $properties = [
                    'fileid' => $id,
                    'thumb' => $fileInfo['base_url'] . $fileInfo['thumbName'],
                    'original' => $fileInfo['originalName'],
                    'style' => 'width: ' . $this->getOption('thumbX') . 'px; height: ' . $this->getOption('thumbY') . 'px;'
                ];
                $files[$id]['thumbName'] = $this->generateThumbnail($fileInfo);
                $itemList[] = $this->modx->getChunk($this->getOption('imageTpl'), $properties);
            } else {
                unset($files[$id]);
            }
        }
        return implode("\r\n", $itemList);
    }

    /**
     * Generate a thumbnail with a random name for an image.
     *
     * @access public
     * @param array $fileInfo An array of file information.
     * @return string html file list to prefill the template
     */
    public function generateThumbnail($fileInfo = [])
    {
        if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
            if (!isset($fileInfo['thumbName'])) {
                $path_info = pathinfo($fileInfo['uniqueName']);
                $thumbOptions = [];
                if (in_array(strtolower($path_info['extension']), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $thumbOptions['src'] = $fileInfo['path'] . $fileInfo['uniqueName'];
                    if ($this->getOption('thumbX')) {
                        $thumbOptions['w'] = $this->getOption('thumbX');
                    }
                    if ($this->getOption('thumbY')) {
                        $thumbOptions['h'] = $this->getOption('thumbY');
                    }
                    if ($this->getOption('thumbX') && $this->getOption('thumbY')) {
                        $thumbOptions['zc'] = '1';
                    }
                } else {
                    $thumbOptions['src'] = $this->getOption('assetsPath') . '/images/generic.png';
                    $thumbOptions['aoe'] = '1';
                    $thumbOptions['fltr'] = ['wmt|' . strtoupper($path_info['extension']) . '|5|C|000000'];
                    if ($this->getOption('thumbX')) {
                        $thumbOptions['w'] = $this->getOption('thumbX');
                    }
                    if ($this->getOption('thumbY')) {
                        $thumbOptions['h'] = $this->getOption('thumbY');
                    }
                    if ($this->getOption('thumbX') && $this->getOption('thumbY')) {
                        $thumbOptions['zc'] = '1';
                    }
                    $thumbOptions['f'] = 'png';
                    $path_info['extension'] = 'png';
                }
                $thumbName = md5($path_info['basename'] . time() . '.thumb') . '.' . $path_info['extension'];

                // Compatibility for 3.x
                if (!class_exists('modPhpThumb')) {
                    class_alias(\MODX\Revolution\modPhpThumb::class, \modPhpThumb::class);
                }
                // generate Thumbnail & save it
                $phpThumb = new modPhpThumb($this->modx, $thumbOptions);
                $phpThumb->initialize();
                if ($phpThumb->GenerateThumbnail()) {
                    if (!$phpThumb->RenderToFile($fileInfo['path'] . $thumbName)) {
                        $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Thumbnail generation: Thumbnail not saved.' . "\nDebugmessages:\n" . implode("\n", $phpThumb->debugmessages), '', 'AjaxUpload');
                        $this->debug[] = 'Thumbnail generation: Thumbnail not saved.' . "\nDebugmessaes:\n" . implode("\n", $phpThumb->debugmessages);
                    } else {
                        $filePerm = (int)$this->getOption('newFilePermissions');
                        if (!@chmod($fileInfo['path'] . $thumbName, octdec($filePerm))) {
                            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not change the thumbnail file permission.', '', 'AjaxUpload');
                        }
                    }
                } else {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Thumbnail generation: Thumbnail not created.' . "\nDebugmessages:\n" . implode("\n", $phpThumb->debugmessages), '', 'AjaxUpload');
                    $this->debug[] = 'Thumbnail generation: Thumbnail not created.' . "\nDebugmessaes:\n" . implode("\n", $phpThumb->debugmessages);
                }
                $fileInfo['thumbName'] = $thumbName;
            }
            return $fileInfo['thumbName'];
        } else {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Thumbnail generation: Original file not found.', '', 'AjaxUpload');
            $this->debug[] = 'Thumbnail generation: Original file not found';
            return false;
        }
    }

    /**
     * Retreive already uploaded files.
     *
     * @access public
     * @param array| $files Array of filenames (relative to $modx->getOption['assetsPath'])
     * @return void
     */
    public function retrieveUploads($files = [])
    {
        foreach ($files as $file) {
            $file = str_replace($this->modx->getOption('assets_url'), '', '/' . ltrim($file, '/'));
            $pathinfo = pathinfo($file);
            if (file_exists($this->modx->getOption('assets_path') . $file)) {
                $fileInfo = [];

                // Get original file info
                $originalName = $pathinfo['basename'];
                $originalExtension = $pathinfo['extension'];
                $originalFilename = (isset($pathinfo['filename'])) ? $pathinfo['filename'] : substr($originalName, 0, strrpos($originalName, '.'));
                $path = $this->modx->getOption('assets_path') . $pathinfo['dirname'] . '/';

                // Prepare session file info
                $fileInfo['originalName'] = $originalName;
                $fileInfo['originalPath'] = $path;
                $fileInfo['originalBaseUrl'] = $this->modx->getOption('assets_url');
                $fileInfo['path'] = $this->getOption('cachePath');
                $fileInfo['base_url'] = $this->getOption('cacheUrl');

                // Check if file is already in session
                $found = false;
                foreach ($this->session[$this->getOption('uid')] as $sessionInfo) {
                    if ($sessionInfo['originalName'] === $fileInfo['originalName']) {
                        $found = true;
                        break;
                    }
                }

                // create unique filename and set permissions
                if (empty($fileInfo['uniqueName'])) {
                    $fileInfo['uniqueName'] = md5($originalFilename . time()) . '.' . $originalExtension;
                }
                if (!@copy($fileInfo['originalPath'] . $fileInfo['originalName'], $fileInfo['path'] . $fileInfo['uniqueName'])) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not copy the uploaded file to the upload cache.', '', 'AjaxUpload');
                }
                $filePerm = (int)$this->getOption('newFilePermissions');
                if (!@chmod($fileInfo['path'] . $fileInfo['uniqueName'], octdec($filePerm))) {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not change the uploaded file permission in the upload cache.', '', 'AjaxUpload');
                }

                // create thumbnail
                $fileInfo['thumbName'] = $this->generateThumbnail($fileInfo);
                if ($fileInfo['thumbName']) {
                    // fill session
                    if (!$found) {
                        $this->session[$this->getOption('uid')][] = $fileInfo;
                    }
                } else {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Thumbnail generation: Original file not found.', '', 'AjaxUpload');
                    $this->debug[] = 'Thumbnail generation: Original file not found';
                    @unlink($fileInfo['path'] . $fileInfo['uniqueName']);
                }
            } else {
                // Check if not found file is in session and delete the unique file and the thumbnail
                foreach ($this->session[$this->getOption('uid')] as $sessionInfo) {
                    if ($sessionInfo['originalName'] === $pathinfo['basename']) {
                        @unlink($this->getOption('cachePath') . $sessionInfo['uniqueName']);
                        @unlink($this->getOption('cachePath') . $sessionInfo['thumbName']);
                        break;
                    }
                }
            }
        }
    }

    /**
     * Save the uploaded files to the specified target.
     *
     * @access public
     * @param string $target Target path (relative to $modx->getOption['assets_path'])
     * @param bool $clearQueue
     * @param bool $allowOverwrite
     * @param bool $sanitizeFilename
     * @return boolean|string
     */
    public function saveUploads($target, $clearQueue = false, $allowOverwrite = true, $sanitizeFilename = false)
    {
        $error = false;
        $target = rtrim($target, '/') . '/';
        if (!file_exists(MODX_ASSETS_PATH . $target)) {
            $cacheManager = $this->modx->getCacheManager();
            if (!$cacheManager->writeTree(MODX_ASSETS_PATH . $target)) {
                $error = $this->modx->lexicon('ajaxupload.targetNotCreatable');
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, $error, '', 'AjaxUpload');
            }
        }
        foreach ($this->session[$this->getOption('uid')] as $fileId => &$fileInfo) {
            if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
                if ($sanitizeFilename) {
                    $pathinfo = pathinfo($fileInfo['originalName']);

                    // Replace all spaces and special characters with -
                    $fileName = $this->modx->filterPathSegment($pathinfo['filename']);
                    $fileName = preg_replace('/[^A-Za-z0-9 _-]/', '', $fileName); // strip non-alphanumeric characters
                    $fileName = str_replace(',', '-', $fileName); // replace comma
                    $fileName = str_replace('.', '-', $fileName); // replace period
                    $fileName = trim($fileName, '-'); // trim edges

                    // Reunite with file extension
                    $fileInfo['originalName'] = $fileName . '.' . $pathinfo['extension'];
                }
                if (!$allowOverwrite) {
                    $pathinfo = pathinfo($fileInfo['originalName']);
                    $i = 0;
                    while (file_exists(MODX_ASSETS_PATH . $target . $pathinfo['filename'] . (empty($i) ? '' : ('_' . $i)) . '.' . $pathinfo['extension'])) {
                        ++$i;
                    }
                    $fileInfo['originalName'] = $pathinfo['filename'] . (empty($i) ? '' : ('_' . $i)) . '.' . $pathinfo['extension'];
                }
                if (!@copy($fileInfo['path'] . $fileInfo['uniqueName'], MODX_ASSETS_PATH . $target . $fileInfo['originalName'])) {
                    $error = $this->modx->lexicon('ajaxupload.targetNotWritable');
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, $error, '', 'AjaxUpload');
                } else {
                    $fileInfo['originalPath'] = MODX_ASSETS_PATH . $target;
                    $fileInfo['originalBaseUrl'] = $this->modx->getOption('assets_url') . $target;
                }
                if ($clearQueue) {
                    @unlink($fileInfo['path'] . $fileInfo['uniqueName']);
                    @unlink($fileInfo['path'] . $fileInfo['thumbName']);
                    unset($fileInfo[$fileId]);
                }
            }
        }
        return $error;
    }

    /**
     * Delete existing files in target that are deleted in $_SESSION.
     *
     * @access public
     * @return void
     */
    public function deleteExisting()
    {
        foreach ($this->session[$this->getOption('uid') . 'delete'] as $fileInfo) {
            if (isset($fileInfo['originalPath']) && file_exists($fileInfo['originalPath'] . $fileInfo['originalName'])) {
                @unlink($fileInfo['originalPath'] . $fileInfo['originalName']);
            }
        }
        $this->session[$this->getOption('uid') . 'delete'] = [];
    }

    /**
     * Get the current uploads in specified format.
     *
     * @access public
     * @param string $format Format of the returned value
     * @return string Current uploads formatted by $format
     */
    public function getValue($format)
    {
        $output = [];
        foreach ($this->session[$this->getOption('uid')] as $fileInfo) {
            $output[] = ($fileInfo['originalBaseUrl'] ?? $fileInfo['base_url']) . $fileInfo['originalName'];
        }
        switch ($format) {
            case 'json' :
                $output = json_encode($output, JSON_UNESCAPED_SLASHES);
                break;
            case 'csv':
            default :
                $output = implode(',', $output);
        }
        return $output;
    }

    /**
     * Clear the current uploads.
     *
     * @access public
     * @return void
     */
    public function clearValue()
    {
        if (isset($this->session[$this->getOption('uid')])) {
            unset($this->session[$this->getOption('uid')]);
            unset($this->session[$this->getOption('uid') . 'config']);
        }
    }

    /**
     * Clear all files in cache older than specified hours.
     *
     * @access public
     * @param integer $hours Specified hours
     * @return void
     */
    public function clearCache($hours = 4)
    {
        $cache = opendir($this->getOption('cachePath'));
        while (false !== ($file = readdir($cache))) {
            $filelastmodified = filemtime($this->getOption('cachePath') . $file);
            if (((time() - $filelastmodified) > ($hours * 3600)) && is_file($this->getOption('cachePath') . $file)) {
                @unlink($this->getOption('cachePath') . $file);
            }
        }
        closedir($cache);
    }
}
