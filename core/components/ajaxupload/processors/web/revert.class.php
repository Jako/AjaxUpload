<?php
/**
 * Revert uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadRevertProcessor extends FilePondProcessor
{
    public function process()
    {
        // Check the file id
        $id = file_get_contents('php://input');
        if (empty($id) || !FilePond::is_valid_transfer_id($id)) {
            return $this->filePondFailure();
        }

        // Remove transfer directory
        FilePond::remove_transfer_directory(TRANSFER_DIR, $id);

        // No content to return
        return $this->filePondSuccess('', 204);
    }
}

return 'AjaxUploadRevertProcessor';
