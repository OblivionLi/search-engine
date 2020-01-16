<?php

include('config.php');
include('classes/DomDocumentParser.php');

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExists($url)
{
    global $db;

    $sql = "SELECT * FROM sites WHERE url = '$url'";

    $result = mysqli_query($db, $sql);

    if (!$result) {
        confirm_query_result($result);
    }

    return $result->num_rows != 0;
}

function insertLink($url, $title, $description, $keywords)
{
    global $db;

    $sql = "INSERT INTO sites (url, title, description, keywords) VALUES ('$url', '$title', '$description', '$keywords')";

    $result = mysqli_query($db, $sql);

    if (!$result) {
        confirm_query_result($result);
    }
}

function insertImage($url, $src, $alt, $title)
{
    global $db;

    $sql = "INSERT INTO images (siteUrl, imageUrl, alt, title) VALUES ('$url', '$src', '$alt', '$title')";

    $result = mysqli_query($db, $sql);

    if (!$result) {
        confirm_query_result($result);
    }
}

function createLink($src, $url)
{
    $scheme = parse_url($url)['scheme'];
    $host = parse_url($url)['host'];

    if (substr($src, 0, 2) == '//') {
        $src = $scheme . ':' . $src;
    } elseif (substr($src, 0, 1) == '/') {
        $src = $scheme . '://' . $host . $src;
    } elseif (substr($src, 0, 2) == './') {
        $src = $scheme . '://' . $host . dirname(parse_url($url)['path']) . substr($src, 1);
    } elseif (substr($src, 0, 3) == '../') {
        $src = $scheme . '://' . $host . '/' . $src;
    } elseif (substr($src, 0, 5) != 'https' && substr($src, 0, 4) != 'http') {
        $src = $scheme . '://' . $host . '/' . $src;
    }

    return $src;
}

function getDetails($url)
{
    global $alreadyFoundImages;

    $parser = new DomDocumentParser($url);

    $titleArray = $parser->getTitle();

    if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
        return;
    }

    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace('\n', '', $title);

    if ($title == '') {
        return;
    }

    $description = '';
    $keywords = '';

    $metaArray = $parser->getMetaTags();

    foreach ($metaArray as $meta) {
        if ($meta->getAttribute('name') == 'description') {
            $description = $meta->getAttribute('content');
        }
        if ($meta->getAttribute('name') == 'keywords') {
            $keywords = $meta->getAttribute('content');
        }
    }

    $description = str_replace('\n', '', $description);
    $keywords = str_replace('\n', '', $keywords);

    if (linkExists($url)) {
        echo 'already exists';
    } elseif (insertLink($url, $title, $description, $keywords)) {
        echo 'success';
    } else {
        echo 'error: failed to insert';
    }

    $imageArray = $parser->getImages();

    foreach ($imageArray as $image) {
        $src = $image->getAttribute('src');
        $alt = $image->getAttribute('alt');
        $title = $image->getAttribute('title');

        if (!$title && !$alt) {
            continue;
        }

        $src = createLink($src, $url);

        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;

            insertImage($url, $src, $alt, $title);
        }
    }
}

function followLinks($url)
{
    global $alreadyCrawled;
    global $crawling;

    $parser = new DomDocumentParser($url);

    $linkList = $parser->getLinks();

    foreach ($linkList as $link) {
        $href = $link->getAttribute('href');

        if (strpos($href, '#') !== false) {
            continue;
        } elseif (substr($href, 0, 11) == 'javascript:') {
            continue;
        }

        $href = createLink($href, $url);

        if (!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;

            getDetails($href);
        }
    }

    array_shift($crawling);

    foreach ($crawling as $site) {
        followLinks($site);
    }
}

$startUrl = "https://www.bbc.com/news";
followLinks($startUrl);