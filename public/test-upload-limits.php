<?php
/**
 * Test PHP Upload Limits
 * Visit this page to check if your PHP configuration is working
 */

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Upload Limits Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .config-table { border-collapse: collapse; width: 100%; }
        .config-table th, .config-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .config-table th { background-color: #f2f2f2; }
        .status-ok { color: green; font-weight: bold; }
        .status-warning { color: orange; font-weight: bold; }
        .status-error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>PHP Upload Configuration Test</h1>
    
    <h2>Current PHP Settings</h2>
    <table class="config-table">
        <tr>
            <th>Setting</th>
            <th>Current Value</th>
            <th>Recommended</th>
            <th>Status</th>
        </tr>
        
        <?php
        $settings = [
            'upload_max_filesize' => ['recommended' => '50M', 'min' => parseSize('50M')],
            'post_max_size' => ['recommended' => '500M', 'min' => parseSize('500M')],
            'max_file_uploads' => ['recommended' => '200', 'min' => 200],
            'max_execution_time' => ['recommended' => '600', 'min' => 600],
            'max_input_time' => ['recommended' => '600', 'min' => 600],
            'memory_limit' => ['recommended' => '512M', 'min' => parseSize('512M')],
            'max_input_vars' => ['recommended' => '5000', 'min' => 5000],
        ];
        
        foreach ($settings as $setting => $config) {
            $current = ini_get($setting);
            $currentParsed = in_array($setting, ['upload_max_filesize', 'post_max_size', 'memory_limit']) 
                ? parseSize($current) 
                : (int)$current;
            
            $status = 'status-ok';
            $statusText = 'OK';
            
            if ($currentParsed < $config['min']) {
                $status = 'status-error';
                $statusText = 'TOO LOW';
            } elseif ($currentParsed < ($config['min'] * 0.8)) {
                $status = 'status-warning';
                $statusText = 'LOW';
            }
            
            echo "<tr>";
            echo "<td>{$setting}</td>";
            echo "<td>" . (in_array($setting, ['upload_max_filesize', 'post_max_size', 'memory_limit']) ? formatBytes($currentParsed) : $current) . "</td>";
            echo "<td>{$config['recommended']}</td>";
            echo "<td class='{$status}'>{$statusText}</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <h2>Upload Test</h2>
    <p>Maximum theoretical upload size: <strong><?php echo formatBytes(min(parseSize(ini_get('upload_max_filesize')), parseSize(ini_get('post_max_size')))); ?></strong></p>
    <p>Maximum files per upload: <strong><?php echo ini_get('max_file_uploads'); ?></strong></p>
    
    <h2>Recommendations</h2>
    <ul>
        <li>If any settings show "TOO LOW", your uploads may fail</li>
        <li>For 200 photos at 5MB each, you need at least 1GB post_max_size</li>
        <li>The chunked upload solution uploads photos one by one to avoid these limits</li>
        <li>If settings are still low, contact your hosting provider or update your server configuration</li>
    </ul>
    
    <p><a href="javascript:history.back()">← Back to Application</a></p>
</body>
</html>