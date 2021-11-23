<?php

namespace classes;

class OpenFileController {

    public $jsonData;

    public function __construct($jsonData) {
        $this->jsonData = $jsonData;
    }

    public function getObjectFromId($ObjectNb) {
        return ($this->jsonData[$ObjectNb]);
    }

    public function getObjectCount() {
        if (!isset($this->jsonData['runtime'])) {
            return count($this->jsonData);
        }
        return (count($this->jsonData) - 1);
    }

    public function getObjectFromUrl($url) {
        for ($ObjectNb = 1; $ObjectNb <= $this->getObjectCount(); $ObjectNb++) {
            if ($this->jsonData[$ObjectNb]['url'] === $url)
                return $ObjectNb;
        }
        return -1;
    }

    public function getUrlPredecessor($ObjectNb) {
        $current_url = $this->getUrl($ObjectNb);
        if ($this->getUrlDepth($ObjectNb) == 0)
            return;
        for ($obj = 1; $obj <= $this->getObjectCount(); $obj++) {
            for ($link = 0; $link < $this->getAllLinksSize($obj); $link++) {
                if ($this->getSingleLink($obj, $link) === $current_url)
                    return $this->getUrl($obj);
            }
        }
        return 'sitemap';
    }

    public function getIteration($ObjectNb) {
        return ($this->jsonData[$ObjectNb]['Iteration']);
    }

    public function getUrl($ObjectNb) {
        return ($this->jsonData[$ObjectNb]['url']);
    }

    public function getUrlCharSize($ObjectNb) {
        return (strlen($this->getUrl($ObjectNb)));
    }

    public function getStatus($ObjectNb) {
        return ($this->jsonData[$ObjectNb]['status']);
    }

    public function getUrlDepth($ObjectNb) {
        return ($this->jsonData[$ObjectNb]['urlDepth']);
    }

    public function getRuntimeData() {
        if (isset($this->jsonData['runtime']))
            return ($this->jsonData['runtime']);
        else
            return null;
    }

    public function getTimesUrlFound($ObjectNb) {
        if ($this->getRuntimeData() !== null)
            return ($this->getRuntimeData()[$ObjectNb - 1]);
        return "Error: runtime data is missing.";
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
        return (strlen($this->getTitle($ObjectNb)));
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
        return (strlen($this->getAllHtml($ObjectNb)['meta'][$MetaNb]));
    }

    public function displayAllMeta($ObjectNb) {
        $metaSize = $this->getAllMetaSize($ObjectNb);
        for ($i = 0; $i < $metaSize; $i++) {
            echo htmlentities($this->getSingleMetaTag($ObjectNb, $i)) . '<br>';
        }
    }

    public function displayAllMetaCharSizes($ObjectNb) {
        $metaSize = $this->getAllMetaSize($ObjectNb);
        for ($i = 0; $i < $metaSize; $i++) {
            echo $this->getSingleMetaCharSize($ObjectNb, $i) . "<br>";
        }
    }

    public function getMetaDescription($ObjectNb) {
        $metaSize = $this->getAllMetaSize($ObjectNb);
        for ($i = 0; $i < $metaSize; $i++) {
            if (strpos($this->getSingleMetaTag($ObjectNb, $i), "name=\"description\"") !== false) {
                return $this->getSingleMetaTag($ObjectNb, $i);
            }
        }
        return '';
    }

    public function getMetaDescriptionCharSize($ObjectNb) {
        return (strlen($this->getMetaDescription($ObjectNb)));
    }

    public function getSingleMetaIndex($ObjectNb, $metaNb) {
        if (strpos($this->getSingleMetaTag($ObjectNb, $metaNb), "noindex") === false)
            return 'Y';
        return 'N';
    }

    public function getMetaIndex($ObjectNb) {
        $allMetaSize = $this->getAllMetaSize($ObjectNb);

        for ($i = 0; $i < $allMetaSize; $i++) {
            echo $this->getSingleMetaIndex($ObjectNb, $i);
            echo '<br>';
        }
    }

    public function getSingleMetaFollow($ObjectNb, $metaNb) {
        if (strpos($this->getSingleMetaTag($ObjectNb, $metaNb), "nofollow") === false)
            return 'Y';
        return 'N';
    }

