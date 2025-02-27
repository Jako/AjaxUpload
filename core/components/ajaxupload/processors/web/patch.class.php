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
        $id = $this->getProperty('id');

        // Location of patch files
        $dir = TRANSFER_DIR . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;

        // Exit if is get
        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            $patch = glob($dir . '.patch.*');
            $offsets = [];
            $size = '';
            $last_offset = 0;
            foreach ($patch as $filename) {

                // Get size of chunk
                $size = filesize($filename);

                // Get offset of chunk
                list($dir, $offset) = explode('.patch.', $filename, 2);

                // Offsets
                array_push($offsets, $offset);

                // Test if it is missing a previous chunk
                // Don't test first chunk (previous chunk is non-existent)
                if ($offset > 0 && !in_array($offset - $size, $offsets)) {
                    $last_offset = $offset - $size;
                    break;
                }

                // Last offset is at least next offset
                $last_offset = $offset + $size;
            }

            // Return offset
            header('Upload-Offset: ' . $last_offset);
            return $this->filePondSuccess();
        }

        // Get patch data
        $offset = $_SERVER['HTTP_UPLOAD_OFFSET'];
        $length = $_SERVER['HTTP_UPLOAD_LENGTH'];

        // Should be numeric values, else exit
        if (!is_numeric($offset) || !is_numeric($length)) {
            return $this->filePondFailure();
        }

        // Get sanitized name
        $name = FilePond::restrict_filename($_SERVER['HTTP_UPLOAD_NAME']);

        // Write patch file for this request
        file_put_contents($dir . '.patch.' . $offset, fopen('php://input', 'r'));

        // Calculate total size of patches
        $size = 0;
        $patch = glob($dir . '.patch.*');
        foreach ($patch as $filename) {
            $size += filesize($filename);
        }

        // If total size equals length of file we have gathered all patch files
        if ($size == $length) {

            // Create output file
            $file_handle = fopen($dir . $name, 'w');

            // Write patches to file
            foreach ($patch as $filename) {

                // Get offset from filename
                list($dir, $offset) = explode('.patch.', $filename, 2);

                // Read patch and close
                $patch_handle = fopen($filename, 'r');
                $patch_contents = fread($patch_handle, filesize($filename));
                fclose($patch_handle);

                // Apply patch
                fseek($file_handle, $offset);
                fwrite($file_handle, $patch_contents);
            }

            // Remove patches
            foreach ($patch as $filename) {
                unlink($filename);
            }

            // Done with file
            fclose($file_handle);
        }

        return $this->filePondSuccess('', 204);
    }
}

return 'AjaxUploadLoadProcessor';
