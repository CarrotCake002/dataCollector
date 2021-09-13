<?php

    namespace classes;

    class OpenFileController {
        
        public $jsonData;
        
        public function __construct($jsonData) {
            $this->jsonData = $jsonData;
        }

        public function getObjectCount() {
            return (count($this->jsonData));
        }

        public function getObjectFromUrl($url) {
            for ($ObjectNb = 1; $ObjectNb <= $this->getObjectCount(); $ObjectNb++) {
                if ($this->jsonData[$ObjectNb]['url'] === $url)
                    return $ObjectNb;
            }
            return null;
        }

        public function getIteration($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['Iteration']);
        }

        public function getUrl($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['url']);
        }

        public function getUrlCharSize($ObjectNb) {
            return (count($this->getUrl($ObjectNb)));
        }

        public function getStatus($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['status']);
        }

        public function getUrlDepth($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['urlDepth']);
        }

        public function getResponseTime($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['time']);
        }

        public function getAllHtml($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['html']);
        }

        public function getAllHtmlSize($ObjectNb) {
            return (count($this->getAllHtml($ObjectNb)));
        }

        public function getTitle($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['title']);
        }

        public function getTitleSize($ObjectNb) {
            return (count($this->getTitle($ObjectNb)));
        }

        public function getAllMeta($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['meta']);
        }

        public function getAllMetaSize($ObjectNb) {
            return (count($this->getAllMeta($ObjectNb)));
        }

        public function getSingleMetaTag($ObjectNb, $MetaNb) {
            return ($this->getAllHtml($ObjectNb)['meta'][$MetaNb]);
        }

        public function getSingleMetaCharSize($ObjectNb, $MetaNb) {
            return (count($this->getAllHtml($ObjectNb)['meta'][$MetaNb]));
        }

        public function getAllHreflang($ObjectNb) {
            return ($this->getAllHtml($ObjectNb)['hreflang']);
        }

        public function getAllHreflangSize($ObjectNb) {
            return (count($this->getAllHreflang($ObjectNb)));
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

        public function getAllUserSelectorCount($ObjectNb) {
            return (count($this->getAllUserSelector($ObjectNb)));
        }

        public function getSingleUserSelector($ObjectNb, $SelectorNb) {
            return ($this->getAllHtml($ObjectNb)['userSelected'][$SelectorNb]);
        }

        public function getSingleUserSelectorCharSize($ObjectNb, $SelectorNb) {
            return (count($this->getSingleUserSelector($ObjectNb, $SelectorNb)));
        }

        public function getAllLinks($ObjectNb) {
            return ($this->jsonData[$ObjectNb]['links']);
        }

        public function getAllLinksSize($ObjectNb) {
            return (count($this->getAllLinks($ObjectNb)));
        }

        public function getSingleLink($ObjectNb, $LinkNb) {
            return ($this->jsonData[$ObjectNb]['links'][$LinkNb]);
        }

        public function getSingleLinkCharSize($ObjectNb, $LinkNb) {
            return (count($this->getSingleLink($ObjectNb, $LinkNb)));
        }
    }