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
if ($json === null) throw new Exception("Failed to parse JSON: error ". json_last_error());

function getOS() {
  $sys = strtoupper(PHP_OS);
  $os = 0;
  if(substr($sys,0,3) == "WIN")
  {
      $os = 1;
  }
  else if($sys == "LINUX")
  {
      $os = 2;
  }
  else if ($sys == "DARWIN")
  {
      $os = 3;
  }
  return $os;
}
