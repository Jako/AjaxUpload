<?php

namespace TreehillStudio\AjaxUpload\FilePond\Helper;

class Post
{
    /**
     * @var string $format
     */
    private string $format;
    /**
     * @var array $values
     */
    private array $values;

    /**
     * @param $entry
     */
    public function __construct($entry)
    {
        if (isset($_FILES[$entry])) {
            $this->values = self::to_array_of_files($_FILES[$entry]);
            $this->format = 'FILE_OBJECTS';
        } elseif (isset($_POST[$entry])) {
            $this->values = $this->to_array($_POST[$entry]);
            if (self::is_encoded_file($this->values[0])) {
                $this->format = 'BASE64_ENCODED_FILE_OBJECTS';
            } else {
                $this->format = 'TRANSFER_IDS';
            }
        }
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $arr
     * @return bool
     */
    public static function is_associative_array(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param $value
     * @return array
     */
    public static function to_array($value): array
    {
        if (is_array($value) && self::is_associative_array($value)) {
            return $value;
        }
        return isset($value) ? [$value] : [];
    }

    /**
     * @param $value
     * @return array
     */
    public static function to_array_of_files($value): array
    {
        if (is_array($value['tmp_name'])) {
            $results = [];
            foreach ($value['tmp_name'] as $index => $tmpName) {
                $file = [
                    'tmp_name' => $value['tmp_name'][$index],
                    'name' => $value['name'][$index],
                    'size' => $value['size'][$index],
                    'error' => $value['error'][$index],
                    'type' => $value['type'][$index]
                ];
                $results[] = $file;
            }
            return $results;
        }
        return self::to_array($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function is_encoded_file($value): bool
    {
        $data = @json_decode($value);
        return is_object($data);
    }
}
