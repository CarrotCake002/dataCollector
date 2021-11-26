<?php

namespace classes;

use classes\FilesController;

class ExecController extends FilesController {

    private $folderList;

    public function __construct() {
        $this->folderList = array_values(array_diff(scandir('../../savefiles/'), array('.', '..')));
    }

    private function getFileListFromFolder($folder) {
        return array_values(array_diff(scandir('../../savefiles/' . $folder), array('.', '..')));
    }

    private function isFileActive($filepath) {
        return $this->getFileStatus($this->getFileId($filepath)) === 'Active';
    }

    private function increaseActiveRobotsCount($counter, $filepath) {
        if ($this->isFileActive($filepath))
            $counter += 1;
        return $counter;
    }

    private function activeRobotsInFileList($fileList) {
        $robotCounter = 0;

        foreach ($fileList as $filepath) {
            $robotCounter = $this->increaseActiveRobotsCount($robotCounter, $filepath);
        }
        return $robotCounter;
    }

    private function getNbExecutingRobotsInFolder($folder) {
        $fileList = $this->getFileListFromFolder($folder);

        return $this->activeRobotsInFileList($fileList);
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

    public function totalRobotLimitReached() {
        echo "Info: the simultaneous robots exxecution limit has been reached.<br>
        You will have to wait for another robot to finish before another one can be launched";
        return true;
    }

    public function isRobotLimitReached() {
        if ($this->isTotalRobotLimitReached())
            return $this->totalRobotLimitReached();
        else if ($this->isUserRobotLimitReached())
            return $this->userRobotLimitReached();
        return false;
    }

}