<?php

namespace TreehillStudio\AjaxUpload\FilePond\Helper;

class Transfer
{
    /**
     * @var string $id
     */
    private string $id;
    /**
     * @var null|array $file
     */
    private ?array $file = null;
    /**
     * @var null|array $chunks
     */
    private ?array $chunks = [];
    /**
     * @var null|array $variants
     */
    private ?array $variants = [];
    /**
     * @var null|array $metadata
     */
    private ?array $metadata = [];

    /**
     * @param false|string $id
     */
    public function __construct($id = false)
    {
        $this->id = $id ?: UniqueIdDispenser::dispense();
    }

    /**
     * @return bool|string
     */
    public function getid()
    {
        return $this->id;
    }

    /**
     * @return null|array
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @return null|array
     */
    public function getChunks(): ?array
    {
        return $this->chunks;
    }

    /**
     * @param $mutator
     * @return array|mixed|null[]|null
     */
    public function getFiles($mutator = null)
    {
        if ($this->file === null) {
            return null;
        }
        $files = array_merge([$this->file], $this->variants ?? []);
        return $mutator === null ? $files : call_user_func($mutator, $files, $this->metadata);
    }

    /**
     * @param array|null $file
     * @param array|null $variants
     * @param array|null $chunks
     * @param array|null $metadata
     */
    public function restore(?array $file, ?array $variants = [], ?array $chunks = [], ?array $metadata = [])
    {
        $this->file = $file;
        $this->variants = $variants;
        $this->chunks = $chunks;
        $this->metadata = $metadata;
    }

    /**
     * @param $entry
     */
    public function populate($entry)
    {
        $files = isset($_FILES[$entry]) ? Post::to_array_of_files($_FILES[$entry]) : null;
        $metadata = isset($_POST[$entry]) ? Post::to_array($_POST[$entry]) : [];

        // Parse metadata
        if (count($metadata)) {
            $this->metadata = is_string($metadata[0]) ? (@json_decode($metadata[0], true) ?? []) : [];
        }

        // No files
        if ($files === null) {
            return;
        }

        // Files should always be available, first file is always the main file
        $this->file = $files[0];

        // If variants submitted, set to variants array
        $this->variants = array_slice($files, 1);
    }
}
