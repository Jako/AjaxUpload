<?php
/**
 * Restore uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadRestoreProcessor extends FilePondProcessor
{
    public function process()
    {
        // Check the file id
        $id = $this->getProperty('id');
        if (empty($id) || !FilePond::is_valid_transfer_id($id)) {
            return $this->filePondFailure();
        }

        // Create transfer wrapper around upload
        $transfer = FilePond::get_transfer(TRANSFER_DIR, $id);

        // Get the temp file content
        $files = $transfer->getFiles();

        // No file returned
        if (count($files) === 0) {
            return $this->filePondFailure('',404);
        }

        // Return file
        FilePond::echo_file($files[0]);
        @session_write_close();
        exit;
    }
}

return 'AjaxUploadRestoreProcessor';
