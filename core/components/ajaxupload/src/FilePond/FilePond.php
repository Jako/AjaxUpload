<?php

namespace TreehillStudio\AjaxUpload\FilePond;

use Exception;
use TreehillStudio\AjaxUpload\FilePond\Helper\Post;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;

class FilePond
{
    /**
     * @param string $url
     * @return array|false
     */
    public static function fetch(string $url)
    {
        try {
            // Create temp file
            $out = tmpfile();

            // Go!
            $ch = curl_init(str_replace(' ', '%20', $url));
            curl_setopt($ch, CURLOPT_FILE, $out);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);

            if (!curl_exec($ch)) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            return [
                'tmp_name' => stream_get_meta_data($out)['uri'],
                'name' => self::restrict_filename(pathinfo($url)['basename']),
                'type' => $type,
                'length' => $length,
                'error' => $code >= 200 && $code < 300 ? 0 : $code,
                'ref' => $out, // Need this so the file is not automatically removed
            ];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function sanitize_filename(string $filename): string
    {
        $info = pathinfo($filename);
        $name = self::sanitize_filename_part($info['filename']);
        $extension = self::sanitize_filename_part($info['extension']);
        return (strlen($name) > 0 ? $name : '_') . '.' . $extension;
    }

    /**
     * @param $str
     * @return array|string|string[]|null
     */
    public static function sanitize_filename_part($str)
    {
        return preg_replace('/[^a-zA-Z0-9\-_.\s]/', '', $str);
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function restrict_filename(string $filename): string
    {
        $info = pathinfo($filename);
        $name = self::restrict_filename_part($info['filename']);
        $extension = self::restrict_filename_part($info['extension']);
        return (strlen($name) > 0 ? $name : '_') . '.' . $extension;
    }

    /**
     * @param $str
     * @return array|string|string[]|null
     */
    public static function restrict_filename_part($str)
    {
        return preg_replace('/[\0\x0B\t\n\r\f\a&=+%#<>~`@?\[\]\{\}\|^\'\"\\\\\/]/', '', $str);
    }

    /**
     * @param string $path
     * @return void
     */
    public static function remove_directory(string $path)
    {
        if (!is_dir($path)) {
            return;
        }
        $files = glob($path . DIRECTORY_SEPARATOR . '{.,}*', GLOB_BRACE);
        @array_map('unlink', $files);
        @rmdir($path);
    }

    /**
     * @param string $path
     * @param string $id
     * @return void
     */
    public static function remove_transfer_directory(string $path, string $id)
    {
        // Don't remove anything if the transfer id is not valid (just a security precaution)
        if (!self::is_valid_transfer_id($id)) return;

        self::remove_directory($path . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . VARIANTS_DIR);
        self::remove_directory($path . DIRECTORY_SEPARATOR . $id);
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function create_directory(string $path): bool
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @return void
     */
    public static function secure_directory(string $path)
    {
        $content = '# Don\'t list directory contents
IndexIgnore *
# Disable script execution
AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
Options -ExecCGI -Indexes';
        file_put_contents($path . DIRECTORY_SEPARATOR . '.htaccess', $content);
    }

    /**
     * @param string $path
     * @return void
     */
    public static function create_secure_directory(string $path)
    {
        $created = self::create_directory($path);
        if ($created) {
            self::secure_directory($path);
        }
    }

    /**
     * @param string $path
     * @param string $data
     * @param string $filename
     * @return void
     */
    public static function write_file(string $path, string $data, string $filename)
    {
        $handle = fopen($path . DIRECTORY_SEPARATOR . $filename, 'w');
        fwrite($handle, $data);
        fclose($handle);
    }

    /**
     * @param string $str
     * @return bool
     */
    public static function is_url(string $str): bool
    {
        if (!filter_var($str, FILTER_VALIDATE_URL)) return false;
        return in_array(parse_url($str, PHP_URL_SCHEME), ['http', 'https', 'ftp']);
    }

    /**
     * @param array|string $file
     * @return void
     */
    public static function echo_file($file)
    {
        // Read file object
        if (is_string($file)) {
            $file = self::read_file($file);
        }

        // Something went wrong while reading the file
        if (!$file) {
            http_response_code(500);
        }

        // Allow to read Content Disposition (so we can read the file name on the client side)
        header('Access-Control-Expose-Headers: Content-Disposition, Content-Length, X-Content-Transfer-Id');
        header('Content-Type: ' . $file['type']);
        header('Content-Length: ' . $file['length']);
        header('Content-Disposition: inline; filename="' . $file['name'] . '"');
        echo $file['content'] ?? self::read_file_contents($file['tmp_name']);
    }

    /**
     * @param string $filename
     * @return false|mixed
     */
    public static function read_file_contents(string $filename)
    {
        $file = self::read_file($filename);
        if (!$file) return false;
        return $file['content'];
    }

    /**
     * @param string $filename
     * @return array|false
     */
    public static function read_file(string $filename)
    {
        $handle = fopen($filename, 'r');
        if (!$handle) return false;
        $content = fread($handle, filesize($filename));
        fclose($handle);
        if (!$content) return false;
        return [
            'tmp_name' => $filename,
            'name' => basename($filename),
            'content' => $content,
            'type' => mime_content_type($filename),
            'length' => filesize($filename),
            'error' => 0
        ];
    }

    /**
     * @param array $file
     * @param string $path
     * @param bool $sanitize
     * @param bool $overwrite
     * @return false|string
     */
    public static function move_temp_file(array $file, string $path, bool $sanitize = true, bool $overwrite = true)
    {
        $filename = self::restrict_filename($file['name']);
        $filename = ($sanitize) ? self::sanitize_filename($filename) : $filename;
        if (!$overwrite && file_exists($path . DIRECTORY_SEPARATOR . $filename)) {
            return false;
        }
        $filepath = $path . DIRECTORY_SEPARATOR . $filename;
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filepath;
        } else {
            return false;
        }

    }

    /**
     * @param array $file
     * @param string $path
     * @param bool $sanitize
     * @param bool $overwrite
     * @return false|string
     */
    public static function move_file(array $file, string $path, bool $sanitize = true, bool $overwrite = true)
    {
        $filename = ($sanitize) ? self::sanitize_filename($file['name']) : $file['name'];
        if (!$overwrite && file_exists($path . DIRECTORY_SEPARATOR . $filename)) {
            return false;
        }
        if (is_uploaded_file($file['tmp_name'])) {
            return self::move_temp_file($file, $path, $sanitize, $overwrite);
        }
        $filepath = $path . DIRECTORY_SEPARATOR . $filename;
        if (rename($file['tmp_name'], $filepath)) {
            return $filepath;
        } else {
            return false;
        }
    }

    /**
     * @param string $path
     * @param Transfer $transfer
     * @return void
     */
    public static function store_transfer(string $path, Transfer $transfer)
    {
        // Create transfer directory
        $path = $path . DIRECTORY_SEPARATOR . $transfer->getId();
        self::create_secure_directory($path);

        // Store metadata
        if ($transfer->getMetadata()) {
            self::write_file($path, @json_encode($transfer->getMetadata()), METADATA_FILENAME);
        }

        // Store main file if set (if not set, we expect to receive chunks in the near future)
        $files = $transfer->getFiles();

        if ($files === null) return;
        $file = $files[0];
        self::move_file($file, $path, false);

        // Store variants
        if (count($transfer->getFiles()) > 1) {

            $files = array_slice($files, 1);
            $variants = $path . DIRECTORY_SEPARATOR . VARIANTS_DIR;
            self::create_secure_directory($variants);

            foreach ($files as $file) {
                self::move_file($file, $variants, false);
            }
        }
    }

    /**
     * @param string $path
     * @param string $pattern
     * @return array
     */
    public static function get_files(string $path, string $pattern): array
    {
        $results = [];
        $files = glob($path . DIRECTORY_SEPARATOR . $pattern);
        foreach ($files as $file) {
            $results[] = self::create_file_object($file);
        }
        return $results;
    }

    /**
     * @param string $path
     * @param string $pattern
     * @return mixed|null
     */
    public static function get_file(string $path, string $pattern)
    {
        $result = self::get_files($path, $pattern);
        if (count($result) > 0) {
            return $result[0];
        }
        return null;
    }

    /**
     * @param string $filename
     * @return array
     */
    public static function create_file_object(string $filename): array
    {
        return [
            'tmp_name' => $filename,
            'name' => basename($filename),
            'type' => mime_content_type($filename),
            'length' => filesize($filename),
            'error' => 0
        ];
    }

    /**
     * @param string $id
     * @return false|int
     */
    public static function is_valid_transfer_id(string $id)
    {
        return preg_match('/^[0-9a-fA-F]{32}$/', $id);
    }

    /**
     * @param string $path
     * @param string $id
     * @return false|Transfer
     */
    public static function get_transfer(string $path, string $id)
    {
        if (!self::is_valid_transfer_id($id)) {
            return false;
        }

        $transfer = new Transfer($id);
        $path = $path . DIRECTORY_SEPARATOR . $id;
        $file = self::get_file($path, '*.*');
        $metadata = self::get_file($path, METADATA_FILENAME);
        $variants = self::get_files($path . DIRECTORY_SEPARATOR . VARIANTS_DIR, '*.*');
        $transfer->restore($file, $variants, [], $metadata);

        return $transfer;
    }

    /**
     * @param string $entry
     * @return false|Post
     */
    public static function get_post(string $entry)
    {
        return isset($_FILES[$entry]) || isset($_POST[$entry]) ? new Post($entry) : false;
    }
}