    public function getMetaFollow($ObjectNb) {
        $allMetaSize = $this->getAllMetaSize($ObjectNb);

        for ($i = 0; $i < $allMetaSize; $i++) {
            echo $this->getSingleMetaFollow($ObjectNb, $i);
            echo '<br>';
        }
    }

    public function getSingleMetaSponsored($ObjectNb, $metaNb) {
        if (strpos($this->getSingleMetaTag($ObjectNb, $metaNb), "sponsored") === false)
            return 'N';
        return 'Y';
    }

    public function getMetaSponsored($ObjectNb) {
        $allMetaSize = $this->getAllMetaSize($ObjectNb);

        for ($i = 0; $i < $allMetaSize; $i++) {
            echo $this->getSingleMetaSponsored($ObjectNb, $i);
            echo '<br>';
        }
    }

    public function getSingleMetaUgc($ObjectNb, $metaNb) {
        if (strpos($this->getSingleMetaTag($ObjectNb, $metaNb), "ugc") === false)
            return 'N';
        return 'Y';
    }

    public function getMetaUgc($ObjectNb) {
        $allMetaSize = $this->getAllMetaSize($ObjectNb);

        for ($i = 0; $i < $allMetaSize; $i++) {
            echo $this->getSingleMetaUgc($ObjectNb, $i);
            echo '<br>';
        }
    }

    public function getSingleMetaNoopener($ObjectNb, $metaNb) {
        if (strpos($this->getSingleMetaTag($ObjectNb, $metaNb), "noopener") === false)
            return 'N';
        return 'Y';
    }

    public function getMetaNoopener($ObjectNb) {
        $allMetaSize = $this->getAllMetaSize($ObjectNb);

        for ($i = 0; $i < $allMetaSize; $i++) {
            echo $this->getSingleMetaNoopener($ObjectNb, $i);
            echo '<br>';
        }
    }

    public function getBiggestMetaNb() {
        $objCount = $this->getObjectCount();
        $biggestMetaNb = 0;

        for ($i = 1; $i <= $objCount; $i++) {
            if ($this->getAllMetaSize($i) > $biggestMetaNb)
                $biggestMetaNb = $this->getAllMetaSize($i);
        }
        return $biggestMetaNb;
    }

    public function getAllMetaTitlesInCSV() {
        $metaCount = $this->getBiggestMetaNb();
        $query = "";

        for ($i = 0; $i < $metaCount; $i++) {
            $query = $query . "'Meta " . $i + 1 . "',";
            $query = $query . "'Meta index " . $i + 1 . "',";
            $query = $query . "'Meta follow " . $i + 1 . "',";
            $query = $query . "'Meta sponsored " . $i + 1 . "',";
            $query = $query . "'Meta UGC " . $i + 1 . "',";
            $query = $query . "'Meta noopener " . $i + 1 . "',";
        }
        return $query;
    }

    public function getAllMetaDataInCSV($ObjectNb) {
        $metaCount = $this->getAllMetaSize($ObjectNb);
        $maxCount = $this->getBiggestMetaNb();
        $query = "";

        for ($i = 0; $i < $metaCount; $i++) {
            $query = $query . "'" . $this->getSingleMetaTag($ObjectNb, $i) . "',";
            $query = $query . "'" . $this->getSingleMetaIndex($ObjectNb, $i) . "',";
            $query = $query . "'" . $this->getSingleMetaFollow($ObjectNb, $i) . "',";
            $query = $query . "'" . $this->getSingleMetaSponsored($ObjectNb, $i) . "',";
            $query = $query . "'" . $this->getSingleMetaUgc($ObjectNb, $i) . "',";
            $query = $query . "'" . $this->getSingleMetaNoopener($ObjectNb, $i) . "',";
        }
        for ($i = $metaCount; $i < $maxCount ; $i++) {
            for ($j = 0; $j < 6; $j++) {
                $query = $query . "' ',";
            }
        }
        return $query;
    }

    public function getAllHreflang($ObjectNb) {
        return ($this->getAllHtml($ObjectNb)['hreflang']);
    }

    public function getAllHreflangSize($ObjectNb) {
        return (count($this->getAllHreflang($ObjectNb)));
    }

    public function getSingleHreflang($ObjectNb, $hreflangNb) {
        return ($this->getAllHreflang($ObjectNb)[$hreflangNb]);
    }

