<?php

    namespace classes;

    class SessionController {

        private $session_id;

        public function __construct($session_id) {
            $this->session_id = $session_id;
        }

        private function getSessionFolderPath() {
            return $_SERVER["DOCUMENT_ROOT"] . '/savefiles/' . $this->session_id;
        }

        public function checkSessionFolderExists() {
            $dir = $this->getSessionFolderPath();
            return file_exists($dir);
        }

        public function createSessionFolder() {
            return mkdir($this->getSessionFolderPath(), 0777, false);
        }

    }