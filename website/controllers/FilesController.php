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

    public function deleteFile($fileNb) {
        return unlink($this->getFileRelativePath($fileNb));
    }

    private function isFileCorrect($fileNb) {
        $filePath = $this->getFileRelativePath($fileNb);
        @ $json_data = file_get_contents($filePath);
        if ($json_data === false) {
            return null;
        }
        $json_data = json_decode($json_data, true);
        if ($json_data === null) {
            return false;
        }
        return true;
    }

    public function getFileStatus($fileNb) {
        $fileDate = date_create(date("d-m-y G:i:s", filemtime($this->getFileRelativePath($fileNb))));
        $dateInterval = date_diff(date_create(date("d-m-y G:i:s")), $fileDate);
        $fileCorrect = $this->isFileCorrect($fileNb);
        if ($fileCorrect === null)
            return "Error";

        if ($fileCorrect === true) {
            return "Finished";
        } else if (($dateInterval->y > 0 || $dateInterval->m > 0 || $dateInterval->d > 0 || $dateInterval->h > 0 || $dateInterval->i > 1) && $fileCorrect === false) {
            return "Stopped";
        } else {
            return "Active";
        }
    }
}