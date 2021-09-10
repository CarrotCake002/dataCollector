<?php

    namespace Savefile;

    class OpenFileController {
        
        public $jsonData;
        
        public function __construct($jsonData) {
            $this->jsonData = $jsonData;
        }
        
        public function getIteration($ObjectNb) {
            return ($this->jsonData["Object " . $ObjectNb]['Iteration']);
        }
        
        public function getUrl($ObjectNb) {
            return ($this->jsonData["Object " . $ObjectNb]['url']);
        }

        public function getStatus($ObjectNb) {
            return ($this->jsonData["Object " . $ObjectNb]['status']);
        }

        public function getUrlDepth($ObjectNb) {
            return ($this->jsonData["Object " . $ObjectNb]['urlDepth']);
        }

        public function getResponseTime($ObjectNb) {
            return ($this->jsonData["Object " . $ObjectNb]['time']);
        }

        public function getAllHtml($ObjectNb) {
            return ($this->jsonData["Object " . $ObjectNb]['html']);
        }

        public function getTitle($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['title']);
        }
        
        public function getTitleSize($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['titleSize']);
        }

        public function getAllMeta($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['meta']);
        }

        public function getAllMetaSize($ObjectNb) {
            return count($this->getAllMeta($ObjectNb));
        }

        public function getSingleMetaTag($ObjectNb, $MetaNb) {
            return ($this->getAllHtml($ObjectNb)['meta'][$MetaNb][0]);
        }

        public function getSingleMetaCharSize($ObjectNb, $MetaNb) {
            return ($this->getAllHtml($ObjectNb)['meta'][$MetaNb][1]);
        }

        public function getAllHreflang($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['hreflang']);
        }

        public function getSingleHreflang($ObjectNb, $hreflangNb) {
            return ($this->getAllHtml($ObjectNb)['hreflang'][$hreflangNb]);
        }

        public function getSingleHreflangCharSize($ObjectNb, $hreflangNb) {
            return (count($this->getSingleHreflang($ObjectNb, $hreflangNb)));
        }

        public function getAllUserSelector($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['userSelected']);
        }

        public function getSingleUserSelector($ObjectNb, $SelectorNb) {
            return ($this->getAllHtml($ObjectNb)['userSelected'][$SelectorNb]);
        }

        public function getSingleUserSelectorCharSize($ObjectNb, $SelectorNb) {
            return (count($this->getSingleUserSelector($ObjectNb, $SelectorNb)));
        }
    }