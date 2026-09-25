<?php
/**
 * Load uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadLoadProcessor extends FilePondProcessor
{
    public function process()
    {
        // Check the ref
        $ref = $this->getProperty('ref');
        if (empty($ref)) {
            $this->filePondFailure();
        }

        // Get and validate the file path for directory traversal
        $baseDir = realpath(UPLOAD_DIR . DIRECTORY_SEPARATOR);
        $path = realpath($baseDir . FilePond::restrict_filename($ref));
        if ($path === false || strpos($path, $baseDir) !== 0) {
            $this->filePondFailure();
        }

        // Return file
        FilePond::echo_file($path);
        @session_write_close();
        exit;
    }
}

return 'AjaxUploadLoadProcessor';