    public function getSingleHreflangCharSize($ObjectNb, $hreflangNb) {
        return (strlen($this->getSingleHreflang($ObjectNb, $hreflangNb)));
    }

    public function getBiggestHreflangNb() {
        $objCount = $this->getObjectCount();
        $biggestNb = 0;

        for ($i = 1; $i <= $objCount; $i++) {
            if ($this->getAllHreflangSize($i) > $biggestNb)
                $biggestNb = $this->getAllHreflangSize($i);
        }
        return $biggestNb;
    }

    public function displayAllHreflang($ObjectNb) {
        $hreflang_size = $this->getAllHreflangSize($ObjectNb);
        for ($i = 0; $i < $hreflang_size; $i++) {
            echo htmlentities($this->getSingleHreflang($ObjectNb, $i)) . '<br>';
        }
    }

    public function displayAllHreflangCharSizes($ObjectNb) {
        $hreflang_size = $this->getAllHreflangSize($ObjectNb);
        for ($i = 0; $i < $hreflang_size; $i++) {
            echo  $this->getSingleHreflangCharSize($ObjectNb, $i) . '<br>';
        }
    }

    public function getAllHreflangTitlesInCSV() {
        $hreflangCount = $this->getBiggestHreflangNb();
        $query = "";

        for ($i = 0; $i < $hreflangCount; $i++) {
            $query = $query . "'Hreflang Nb" . $i + 1 . "',";
        }
        return $query;
    }

    public function getAllHreflangDataInCSV($ObjectNb) {
        $hreflangCount = $this->getAllHreflangSize($ObjectNb);
        $maxCount = $this->getBiggestHreflangNb();
        $query = "";

        for ($i = 0; $i < $hreflangCount; $i++) {
            $query = $query . "'" . $this->getSingleHreflang($ObjectNb, $i) . "',";
        }
        for ($i = $hreflangCount; $i < $maxCount; $i++) {
            $query = $query . "' ',";
        }
        return $query;
    }

    public function getAllCanonicals($ObjectNb) {
        return ($this->getAllHtml($ObjectNb)['canonicals']);
    }

    public function getAllCanonicalsSize($ObjectNb) {
        return (count($this->getAllCanonicals($ObjectNb)));
    }

    public function getSingleCanonicalRaw($ObjectNb, $canonicalNb) {
        return $this->getAllCanonicals($ObjectNb)[$canonicalNb];
    }

    public function getSingleCanonical($ObjectNb, $canonicalNb) {
        return htmlentities($this->getSingleCanonicalRaw($ObjectNb, $canonicalNb));
    }

    public function getSingleCanonicalCharSize($ObjectNb, $canonicalNb) {
        return (strlen($this->getSingleCanonical($ObjectNb, $canonicalNb)));
    }

    public function displayAllCanonicals($ObjectNb) {
        $allCanonicalsSize = $this->getAllCanonicalsSize($ObjectNb);

        for ($i = 0; $i < $allCanonicalsSize; $i++) {
            echo $this->getSingleCanonical($ObjectNb, $i) . '<br>';
        }
    }

    public function displayAllCanonicalSizes($ObjectNb) {
        $allCanonicalsSize = $this->getAllCanonicalsSize($ObjectNb);

        for ($i = 0; $i < $allCanonicalsSize; $i++) {
            echo $this->getSingleCanonicalCharSize($ObjectNb, $i) . '<br>';
        }
    }

    public function getAllCanonicalDataInCSV($ObjectNb) {
        $query = "";

        $query = $query . "'" . $this->getSingleCanonicalRaw($ObjectNb, 0) . "',";
        $query = $query . "' ',";
        return $query;
    }

    public function getAllHeads($ObjectNb) {
        return ($this->getAllHtml($ObjectNb)['heads']);
    }

    public function getAllHeadsSize($ObjectNb) {
        return (count($this->getAllHeads($ObjectNb)));
    }

    public function getTypeHead($ObjectNb, $typeNb) {
        if ($this->getAllHeadsSize($ObjectNb) === 0)
            return null;
        return ($this->getAllHeads($ObjectNb)[$typeNb]);
    }

