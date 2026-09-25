<?php
/**
 * Fetch uploaded files
 *
 * @package ajaxupload
 * @subpackage processors
 */

use TreehillStudio\AjaxUpload\FilePond\FilePond;
use TreehillStudio\AjaxUpload\FilePond\Helper\Transfer;
use TreehillStudio\AjaxUpload\Processors\FilePondProcessor;

class AjaxUploadFetchProcessor extends FilePondProcessor
{
    public function process()
    {
        // Stop here if no data supplied
        $url = $this->getProperty('url');
        if (empty($url)) {
            $this->filePondFailure();
        }

        // Check for denied file extensions
        $fileExtension = pathinfo($url, PATHINFO_EXTENSION);
        if ($this->isDeniedExtension($fileExtension)) {
            $this->filePondFailure();
        }

        // Is this a valid url
        if (FilePond::is_url($url)) {
            $url = self::traceAndValidateUrl($url);
            if (empty($url)) {
                $this->filePondFailure();
            }
        } else {
            $this->filePondFailure();
        }

        // Let's try to get the remote file content
        $file = FilePond::fetch($url);

        // Something went wrong
        if (!$file) {
            $this->filePondFailure();
        }

        // Remote server returned invalid response
        if ($file['error'] !== 0) {
            $this->filePondFailure($file['error']);
        }

        // Check for the MIME type of uploaded files
        $hasContent = (isset($file['tmp_name']) && is_file($file['tmp_name'])) || isset($file['content']);
        if ($hasContent) {
            if (isset($file['tmp_name']) && is_file($file['tmp_name'])) {
                $mimeType = $this->finfoMimeTypeDetector->detectMimeTypeFromFile($file['tmp_name']);
            } elseif (isset($file['content'])) {
                $mimeType = $this->finfoMimeTypeDetector->detectMimeTypeFromBuffer($file['content']);
            } else {

                $this->filePondFailure();
            }
            if (!$this->isValidMimeType($mimeType)) {
                if (isset($file['tmp_name']) && is_file($file['tmp_name'])) {
                    @unlink($file['tmp_name']);
                }
                $this->filePondFailure();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            // No content and no HEAD request? That's invalid.
            $this->filePondFailure();
        }

        // If we only return headers we store the file in the transfer folder
        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            // Deal with this file as if it's a file transfer, will return unique id to client
            $transfer = new Transfer();
            $transfer->restore($file);
            FilePond::store_transfer(TRANSFER_DIR, $transfer);

            // Send transfer id back to client
            header('X-Content-Transfer-Id: ' . $transfer->getId());
        }

        // Return the file to the client
        FilePond::echo_file($file);
        @session_write_close();
        exit;
    }

    /**
     * Trace a URL redirection chain and only follow it if each step is secure.
     * Protects against: Loopback, Private IPs, Link-Local IPs, and DNS Rebinding.
     */
    public static function traceAndValidateUrl(string $url, int $maxRedirects = 5)
    {
        $currentUrl = $url;
        $redirectCount = 0;

        while ($redirectCount <= $maxRedirects) {
            $parts = parse_url($currentUrl);
            if (!$parts || !isset($parts['scheme']) || !isset($parts['host'])) {
                return false;
            }

            // Only allow HTTP and HTTPS
            $scheme = strtolower($parts['scheme']);
            if (!in_array($scheme, ['http', 'https'], true)) {
                return false;
            }

            // Remove IPv6 brackets
            $host = trim($parts['host'], '[]');

            // Get the port
            $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

            // DNS Resolution (Prevents DNS Rebinding by locking the resolved IP)
            $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
            if (!$ip || $ip === $host) {
                return false;
            }

            // IP Security Inspections
            if (self::isLoopbackIp($ip)) {
                return false;
            }
            if (self::isLinkLocalIp($ip)) {
                return false;
            }
            if (self::isPrivateIp($ip)) {
                return false;
            }

            // Send HTTP request directly to the IP (forces cURL to use the validated IP)
            $ch = curl_init();
            $requestUrl = $scheme . '://' . ($parts['v6'] ?? $ip) . ':' . $port . ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
            $resolveRule = "{$host}:{$port}:{$ip}";

            curl_setopt_array($ch, [
                CURLOPT_URL => $requestUrl,
                CURLOPT_RESOLVE => [$resolveRule],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => true, // HEAD request only (saves bandwidth)
                CURLOPT_FOLLOWLOCATION => false, // IMPORTANT: We handle redirects manually!
                CURLOPT_TIMEOUT => 4,
                CURLOPT_HTTPHEADER => ["Host: $host"], // Required for Named Virtual Hosts
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            // If the IP is IPv6, instruct cURL accordingly
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
            }

            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            curl_close($ch);

            if ($response === false) {
                return false;
            }

            // Evaluate the HTTP status code
            if ($statusCode >= 300 && $statusCode < 400 && !empty($redirectUrl)) {
                // Resolve relative redirect paths (e.g., Location: /index.php)
                if (parse_url($redirectUrl, PHP_URL_HOST) === null) {
                    $redirectUrl = $scheme . '://' . $host . ':' . $port . '/' . ltrim($redirectUrl, '/');
                }

                $currentUrl = $redirectUrl;
                $redirectCount++;
            } else {
                // No more redirects -> target reached safely!
                return $currentUrl;
            }
        }

        // Too many redirects
        return false;
    }

    private static function isLoopbackIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return strpos($ip, '127.') === 0;
        }
        return $ip === '::1' || $ip === '0000:0000:0000:0000:0000:0000:0000:0001';
    }

    private static function isLinkLocalIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return strpos($ip, '169.254.') === 0;
        }
        $hex = unpack('H*', inet_pton($ip));
        return $hex && in_array(substr($hex[1], 0, 3), ['fe8', 'fe9', 'fea', 'feb'], true);
    }

    private static function isPrivateIp(string $ip): bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
    }
}

return 'AjaxUploadFetchProcessor';
