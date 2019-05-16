<?php
/**
 * Upload/Delete images in the upload queue
 *
 * @package colorpicker
 * @subpackage processor
 */

class AjaxUploadUploadProcessor extends modProcessor
{
    public $languageTopics = array('colorpicker:default');
    
    public function process()
    {
        $delete = $this->getProperty('delete', false);
        $uid = htmlspecialchars(trim($this->getProperty('uid', false)));
        $output = '';

        if (isset($_SESSION['ajaxupload'][$uid . 'config'])) {
            $ajaxupload = new AjaxUpload($this->modx, $_SESSION['ajaxupload'][$uid . 'config']);
            $ajaxupload->initialize($_SESSION['ajaxupload'][$uid . 'config']);

            $result = array();
            if ($delete !== false) {
                if (strtolower($delete) == 'all') {
                    // Delete all uploaded files/thumbs & clean session
                    if (is_array($_SESSION['ajaxupload'][$uid])) {
                        foreach ($_SESSION['ajaxupload'][$uid] as $fileInfo) {
                            if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
                                unlink($fileInfo['path'] . $fileInfo['uniqueName']);
                                $fileInfo['uniqueName'] = '';
                            }
                            if (file_exists($fileInfo['path'] . $fileInfo['thumbName'])) {
                                unlink($fileInfo['path'] . $fileInfo['thumbName']);
                                $fileInfo['thumbName'] = '';
                            }
                            $_SESSION['ajaxupload'][$uid . 'delete'][] = $fileInfo;
                        }
                    }
                    $_SESSION['ajaxupload'][$uid] = array();
                    $result['success'] = true;
                } else {
                    // Delete one uploaded file/thumb & remove session entry
                    $fileId = preg_replace('/[^0-9a-f]/', '', $delete);
                    if (isset($_SESSION['ajaxupload'][$uid][$fileId])) {
                        $fileInfo = $_SESSION['ajaxupload'][$uid][$fileId];
                        if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
                            unlink($fileInfo['path'] . $fileInfo['uniqueName']);
                            $fileInfo['uniqueName'] = '';
                        }
                        if (file_exists($fileInfo['path'] . $fileInfo['thumbName'])) {
                            unlink($fileInfo['path'] . $fileInfo['thumbName']);
                            $fileInfo['thumbName'] = '';
                        }
                        $_SESSION['ajaxupload'][$uid . 'delete'][] = $fileInfo;
                        unset($_SESSION['ajaxupload'][$uid][$fileId]);
                        $result['success'] = true;
                    } else {
                        $result['error'] = $this->modx->lexicon('ajaxupload.notFound', array('maxFiles' => $ajaxupload->config['maxFiles']));
                    }
                }
            } else {
                // Upload the image(s)
                $uploader = new qqFileUploader($ajaxupload->config['allowedExtensions'], $ajaxupload->config['sizeLimit']);
                // To pass data through iframe you will need to encode all html tags
                $lexicon = array(
                    'notWritable' => $this->modx->lexicon('ajaxupload.notWritable'),
                    'noFile' => $this->modx->lexicon('ajaxupload.noFile'),
                    'emptyFile' => $this->modx->lexicon('ajaxupload.emptyFile'),
                    'largeFile' => $this->modx->lexicon('ajaxupload.largeFile'),
                    'wrongExtension' => $this->modx->lexicon('ajaxupload.wrongExtension'),
                    'saveError' => $this->modx->lexicon('ajaxupload.saveError')
                );
                $result = $uploader->handleUpload($ajaxupload->config['cachePath'], true, $this->modx->lexicon->fetch('ajaxupload.', true));

                // File successful uploaded
                if ($result['success']) {
                    $fileInfo = array();
                    $path = $uploader->path;
                    // Check if count of uploaded files are below max file count
                    if (count($_SESSION['ajaxupload'][$uid]) < $ajaxupload->config['maxFiles']) {
                        $fileInfo['originalBaseUrl'] = $ajaxupload->config['cachePath'];
                        $fileInfo['path'] = $path;
                        $fileInfo['base_url'] = $ajaxupload->config['cacheUrl'];

                        // Create unique filename and set permissions
                        $fileInfo['uniqueName'] = md5($uploader->filename . time()) . '.' . $uploader->extension;
                        @rename($path . $uploader->filename . '.' . $uploader->extension, $path . $fileInfo['uniqueName']);
                        $filePerm = (int)$ajaxupload->config['newFilePermissions'];
                        @chmod($path . $fileInfo['uniqueName'], octdec($filePerm));

                        $fileInfo['originalName'] = $uploader->filename . '.' . $uploader->extension;

                        // Create thumbnail
                        $fileInfo['thumbName'] = $ajaxupload->generateThumbnail($fileInfo);
                        if ($fileInfo['thumbName']) {
                            // Fill session
                            $hash = hash('md5', serialize($fileInfo));
                            $_SESSION['ajaxupload'][$uid][$hash] = $fileInfo;
                            // Prepare returned values (filename, originalName & fileid)
                            $result['filename'] = $fileInfo['base_url'] . $fileInfo['thumbName'];
                            $result['originalName'] = $fileInfo['originalName'];
                            $result['fileid'] = $hash;
                        } else {
                            unset($result['success']);
                            $result['error'] = $this->modx->lexicon('ajaxupload.thumbnailGenerationProblem');
                            @unlink($path . $fileInfo['uniqueName']);
                        }
                    } else {
                        unset($result['success']);
                        // Error message
                        $result['error'] = $this->modx->lexicon('ajaxupload.maxFiles', array('maxFiles' => $ajaxupload->config['maxFiles']));
                        // Delete uploaded file
                        @unlink($path . $uploader->filename . '.' . $uploader->extension);
                    }
                }
            }
            $output = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        }
        return $output;
    }
}

return 'AjaxUploadUploadProcessor';
