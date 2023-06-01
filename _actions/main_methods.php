<?php

function filterInput ($value) {
    $value = trim($value);
    $value = htmlentities($value);
    $value = strip_tags($value);

    return $value;
}