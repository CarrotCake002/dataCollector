<?php

namespace classes;

class FilesController {

    public $folder;
    public $file_list;
    public $filesize_list;

    public function __construct($folder_name) {
        $this->folder = $folder_name;
        $this->file_list = array_values(array_diff(scandir('../../savefiles/' . $this->folder), array('.', '..')));
        $this->filesize_list = $this->getFilesizeList();
    }

    public function getFolderPath() {
        return '/savefiles/' . $this->folder;
    }

    public function getFileListSize() {
        return count($this->file_list);
    }

    public function getFileName($fileNb) {
        if (isset($this->file_list[$fileNb]));
            return $this->file_list[$fileNb];
        return false;
    }

    public function getFileType($fileNb) {
        return pathinfo($this->getFileName($fileNb), PATHINFO_EXTENSION);
    }

    public function getFileId($filename) {
        for ($id = 0; $id < $this->getFileListSize(); $id++) {
            if ($this->file_list[$id] === $filename)
                return $id;
        }
        return false;
    }

    public function getFileRelativePath($fileNb) {
        $filename = $this->getFileName($fileNb);
        if (!$filename)
            return false;
        return '../../savefiles/' . $this->folder . '/' . $filename;
    }

    public function getFilesizeList() {
        $filesize_list = [];

        for ($i = 0; $i < $this->getFileListSize(); $i++) {
            array_push($filesize_list, $this->getFileSize($i));
        }
        return $filesize_list;
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
        $error = unlink($this->getFileRelativePath($fileNb));
        $this->updateFileList();
        return $error;
    }

    public function checkTokenFolderEmpty() {
        return $this->getFileListSize() < 1;
    }

    public function deleteTokenFolder() {
        rmdir('../../savefiles/' . $this->folder);
        setcookie('token', '', -1, '/');
    }

    private function isFileCorrect($fileNb) {
        if (strpos($this->getFileName($fileNb), '.json') === false)
            return null;
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
            return "-";

        if ($fileCorrect === true) {
            return "Finished";
        } else if (($dateInterval->y > 0 || $dateInterval->m > 0 || $dateInterval->d > 0 || $dateInterval->h > 0 || $dateInterval->i > 0) && $fileCorrect === false) {
            return "Stopped";
        } else {
            return "Active";
        }
    }

    public function deleteTemporalFiles() {
        $listSize = $this->getFileListSize();

        for ($i = 0; $i < $listSize; $i++) {
            if (strpos($this->file_list[$i], '.json') === false && strpos($this->file_list[$i], '.csv') === false) {
                unlink($this->getFileRelativePath($i));
            }
        }
        $this->updateFileList();
    }

    public function updateFileList() {
        $this->file_list = array_values(array_diff(scandir('../../savefiles/' . $this->folder), array('.', '..')));
    }

    public function getFilenamesInStr() {
        $result = "'" . $this->file_list[0];

        for ($i = 1; $i < count($this->file_list); $i++)
            $result .= "', '" . $this->file_list[$i];
        $result .= "'";
        return $result;
    }

    public function getFileSizesInStr() {
        $result = "'" . $this->filesize_list[0];

        for ($i = 1; $i < count($this->filesize_list); $i++)
            $result .= "', '" . $this->filesize_list[$i];
        $result .= "'";
        return $result;
    }

    public function getFileAge($filepath) {
        $fileLastUpdate = date_create(date("y-m-d G:i:s", filemtime($filepath)));
        $currentTime = date_create(date("y-m-d G:i:s"));
        /*var_dump($fileLastUpdate);
        echo '<br>';
        var_dump($currentTime);
        echo '<br>';
        var_dump(date_diff($currentTime, $fileLastUpdate));
        echo '<br>';*/
        return date_diff($currentTime, $fileLastUpdate);
    }

    public function isFileOld($filepath) {
        $fileAge = $this->getFileAge($filepath);

        if ($fileAge->y > 0 || $fileAge->m > 0 || $fileAge->d > 6)
            return true;
        return false;
    }

    public function deleteOldFiles() {
        $filesDeleted = false;

        for ($i = 0; $i < count($this->file_list); $i++) {
            $filepath = './../..' . $this->getFolderPath() . '/' . $this->file_list[$i];

            if ($this->isFileOld($filepath)) {
                $this->deleteFile($i);
                $filesDeleted = true;
            }
        }
        return $filesDeleted;
    }

    public function getFileDeletionTimeLeft($fileNb) {
        $filepath = $this->getFileRelativePath($fileNb);
        $fileAge = $this->getFileAge($filepath);

        return 6 - $fileAge->d . "d " . 23 - $fileAge->h . "h " . 59 - $fileAge->i . "min";
    }
}