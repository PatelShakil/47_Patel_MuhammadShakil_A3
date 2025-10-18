<?php
$directory = './'; // Current directory
$files = scandir($directory);

echo "<h2>Files in Directory: $directory</h2>";
echo "<ul>";

foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $filePath = $directory . $file;
        $fileSize = filesize($filePath);
        $fileType = is_dir($filePath) ? 'Directory' : 'File';
        $lastModified = date("Y-m-d H:i:s", filemtime($filePath));
        
        echo "<li>";
        echo "<strong>$file</strong> - ";
        echo "Type: $fileType | ";
        echo "Size: " . number_format($fileSize) . " bytes | ";
        echo "Modified: $lastModified";
        echo "</li>";
    }
}

echo "</ul>";
?>