    public function getTypeHeadSize($ObjectNb, $typeNb) {
        $count = $this->getTypeHead($ObjectNb, $typeNb);
        if ($count === null)
            return 0;
        return (count($this->getTypeHead($ObjectNb, $typeNb)));
    }

    public function getSingleHeadRaw($ObjectNb, $typeNb, $headNb) {
        if (!$this->getTypeHead($ObjectNb, $typeNb))
            return 'Error';
        return ($this->getTypeHead($ObjectNb, $typeNb)[$headNb]);
    }

    public function getSingleHead($ObjectNb, $typeNb, $headNb) {
        return htmlentities($this->getSingleHeadRaw($ObjectNb, $typeNb, $headNb));
    }

    public function getSingleHeadSize($ObjectNb, $typeNb, $headNb) {
        return (strlen($this->getSingleHead($ObjectNb, $typeNb, $headNb)));
    }

    public function displayTypeHead($ObjectNb, $typeNb) {
        $sizeTypeHead = $this->getTypeHeadSize($ObjectNb, $typeNb);

        if ($sizeTypeHead === 0) {
            echo '-';
        }
        for ($i = 0; $i < $sizeTypeHead; $i++) {
            echo $this->getSingleHead($ObjectNb, $typeNb, $i) . '<br>';
        }
    }

    public function displayTypeHeadSizes($ObjectNb, $typeNb) {
        $sizeTypeHead = $this->getTypeHeadSize($ObjectNb, $typeNb);

        if ($sizeTypeHead === 0) {
            echo '0';
        }
        for ($i = 0; $i < $sizeTypeHead; $i++) {
            echo $this->getSingleHeadSize($ObjectNb, $typeNb, $i) . '<br>';
        }
    }

    public function getAllUserSelectors($ObjectNb) {
        return ($this->getAllHtml($ObjectNb)['userSelected']);
    }

    public function getAllUserSelectorsSize($ObjectNb) {
        return (count($this->getAllUserSelectors($ObjectNb)));
    }

    public function getTypeUserSelectors($ObjectNb, $typeNb) {
        if (count($this->getAllHtml($ObjectNb)['userSelected']) > 0)
            return ($this->getAllHtml($ObjectNb)['userSelected'][$typeNb]);
        return '-';
    }

    public function getTypeUserSelectorsSize($ObjectNb, $typeNb) {
        return (count($this->getTypeUserSelectors($ObjectNb, $typeNb)));
    }

    public function getSingleTypeUserSelectorCharSize($ObjectNb, $typeNb, $selectorNb) {
        return (strlen($this->getSingleTypeUserSelector($ObjectNb, $typeNb, $selectorNb)));
    }

    public function getSingleTypeUserSelector($ObjectNb, $typeNb, $selectorNb) {
        return ($this->getAllHtml($ObjectNb)['userSelected'][$typeNb][$selectorNb]);
    }

    public function displaySingleTypeUserSelector($ObjectNb, $typeNb, $selectorNb) {
        echo htmlentities($this->getSingleTypeUserSelector($ObjectNb, $typeNb, $selectorNb));
    }

    public function displayTypeUserSelectors($ObjectNb, $typeNb) {
        $typeUserSelectorsSize = $this->getTypeUserSelectorsSize($ObjectNb, $typeNb);

        for ($j = 0; $j < $typeUserSelectorsSize; $j++) {
            $this->displaySingleTypeUserSelector($ObjectNb, $typeNb, $j);
            echo '<br>';
        }
    }

    public function getTypeUserSelectorSizes($ObjectNb, $typeNb) {
        $typeUserSelectorsSize = $this->getTypeUserSelectorsSize($ObjectNb, $typeNb);

        for ($i = 0; $i < $typeUserSelectorsSize; $i++) {
            echo $this->getSingleTypeUserSelectorCharSize($ObjectNb, $typeNb, $i);
            echo '<br>';
        }
    }

    public function displayAllUserSelectors($ObjectNb) {
        $allUserSelectorSize = $this->getAllUserSelectorsSize($ObjectNb);

        for ($i = 0; $i < $allUserSelectorSize; $i++) {
            $this->displayTypeUserSelectors($ObjectNb, $i);
        }
    }

    public function getAllLinks($ObjectNb) {
        return ($this->jsonData[$ObjectNb]['links']);
    }

    public function getAllLinksSize($ObjectNb) {
        return (count($this->getAllLinks($ObjectNb)));
    }

