<?php

namespace classes;

use classes\FilesController;

class ExecController extends FilesController {

    private $folderList;
    private $tokenFolder;

    public function __construct($token) {
        $this->tokenFolder = $token;
        $this->folderList = array_values(array_diff(scandir('../savefiles/'), array('.', '..','.gitkeep')));
    }

    private function getFileListFromFolder($folder) {
        return array_values(array_diff(scandir('../savefiles/' . $folder), array('.', '..', '.gitkeep')));
    }

    private function isFileCorrectFromFilepath($filepath) {
        if (strpos($filepath, '.json') === false)
            return null;
        @ $json_data = file_get_contents($filepath);
        if ($json_data === false) {
            return null;
        }
        $json_data = json_decode($json_data, true);
        if ($json_data === null) {
            return false;
        }
        return true;
    }

    private function getStatusFromData($fileCorrect, $dateInterval) {
        if ($fileCorrect === null)
            return "-";

        if ($fileCorrect === true) {
            return "Finished";
        } else if (($dateInterval->y > 0 || $dateInterval->m > 0 || $dateInterval->d > 0
        || $dateInterval->h > 0 || $dateInterval->i > 0) && $fileCorrect === false) {
            return "Stopped";
        } else {
            return "Active";
        }
    }

    private function getStatusFromFilepath($filepath) {
        $fileDate = date_create(date("d-m-y G:i:s", filemtime($filepath)));
        $dateInterval = date_diff(date_create(date("d-m-y G:i:s")), $fileDate);
        $fileCorrect = $this->isFileCorrectFromFilepath($filepath);

        return $this->getStatusFromData($fileCorrect, $dateInterval);
    }

    private function isFileActive($filepath) {
        return $this->getStatusFromFilepath($filepath) === 'Active';
    }

    private function increaseActiveRobotsCount($counter, $filepath) {
        if ($this->isFileActive($filepath))
            $counter += 1;
        return $counter;
    }

    private function activeRobotsInFileList($fileList, $folder) {
        $robotCounter = 0;
        foreach ($fileList as $file) {
            $filepath = '../savefiles/' . $folder . '/' . $file;
            $robotCounter = $this->increaseActiveRobotsCount($robotCounter, $filepath);
        }
        return $robotCounter;
    }

    private function getNbExecutingRobotsInFolder($folder) {
        $fileList = $this->getFileListFromFolder($folder);

        return $this->activeRobotsInFileList($fileList, $folder);
    }

    private function getTotalRobotExecutions() {
        $totalRobotsCount = 0;

        foreach ($this->folderList as $folder) {
            $totalRobotsCount += $this->getNbExecutingRobotsInFolder($folder);
        }
        return $totalRobotsCount;
    }

    private function isTotalRobotLimitReached() {
        return $this->getTotalRobotExecutions() > 2;
    }

    private function totalRobotLimitReached() {
        echo "Info: the simultaneous robots execution limit has been reached.<br>
        You will have to wait for a robot to finish before another one can be launched.";
        return true;
    }

    private function isUserRobotLimitReached() {
        return $this->getNbExecutingRobotsInFolder($this->tokenFolder) > 0;
    }

    private function userRobotLimitReached() {
        echo "Info: the user robots execution limit has been reached.<br>
        You will have to wait for a robot to finish or manually cancel one before another robot can be launched.";
        return true;
    }

    public function isRobotLimitReached() {
        if ($this->isUserRobotLimitReached())
            return $this->userRobotLimitReached();
        if ($this->isTotalRobotLimitReached())
            return $this->totalRobotLimitReached();
        return false;
    }

}