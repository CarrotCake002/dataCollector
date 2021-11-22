<?php

namespace classes;

class SessionController {

    public $session_id;
    public $error = false;

    public function __construct($session_id = false) {
        if ($session_id !== false) {
            session_id($session_id);
            $this->session_id = session_id();
            if (!$this->checkSessionFolderExists())
                $this->error = true;
        }
    }

    public function checkSessionId() {
        if ($this->session_id === NULL)
            $this->session_id = session_id();
    }

    public function getSessionFolderName() {
        return $this->session_id;
    }

    public function getSessionFolderPath() {
        return ($_SERVER["DOCUMENT_ROOT"] . '/savefiles/' . $this->getSessionFolderName());
    }

    public function checkSessionFolderExists() {
        $dir = $this->getSessionFolderPath();
        return file_exists($dir);
    }

    public function createSessionFolder() {
        return mkdir($this->getSessionFolderPath(), 0777, false);
    }

}