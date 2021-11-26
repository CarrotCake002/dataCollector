<?php

namespace classes;

use classes\FilesController;

class ExecController extends FilesController {

    private $folderList;
    private $tokenFolder;

    public function __construct($token) {
        $this->tokenFolder = $token;
        $this->folderList = array_values(array_diff(scandir('../savefiles/'), array('.', '..')));
    }

    private function getFileListFromFolder($folder) {
        return array_values(array_diff(scandir('../savefiles/' . $folder), array('.', '..')));
    }

    private function isFileActive($filepath) {
        var_dump('Read text in isFileActive in the ExecController my duude');die;
        // problem here with getFileId, the function is not constructed as expected when creating this.
        // it needs to be modified or a new one needs to be created in this controller
        return $this->getFileStatus($this->getFileId($filepath)) === 'Active';
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
        return $this->getTotalRobotExecutions() > 3;
    }

    private function totalRobotLimitReached() {
        echo "Info: the simultaneous robots execution limit has been reached.<br>
        You will have to wait for a robot to finish before another one can be launched.";
        return true;
    }

    private function isUserRobotLimitReached() {
        return $this->getNbExecutingRobotsInFolder($this->tokenFolder) > 1;
    }

    private function userRobotLimitReached() {
        echo "Info: the user robots execution limit has been reached.<br>
        You will have to wait for a robot to finish or manually cancel one before another robot can be launched.";
        return true;
    }

    public function isRobotLimitReached() {
        if ($this->isUserRobotLimitReached())
        var_dump('die');die;
            return $this->userRobotLimitReached();
        if ($this->isTotalRobotLimitReached())
            return $this->totalRobotLimitReached();
        return false;
    }

}