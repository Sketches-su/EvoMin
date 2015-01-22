<?php

defined('MODX_BASE_PATH') or die();
$basePath = MODX_BASE_PATH;

// ---------------------------------------------------------------------------

// Take relative path into account
$rel = empty($rel) ? '' : $rel;

// Validate input
if (empty($from)) {
    return '?source-files-not-specified';
}

if (empty($to)) {
    return '?destination-file-not-specified';
}

if (!in_array($mode = strrchr($to, '.'), array('.css', '.js'))) {
    return '?wrong-output-file-type';
}

// Obtain the list of source files
$from = array_map('trim', explode(',', $from));

// Get their timestamps
$tstamp = array();
$baseUrls = array();

foreach ($from as $one) {
    $file = $basePath.$rel.$one;
    $tstamp[$file] = filemtime($file);
    $baseUrls[$file] = rtrim(dirname(MODX_BASE_URL.$rel.$one), '\\/').'/';
}

// Hash the timestamps and compare with the saved one
$need = false;
$hash = md5(serialize($tstamp));

if (file_exists($output = $basePath.$rel.$to)) {
    $h = fopen($output, 'r');
    $row = fgets($h);
    fclose($h);
    $need = (false === strpos($row, $hash));
} else {
    $need = true;
}

// If we need to minify, do it
if ($need) {
    // Load, minify and concatenate all files
    $buf = '';
    
    foreach (array_keys($tstamp) as $file) {
        if ($buf) {
            $buf .= ($mode == '.js') ? ';' : ' ';
        }
        
        $data = file_get_contents($file);
        
        if (empty($bypass)) {
            switch ($mode) {
                case '.css':
                    if (!class_exists('CssMin', false)) {
                        require dirname(__FILE__).'/cssmin.php';
                    }
                    
                    $buf .= CssMin::minify($data, array(), array(
                        'UrlTimePrefix' => array(
                            'BaseUrl' => $baseUrls[$file]
                        )
                    ));
                    break;
                
                case '.js':
                    if (!class_exists('JSMinPlus', false)) {
                        require dirname(__FILE__).'/jsminplus.php';
                    }
                    
                    $buf .= JSMinPlus::minify($data);
                    break;
                
                default:
                    // nothing
            }
        }
    }
    
    
    file_put_contents($output, "/* $hash */\n$buf");
    
    if (!empty($gzip)) {
        file_put_contents($output.'.gz', gzencode($buf, 9));
    }
}

return $rel.$to.'?'.$hash;