    public function getSingleLink($ObjectNb, $LinkNb) {
        return ($this->getAllLinks($ObjectNb)[$LinkNb]);
    }

    public function getSingleLinkCharSize($ObjectNb, $LinkNb) {
        return (strlen($this->getSingleLink($ObjectNb, $LinkNb)));
    }

    public function displayAllLinks($ObjectNb) {
        $link_size = $this->getAllLinksSize($ObjectNb);
        for ($i = 0; $i < $link_size; $i++) {
            echo '<a href="' . $this->getSingleLink($ObjectNb, $i) . '">' . $this->getSingleLink($ObjectNb, $i) . '</a><br>';
        }
    }

    public function displayAllLinkCharSizes($ObjectNb) {
        $link_size = $this->getAllLinksSize($ObjectNb);
        for ($i = 0; $i < $link_size; $i++) {
            echo $this->getSingleLinkCharSize($ObjectNb, $i) . '<br>';
        }
    }

    public function getAllLinkArticles($ObjectNb) {
        return ($this->getAllHtml($ObjectNb)['linkArticle']);
    }

    public function getAllLinkArticlesSize($ObjectNb) {
        return (count($this->getAllLinkArticles($ObjectNb)));
    }

    public function getSingleLinkArticle($ObjectNb, $articleNb) {
        return ($this->getAllLinkArticles($ObjectNb)[$articleNb]);
    }

    public function getSingleLinkArticleCharSize($ObjectNb, $articleNb) {
        return (strlen($this->getSingleLinkArticle($ObjectNb, $articleNb)));
    }

    public function displayAllLinkArticles($ObjectNb) {
        $linkArticleSize = $this->getAllLinkArticlesSize($ObjectNb);

        for ($i = 0; $i < $linkArticleSize; $i++) {
            echo htmlentities($this->getSingleLinkArticle($ObjectNb, $i)) . "<br>";
        }
    }

    public function displayAllLinkArticleCharSizes($ObjectNb) {
        $linkArticleSize = $this->getAllLinkArticlesSize($ObjectNb);

        for ($i = 0; $i < $linkArticleSize; $i++) {
            echo strlen($this->getSingleLinkArticle($ObjectNb, $i)) . "<br>";
        }
    }

    public function getSingleLinkTargetBlank($ObjectNb, $linkNb) {
        if (strpos($this->getSingleLinkArticle($ObjectNb, $linkNb), "target=\"_blank\"") === false)
            return 'N';
        return 'Y';
    }

    public function getLinkTargetBlank($ObjectNb) {
        $linkArticleSize = $this->getAllLinkArticlesSize($ObjectNb);

        if ($linkArticleSize < 1) {
            echo '-';
            return;
        }
        for ($i = 0; $i < $linkArticleSize; $i++) {
            echo $this->getSingleLinkTargetBlank($ObjectNb, $i);
            echo '<br>';
        }
    }

    public function getAllImg($ObjectNb) {
        return ($this->getAllHtml($ObjectNb)['img']);
    }

    public function getAllImgSize($ObjectNb) {
        return (count($this->getAllImg($ObjectNb)));
    }

    public function getSingleImg($ObjectNb, $imgNb) {
        return ($this->getAllImg($ObjectNb)[$imgNb]);
    }

    public function displayAllImgOuterHtml($ObjectNb) {
        $allImgSize = $this->getAllImgSize($ObjectNb);

        for ($i = 0; $i < $allImgSize; $i++) {
            echo htmlentities($this->getSingleImg($ObjectNb, $i));
            echo '<br>';
        }
    }

    public function addSpacesToSizeCols($spacesNb) {
        for ($i = 0; $i < $spacesNb; $i++) {
            echo "&nbsp";
        }
    }

    public function addTabsToSizeCols($tabsNb) {
        for ($i = 0; $i < $tabsNb; $i++) {
            $this->addSpacesToSizeCols(4);
        }
    }

    public function getTelNb($ObjectNb) {
        if ($this->getTypeUserSelectors($ObjectNb, 0) == null)
            return '-';
        if ($this->getSingleTypeUserSelector($ObjectNb, 0, 0) != null)
            return ($this->getSingleTypeUserSelector($ObjectNb, 0, 0));
        return '-';
    }
}