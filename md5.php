#!/usr/bin/env php5
<?php
// Script for quickly adding/updating plugins
// Run from root of repo with PHP5

// First parameter is a zip file you've put in the htdocs dir
// It'll remove the .zip extension, rename to have the MD5, update the .md5 file
// and update data.json

// If the plugin doesn't yet exist, author name and GG2F topic are filled in as
// blanks

$filename = $argv[1];
$pluginname = basename($filename, '.zip');

$plugin_data = file_get_contents('data.json');
if ($plugin_data === FALSE)
    die("Error: Couldn't open data.json file.");
$plugin_data = json_decode($plugin_data, true);
$plugins = &$plugin_data['plugins'];

$md5 = file_get_contents($filename);
if ($md5 === FALSE)
    die("Error: Couldn't open plugin file.");
$md5 = md5($md5);

if (rename($filename, "htdocs/$pluginname@$md5.zip") === FALSE)
    die("Error: Renaming file failed.");
if (file_put_contents("htdocs/$pluginname.md5", $md5) === FALSE)
    die("Error: Creating .md5 file failed.");

// No key for this plugin yet
if (!array_key_exists($pluginname, $plugins)) {
    echo "Not yet any plugin with the name \"$pluginname\", adding with blank fields (change these yourself)." . PHP_EOL;
    $plugins[$pluginname] = [
        'author' => '',
        'topic' => '',
        'md5s' => [
            $md5
        ]
    ];
} else {
    echo "Already a plugin with the name \"$pluginname\", adding new MD5." . PHP_EOL;
    array_unshift($plugins[$pluginname]['md5s'], $md5);
}

if (file_put_contents('data.json', json_encode($plugin_data, JSON_PRETTY_PRINT)) === FALSE)
    die("Error! Couldn't save data.json.");
