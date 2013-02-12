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

//get OS
$sys = strtoupper(PHP_OS);
$os = 0;
if (substr($sys,0,3) == "WIN")
{
    $os = 1;
}
else if ($sys == "LINUX")
{
    $os = 2;
}
else if ($sys == "DARWIN")
{
    $os = 3;
}

$ds = ($os == 1) ? "\\" : "/";

//get minecraft dir
$mc_dir = "";
switch ($os) {
  case 1: //Windows
  {
    $mc_dir = exec("echo %APPDATA%") . "\\.minecraft\\";
    break;
  }
  case 2: //Linux
  {
    die("error: linux is currently not supported.");
    break;
  }
  case 3: //Windows
  {
    $mc_dir = exec('echo $HOME') . "/Library/Application Support/minecraft/";
    break;
  }
  default:
  {
    die("error: could not get minecraft directory");
    break;
  }
}

if (!is_dir($mc_dir)) die("error: could not get minecraft directory");



/****** do jar ******/
//get version
$version = $json->version;
echo "installing version $version".PHP_EOL;

//download jar (http://assets.minecraft.net/X_X_X/minecraft.jar)
mkdir('tmp');
$url = 'http://assets.minecraft.net/'. str_replace(".", "_", $version) .'/minecraft.jar';
$tmp_jar = "tmp".$ds."minecraft.jar";
echo "downloading $url".PHP_EOL; 
//file_put_contents($tmp_jar, file_get_contents($url));

//unzip jar

//delete META-INF

//download and unpack jar mods

//rezip jar

//move jar into minecraft folder


/****** do mods ******/
echo $mods_folder = $mc_dir."mods";

//copy out minimap settings
if (is_dir($rei_dir = "$mods_folder".$ds."rei_minimap")) {
  mkdir($tmp_dir = $mc_dir."tmp");
  exec("cp -r '$rei_dir' '$tmp_dir'");
}

//backup or create mods folder
var_dump($mods_folder);
if (is_dir($mods_folder)) {
  backup($mods_folder, $mc_dir);
  exec("rm -rf '$mods_folder'".$ds."*");
}
else {
  mkdir($mods_folder);
}

//copy in minimap settings
if (isset($tmp_dir)) exec("cp -r '$tmp_dir'".$ds."* $mods_folder'");

foreach($json->common->mods as $mod) {

}


function backup($folder, $dest) {
  $name = basename($folder);
  $zipfile = "$dest".$ds."$name-". date('d-M-Y_H-i-s') .".zip";
  echo "backing up $folder to $zipfile\n";
  if (!class_exists('ZipArchive')) { //assume windows for now
    exec("7z.exe a -tzip $zipfile $folder\\*");
  }
  else {
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
}
