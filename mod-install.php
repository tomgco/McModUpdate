<?php
date_default_timezone_set('UTC');

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

/****** do jar ******/

/****** do mods ******/
//backup mods folder
$home = exec('echo $HOME');
backup("$home/Library/Application Support/minecraft/mods/");

foreach($json->common->mods as $mod) {

}

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

function backup($folder) {
  $name = basename($folder);
  $zipfile = realpath("$folder../"). "/$name-". date('d-M-Y_H-i-s') .".zip";
  echo "backing up $folder to $zipfile\n";
  $zip = new ZipArchive();
  $zip->open($zipfile, ZIPARCHIVE::CREATE) or die("Error creating zip archive.");

  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::CHILD_FIRST);
  foreach ($iterator as $path) {
    if (!$path->isDir()) {
      $filepath = $path->__toString();
      $name = substr($filepath, strlen($folder));
      $zip->addFile($filepath, $name);
      echo "adding $name\n";
    }
  }
  $zip->close() or die("Error closing zip archive.");
  echo "backup of $folder is complete\n";
}
