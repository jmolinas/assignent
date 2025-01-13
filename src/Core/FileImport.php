<?php

class FileImport
{
    protected $folderPath;
    protected $extension;

    public function __construct($extension)
    {
        $this->extension = $extension;
        $this->folderPath = __DIR__ . '/../../import';
    }

    public function scanFolder()
    {
        // Create an array to store file paths
        $fileList = [];

        // Set up a Recursive Directory Iterator
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->folderPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        // Loop through each file and add its path to the list
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() == $this->extension) { // Ensure it’s a file, not a directory
                $fileList[] = $file->getPathname();
            }
        }

        return $fileList;
    }
}
