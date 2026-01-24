<?php
echo "Apache is working! PHP version: " . phpversion();
echo "<br>Document Root: " . $_SERVER['DOCUMENT_ROOT'];
echo "<br>Request URI: " . $_SERVER['REQUEST_URI'];
echo "<br>Script Filename: " . $_SERVER['SCRIPT_FILENAME'];
