<?php

/**
 * AjaxUpload
 *
 * Copyright 2013-2016 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ajaxupload
 * @subpackage classfile
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
     * The version
     * @var string $version
     */
    public $version = '1.5.1';

    /**
     * A configuration array
     * @var array $config
     */
    public $config = array();

    /**
     * An array of debug informations
     * @var array $debug
     */
    public $debug;

    /**
     * CustomRequest constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $config An array of configuration options. Optional.
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx = &$modx;

        $corePath = $this->getOption('core_path', $config, $this->modx->getOption('core_path') . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $config, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $config, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->config = array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'cachePath' => $assetsPath . 'cache/',
            'cacheUrl' => $assetsUrl . 'cache/'
        );

        // Set parameters
        $resourceId = ($this->modx->resource) ? $this->modx->resource->get('id') : 0;
        $this->config = array_merge($this->config, array(
            'debug' => false,
            'uid' => $this->getOption('uid', $config, md5($this->modx->getOption('site_url') . '-' . $resourceId)),
            'uploadAction' => $assetsUrl . 'connector.php',
            'newFilePermissions' => '0664',
            'maxConnections' => 1,
            'cacheExpires' => intval($this->getOption('cacheExpires', $config, 4))
        ));
        $this->debug = array();
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
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Load all class files and init defaults.
     *
     * @param array $properties properties to override the default config (if set)
     * @access public
     * @return boolean success state of initialization
     */
    public function initialize($properties = array())
    {
        if (!$this->modx->getService('smarty', 'smarty.modSmarty')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load modSmarty service.', '', 'AjaxUpload');
            $this->debug[] = 'Could not load modSmarty service.';
            return false;
        }
        if (!$this->modx->loadClass('modPhpThumb', $this->modx->getOption('core_path') . 'model/phpthumb/', true, true)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load modPhpThumb class.', '', 'AjaxUpload');
            $this->debug[] = 'Could not load modPhpThumb class.';
            return false;
        }
        if (!class_exists('qqFileUploader')) {
            include_once $this->config['modelPath'] . 'fileuploader/fileuploader.class.php';
        }
        $language = empty($this->config['language']) ? '' : $this->config['language'] . ':';
        $this->modx->lexicon->load($language . 'ajaxupload:default');
        if (!isset($_SESSION['ajaxupload'][$this->config['uid']])) {
            $_SESSION['ajaxupload'][$this->config['uid']] = array();
        }
        if (!isset($_SESSION['ajaxupload'][$this->config['uid'] . 'delete'])) {
            $_SESSION['ajaxupload'][$this->config['uid'] . 'delete'] = array();
        }
        if (count($properties)) {
            $allowedExtensions = $this->modx->getOption('allowedExtensions', $properties, 'jpg,jpeg,png,gif');
            $allowedExtensions = (!is_array($allowedExtensions)) ? explode(',', $allowedExtensions) : $allowedExtensions;
            $config = array(
                'allowedExtensions' => $allowedExtensions,
                'allowedExtensionsString' => (!empty($allowedExtensions)) ? "'" . implode("','", $allowedExtensions) . "'" : '',
                'sizeLimit' => $this->modx->getOption('sizeLimit', $properties, $this->modx->getOption('maxFilesizeMb', $properties, 8) * 1024 * 1024),
                'maxFiles' => (integer)$this->modx->getOption('maxFiles', $properties, 3),
                'thumbX' => (integer)$this->modx->getOption('thumbX', $properties, 100),
                'thumbY' => (integer)$this->modx->getOption('thumbY', $properties, 100),
                'addJquery' => (bool)$this->modx->getOption('addJquery', $properties, false),
                'addJscript' => $this->modx->getOption('addJscript', $properties, true),
                'addCss' => $this->modx->getOption('addCss', $properties, true),
                'debug' => (bool)$this->getOption('debug', $properties, false)
            );
            $this->config = array_merge($this->config, $config);
            $_SESSION['ajaxupload'][$this->config['uid'] . 'config'] = $this->config;
        }
        if (!@is_dir($this->config['cachePath'])) {
            if (!@mkdir($this->config['cachePath'], 0755)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not create the cache path.', '', 'AjaxUpload');
            };

        }
        $this->clearCache($this->config['cacheExpires']);
        return true;
    }

    /**
     * Preload file list for display if uploaded files exist.
     *
     * @access private
     * @param array $files An array of already uploaded files.
     * @return string html file list to prefill the template
     */
    private function loadFiles(&$files = array())
    {
        $itemList = array();

        foreach ($files as $id => $fileInfo) {
            if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
                $this->modx->smarty->assign('fileid', $id);
                $files[$id]['thumbName'] = $this->generateThumbnail($fileInfo);
                $this->modx->smarty->assign('thumbName', $fileInfo['base_url'] . $fileInfo['thumbName']);
                $itemList[] = $this->modx->smarty->fetch($this->config['templatesPath'] . 'web/image.tpl');
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
    public function generateThumbnail($fileInfo = array())
    {
        if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
            if (!isset($fileInfo['thumbName'])) {
                $path_info = pathinfo($fileInfo['uniqueName']);
                $thumbOptions = array();
                if (in_array(strtolower($path_info['extension']), array('jpg', 'jpeg', 'png', 'gif'))) {
                    $thumbOptions['src'] = $fileInfo['path'] . $fileInfo['uniqueName'];
                    if ($this->config['thumbX']) {
                        $thumbOptions['w'] = $this->config['thumbX'];
                    }
                    if ($this->config['thumbY']) {
                        $thumbOptions['h'] = $this->config['thumbY'];
                    }
                    if ($this->config['thumbX'] && $this->config['thumbY']) {
                        $thumbOptions['zc'] = '1';
                    }
                } else {
                    $thumbOptions['src'] = $this->config['assetsPath'] . '/images/generic.png';
                    $thumbOptions['aoe'] = '1';
                    $thumbOptions['fltr'] = array('wmt|' . strtoupper($path_info['extension']) . '|5|C|000000');
                    if ($this->config['thumbX']) {
                        $thumbOptions['w'] = $this->config['thumbX'];
                    }
                    if ($this->config['thumbY']) {
                        $thumbOptions['h'] = $this->config['thumbY'];
                    }
                    if ($this->config['thumbX'] && $this->config['thumbY']) {
                        $thumbOptions['zc'] = '1';
                    }
                    $thumbOptions['f'] = 'png';
                    $path_info['extension'] = 'png';
                }
                $thumbName = md5($path_info['basename'] . time() . '.thumb') . '.' . $path_info['extension'];

                // generate Thumbnail & save it
                $phpThumb = new modPhpThumb($this->modx, $thumbOptions);
                $phpThumb->initialize();
                if ($phpThumb->GenerateThumbnail()) {
                    if (!$phpThumb->RenderToFile($fileInfo['path'] . $thumbName)) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Thumbnail generation: Thumbnail not saved.' . "\nDebugmessages:\n" . implode("\n", $phpThumb->debugmessages), '', 'AjaxUpload');
                        $this->debug[] = 'Thumbnail generation: Thumbnail not saved.' . "\nDebugmessaes:\n" . implode("\n", $phpThumb->debugmessages);
                    } else {
                        $filePerm = (int)$this->config['newFilePermissions'];
                        if (!@chmod($fileInfo['path'] . $thumbName, octdec($filePerm))) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not change the thumbnail file permission.', '', 'AjaxUpload');
                        };
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Thumbnail generation: Thumbnail not created.' . "\nDebugmessages:\n" . implode("\n", $phpThumb->debugmessages), '', 'AjaxUpload');
                    $this->debug[] = 'Thumbnail generation: Thumbnail not created.' . "\nDebugmessaes:\n" . implode("\n", $phpThumb->debugmessages);
                }
                $fileInfo['thumbName'] = $thumbName;
            }
            return $fileInfo['thumbName'];
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Thumbnail generation: Original file not found.', '', 'AjaxUpload');
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
    public function retrieveUploads($files = array())
    {
        foreach ($files as $file) {
            $file = str_replace($this->modx->getOption('assets_url'), '', '/' . ltrim($file, '/'));
            $pathinfo = pathinfo($file);
            if (file_exists($this->modx->getOption('assets_path') . $file)) {
                $fileInfo = array();

                // Get original file info
                $originalName = $pathinfo['basename'];
                $originalExtension = $pathinfo['extension'];
                $originalFilename = (isset($pathinfo['filename'])) ? $pathinfo['filename'] : substr($originalName, 0, strrpos($originalName, '.'));
                $path = $this->modx->getOption('assets_path') . $pathinfo['dirname'] . '/';

                // Prepare session file info
                $fileInfo['originalName'] = $originalName;
                $fileInfo['originalPath'] = $path;
                $fileInfo['originalBaseUrl'] = $this->modx->getOption('assets_url');
                $fileInfo['path'] = $this->config['cachePath'];
                $fileInfo['base_url'] = $this->config['cacheUrl'];

                // Check if file is already in session
                $found = false;
                foreach ($_SESSION['ajaxupload'][$this->config['uid']] as $sessionInfo) {
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
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not copy the uploaded file to the upload cache.', '', 'AjaxUpload');
                };
                $filePerm = (int)$this->config['newFilePermissions'];
                if (!@chmod($fileInfo['path'] . $fileInfo['uniqueName'], octdec($filePerm))) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not change the uploaded file permission in the upload cache.', '', 'AjaxUpload');
                };

                // create thumbnail
                $fileInfo['thumbName'] = $this->generateThumbnail($fileInfo);
                if ($fileInfo['thumbName']) {
                    // fill session
                    if (!$found) {
                        $_SESSION['ajaxupload'][$this->config['uid']][] = $fileInfo;
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Thumbnail generation: Original file not found.', '', 'AjaxUpload');
                    $this->debug[] = 'Thumbnail generation: Original file not found';
                    @unlink($fileInfo['path'] . $fileInfo['uniqueName']);
                }
            } else {
                // Check if not found file is in session and delete the unique file and the thumbnail
                foreach ($_SESSION['ajaxupload'][$this->config['uid']] as $sessionInfo) {
                    if ($sessionInfo['originalName'] === $pathinfo['basename']) {
                        @unlink($this->config['cachePath'] . $sessionInfo['uniqueName']);
                        @unlink($this->config['cachePath'] . $sessionInfo['thumbName']);
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
     * @return boolean|string
     */
    public function saveUploads($target)
    {
        $errors = false;
        foreach ($_SESSION['ajaxupload'][$this->config['uid']] as &$fileInfo) {
            if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
                if (!@copy($fileInfo['path'] . $fileInfo['uniqueName'], $this->modx->getOption('assets_path') . $target . $fileInfo['originalName'])) {
                    $errors = $this->modx->lexicon('ajaxupload.targetNotWritable');
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $errors, '', 'AjaxUpload');
                } else {
                    $fileInfo['originalPath'] = $this->modx->getOption('assets_path') . $target;
                    $fileInfo['originalBaseUrl'] = $this->modx->getOption('assets_url') . $target;
                }
            }
        }
        return $errors;
    }

    /**
     * Delete existing files in target that are deleted in $_SESSION.
     *
     * @access public
     * @return void
     */
    public function deleteExisting()
    {
        foreach ($_SESSION['ajaxupload'][$this->config['uid'] . 'delete'] as &$fileInfo) {
            if (isset($fileInfo['originalPath']) && file_exists($fileInfo['originalPath'] . $fileInfo['originalName'])) {
                @unlink($fileInfo['originalPath'] . $fileInfo['originalName']);
            }
        }
        $_SESSION['ajaxupload'][$this->config['uid'] . 'delete'] = array();
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
        $output = array();
        foreach ($_SESSION['ajaxupload'][$this->config['uid']] as $fileInfo) {
            $output[] = (isset($fileInfo['originalBaseUrl']) ? $fileInfo['originalBaseUrl'] : $fileInfo['base_url']) . $fileInfo['originalName'];
        }
        switch ($format) {
            case 'json' :
                $output = json_encode($output);
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
     * @param void
     * @return void
     */
    public function clearValue()
    {
        if (isset($_SESSION['ajaxupload'][$this->config['uid']])) {
            unset($_SESSION['ajaxupload'][$this->config['uid']]);
            unset($_SESSION['ajaxupload'][$this->config['uid'] . 'config']);
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
        $cache = opendir($this->config['cachePath']);
        while (false !== ($file = readdir($cache))) {
            $filelastmodified = filemtime($this->config['cachePath'] . $file);
            if ((time() - $filelastmodified) > $hours * 3600 && is_file($this->config['cachePath'] . $file)) {
                @unlink($this->config['cachePath'] . $file);
            }
        }
        closedir($cache);
    }

    /**
     * Output the form inputs.
     *
     * @access public
     * @return string The output
     */
    public function output()
    {
        $assetsUrl = $this->getOption('assetsUrl');
        $jsUrl = $this->getOption('jsUrl') . 'web/';
        $jsSourceUrl = $assetsUrl . '../../../source/js/web/';
        $cssUrl = $this->getOption('cssUrl') . 'web/';
        $cssSourceUrl = $assetsUrl . '../../../source/css/web/';

        if ($this->config['addJquery']) {
            $this->modx->regClientScript('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
        }
        if ($this->config['addCss']) {
            if ($this->getOption('debug') && ($assetsUrl != MODX_ASSETS_URL . 'components/' . $this->namespace . '/')) {
                $this->modx->regClientCSS($cssSourceUrl . 'ajaxupload.css');
            } else {
                $this->modx->regClientCSS($cssUrl . 'ajaxupload.min.css');
            }
        }
        if ($this->config['addJscript']) {
            if ($this->getOption('debug') && ($assetsUrl != MODX_ASSETS_URL . 'components/' . $this->namespace . '/')) {
                $this->modx->regClientScript($jsSourceUrl . 'fileuploader.js');
                $this->modx->regClientScript($jsSourceUrl . 'ajaxupload.js');
            } else {
                $this->modx->regClientScript($jsUrl . 'ajaxupload.min.js');
            }
        }
        $this->modx->smarty->assign('_lang', $this->modx->lexicon->fetch('ajaxupload.', true));
        $this->modx->smarty->assign('params', $this->config);
        $this->modx->regClientScript($this->modx->smarty->fetch($this->config['templatesPath'] . 'web/script.tpl'), true);

        // preload files from $_SESSION
        $itemList = '';
        if (is_array($_SESSION['ajaxupload'][$this->config['uid']])) {
            $itemList = $this->loadFiles($_SESSION['ajaxupload'][$this->config['uid']]);
        }
        $this->modx->smarty->assign('items', $itemList);
        return $this->modx->smarty->fetch($this->config['templatesPath'] . 'web/uploadSection.tpl');
    }

    /**
     * Output debug informations.
     *
     * @access public
     * @return string The debug output
     */
    public function debugOutput()
    {
        if ($this->config['debug']) {
            $this->debug[] = '$_SESSION["ajaxupload"]:<pre>' . print_r($_SESSION['ajaxupload'][$this->config['uid']], true) . '</pre>';
        }
        return implode('<br/>', $this->debug);
    }

}
