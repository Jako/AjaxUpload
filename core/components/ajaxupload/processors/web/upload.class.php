<?php
/**
 * Upload/Delete images in the upload queue
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\Processors\Processor;

class AjaxUploadUploadProcessor extends Processor
{
    public $languageTopics = ['ajaxupload:default'];

    /**
     * @var array $session
     */
    private $session;

    public function process()
    {
        $this->session =& $_SESSION['ajaxupload'];
        $delete = $this->getProperty('delete', false);
        $uid = htmlspecialchars(trim($this->getProperty('uid', false)));
        $output = '';

        if (!$this->modx->getOption('anonymous_sessions')) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'AjaxUpload needs an active session. But the anonymous_sessions system setting is set to false.', '', 'AjaxUploadUploadProcessor');
            return $output;
        }

        if (isset($this->session[$uid . 'config'])) {
            $this->ajaxupload->initialize($this->session[$uid . 'config']);
            if ($delete !== false) {
                $result = $this->deleteUploads($uid, $delete);
            } else {
                $result = $this->createUploads($uid);
            }
            $output = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        }
        return $output;
    }

    /**
     * @param $uid
     * @param $delete
     * @return array
     */
    private function deleteUploads($uid, $delete)
    {
        $result = [];
        if (strtolower($delete) == 'all') {
            // Delete all uploaded files/thumbs & clean session
            if (is_array($this->session[$uid])) {
                foreach ($this->session[$uid] as $fileInfo) {
                    $fileInfo = $this->deleteFile($fileInfo);
                    $this->session[$uid . 'delete'][] = $fileInfo;
                }
            }
            $this->session[$uid] = [];
            $result['success'] = true;
        } else {
            // Delete one uploaded file/thumb & remove session entry
            $fileId = preg_replace('/[^0-9a-f]/', '', $delete);
            if (isset($this->session[$uid][$fileId])) {
                $fileInfo = $this->session[$uid][$fileId];
                $fileInfo = $this->deleteFile($fileInfo);
                $this->session[$uid . 'delete'][] = $fileInfo;
                unset($this->session[$uid][$fileId]);
                $result['success'] = true;
            } else {
                $result['error'] = $this->modx->lexicon('ajaxupload.notFound', ['maxFiles' => $this->ajaxupload->getOption('maxFiles')]);
            }
        }
        return $result;
    }

    /**
     * @param $uid
     * @return array|bool[]
     */
    private function createUploads($uid)
    {
        // Upload the image(s)
        $uploader = new qqFileUploader($this->ajaxupload->getOption('allowedExtensions'), $this->ajaxupload->getOption('sizeLimit'));
        // To pass data through iframe you will need to encode all html tags
        $lexicon = [
            'notWritable' => $this->modx->lexicon('ajaxupload.notWritable'),
            'noFile' => $this->modx->lexicon('ajaxupload.noFile'),
            'emptyFile' => $this->modx->lexicon('ajaxupload.emptyFile'),
            'largeFile' => $this->modx->lexicon('ajaxupload.largeFile'),
            'wrongExtension' => $this->modx->lexicon('ajaxupload.wrongExtension'),
            'saveError' => $this->modx->lexicon('ajaxupload.saveError')
        ];
        $result = $uploader->handleUpload($this->ajaxupload->getOption('cachePath'), true, $lexicon);

        // File successful uploaded
        if ($result['success']) {
            $fileInfo = [];
            $path = $uploader->path;
            // Check if count of uploaded files are below max file count
            if (count($this->session[$uid]) < $this->ajaxupload->getOption('maxFiles')) {
                $fileInfo['originalBaseUrl'] = $this->ajaxupload->getOption('cachePath');
                $fileInfo['path'] = $path;
                $fileInfo['base_url'] = $this->ajaxupload->getOption('cacheUrl');

                // Create unique filename and set permissions
                $fileInfo['uniqueName'] = md5($uploader->filename . time()) . '.' . $uploader->extension;
                @rename($path . $uploader->filename . '.' . $uploader->extension, $path . $fileInfo['uniqueName']);
                $filePerm = (int)$this->ajaxupload->getOption('newFilePermissions');
                @chmod($path . $fileInfo['uniqueName'], octdec($filePerm));

                $fileInfo['originalName'] = $uploader->filename . '.' . $uploader->extension;

                // Create thumbnail
                $fileInfo['thumbName'] = $this->ajaxupload->generateThumbnail($fileInfo);
                if ($fileInfo['thumbName']) {
                    // Fill session
                    $hash = hash('md5', serialize($fileInfo));
                    $this->session[$uid][$hash] = $fileInfo;
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
                $result['error'] = $this->modx->lexicon('ajaxupload.maxFiles', ['maxFiles' => $this->ajaxupload->getOption('maxFiles')]);
                // Delete uploaded file
                @unlink($path . $uploader->filename . '.' . $uploader->extension);
            }
        }
        return $result;
    }

    /**
     * @param array $fileInfo
     * @return array
     */
    private function deleteFile($fileInfo)
    {
        if (file_exists($fileInfo['path'] . $fileInfo['uniqueName'])) {
            unlink($fileInfo['path'] . $fileInfo['uniqueName']);
            $fileInfo['uniqueName'] = '';
        }
        if (file_exists($fileInfo['path'] . $fileInfo['thumbName'])) {
            unlink($fileInfo['path'] . $fileInfo['thumbName']);
            $fileInfo['thumbName'] = '';
        }
        return $fileInfo;
    }
}

return 'AjaxUploadUploadProcessor';
