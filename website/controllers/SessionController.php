<?php

    namespace classes;

    class SessionController {

        private $session_id;

        public function __construct() {
            $this->session_id = session_id();
        }

        public function getSessionFolderName() {
            return $this->session_id;
        }

        public function getSessionFolderPath() {
            return $_SERVER["DOCUMENT_ROOT"] . '/savefiles/' . $this->getSessionFolderName();
        }

        public function checkSessionFolderExists() {
            $dir = $this->getSessionFolderPath();
            return file_exists($dir);
        }

        public function createSessionFolder() {
            return mkdir($this->getSessionFolderPath(), 0777, false);
        }

    }