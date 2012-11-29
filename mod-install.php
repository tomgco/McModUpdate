<?php

$modlist = "mod-list.json";
$isUrl = false;

if (isset($argv[1])) {
  $modlist = $argv[1];
}

if(filter_var($modlist, FILTER_VALIDATE_URL)) {
  $isUrl = true;
  echo "Found a Url: " . $modlist . PHP_EOL;
} else {
  echo "Working with local file." . PHP_EOL;
}

$data = null;
if ($isUrl) {
  // Get file.
} else {
  // load file from disk.
  $handle = fopen($modlist, "r");
  $data = fread($handle, filesize($modlist));
}

$json = json_decode($data);
var_dump($json);