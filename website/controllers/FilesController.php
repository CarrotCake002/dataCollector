<?php

namespace classes;

class FilesController {

    public $folder;
    public $file_list;

    public function __construct($folder_name) {
        $this->folder = $folder_name;
        $this->file_list = array_values(array_diff(scandir('../../savefiles/' . $folder_name), array('.', '..')));
    }

    public function getFileListSize() {
        return count($this->file_list);
    }

    public function getFileName($fileNb) {
        if (isset($this->file_list[$fileNb]));
            return $this->file_list[$fileNb];
        return false;
    }

    public function getFileRelativePath($fileNb) {
        $filename = $this->getFileName($fileNb);
        if (!$filename)
            return false;
        return '../../savefiles/' . $this->folder . '/' . $filename;
    }

    public function getFileSize($fileNb) {
        $filedir = $this->getFileRelativePath($fileNb);
        if (!$filedir)
            return false;
        return filesize($filedir) / 1000;
    }

    public function getFileLastUpdate($fileNb) {
        $filepath = $this->getFileRelativePath($fileNb);
        if (!$filepath)
            return false;
        return date("d/m/y H:i:s", filemtime($filepath));
    }
}