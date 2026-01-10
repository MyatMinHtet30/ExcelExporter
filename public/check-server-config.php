<?php
/**
 * Server Configuration Diagnostic Tool
 * This helps identify why POST limits are still being hit
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

// Test POST data
$postData = '';
$postSize = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents('php://input');
    $postSize = strlen($postData);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Configuration Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .config-table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        .config-table th, .config-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .config-table th { background-color: #f2f2f2; }
        .status-ok { color: green; font-weight: bold; }
        .status-warning { color: orange; font-weight: bold; }
        .status-error { color: red; font-weight: bold; }
        .test-section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .alert { padding: 15px; margin: 20px 0; border-radius: 4px; }
        .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    </style>
</head>
<body>
    <h1>Server Configuration Diagnostic</h1>
    
    <?php if ($postSize > 0): ?>
        <div class="alert alert-info">
            <strong>POST Test Result:</strong> Received <?php echo formatBytes($postSize); ?> of POST data successfully!
        </div>
    <?php endif; ?>
    
    <h2>Current PHP Settings</h2>
    <table class="config-table">
        <tr>
            <th>Setting</th>
            <th>Current Value</th>
            <th>Recommended</th>
            <th>Status</th>
            <th>Notes</th>
        </tr>
        
        <?php
        $settings = [
            'upload_max_filesize' => ['recommended' => '50M', 'min' => parseSize('50M')],
            'post_max_size' => ['recommended' => '1000M', 'min' => parseSize('1000M')],
            'max_file_uploads' => ['recommended' => '200', 'min' => 200],
            'max_execution_time' => ['recommended' => '900', 'min' => 900],
            'max_input_time' => ['recommended' => '900', 'min' => 900],
            'memory_limit' => ['recommended' => '1024M', 'min' => parseSize('1024M')],
            'max_input_vars' => ['recommended' => '10000', 'min' => 10000],
        ];
        
        foreach ($settings as $setting => $config) {
            $current = ini_get($setting);
            $currentParsed = in_array($setting, ['upload_max_filesize', 'post_max_size', 'memory_limit']) 
                ? parseSize($current) 
                : (int)$current;
            
            $status = 'status-ok';
            $statusText = 'OK';
            $notes = '';
            
            if ($currentParsed < $config['min']) {
                $status = 'status-error';
                $statusText = 'TOO LOW';
                $notes = 'This will cause upload failures!';
            } elseif ($currentParsed < ($config['min'] * 0.8)) {
                $status = 'status-warning';
                $statusText = 'LOW';
                $notes = 'May cause issues with large uploads';
            }
            
            echo "<tr>";
            echo "<td>{$setting}</td>";
            echo "<td>" . (in_array($setting, ['upload_max_filesize', 'post_max_size', 'memory_limit']) ? formatBytes($currentParsed) : $current) . "</td>";
            echo "<td>{$config['recommended']}</td>";
            echo "<td class='{$status}'>{$statusText}</td>";
            echo "<td>{$notes}</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <div class="test-section">
        <h3>POST Size Test</h3>
        <p>This will test if your server can handle large POST requests:</p>
        
        <form method="POST" action="">
            <label>
                Test Size: 
                <select name="test_size" id="test_size">
                    <option value="1">1 MB</option>
                    <option value="5">5 MB</option>
                    <option value="10" selected>10 MB</option>
                    <option value="20">20 MB</option>
                    <option value="50">50 MB</option>
                    <option value="100">100 MB</option>
                </select>
            </label>
            <button type="button" onclick="testPostSize()">Test POST Size</button>
        </form>
        
        <div id="test-result"></div>
    </div>
    
    <h2>Server Information</h2>
    <table class="config-table">
        <tr><td>PHP Version</td><td><?php echo PHP_VERSION; ?></td></tr>
        <tr><td>Server Software</td><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td></tr>
        <tr><td>Document Root</td><td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td></tr>
        <tr><td>PHP SAPI</td><td><?php echo php_sapi_name(); ?></td></tr>
        <tr><td>Loaded php.ini</td><td><?php echo php_ini_loaded_file() ?: 'None'; ?></td></tr>
        <tr><td>Additional ini files</td><td><?php echo php_ini_scanned_files() ?: 'None'; ?></td></tr>
    </table>
    
    <h2>Configuration Files Check</h2>
    <table class="config-table">
        <?php
        $configFiles = [
            '.htaccess' => 'Apache configuration',
            'php.ini' => 'PHP configuration',
            '.user.ini' => 'User PHP configuration'
        ];
        
        foreach ($configFiles as $file => $description) {
            $exists = file_exists($file);
            $status = $exists ? 'status-ok' : 'status-warning';
            $statusText = $exists ? 'EXISTS' : 'NOT FOUND';
            
            echo "<tr>";
            echo "<td>{$file}</td>";
            echo "<td>{$description}</td>";
            echo "<td class='{$status}'>{$statusText}</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <div class="alert alert-info">
        <h3>Troubleshooting Steps:</h3>
        <ol>
            <li>If settings show "TOO LOW", your configuration files aren't being loaded</li>
            <li>Contact your hosting provider to increase PHP limits</li>
            <li>Try uploading photos one by one using the chunked upload system</li>
            <li>For HEIC files, the system will convert them to JPEG automatically</li>
        </ol>
    </div>
    
    <script>
        function testPostSize() {
            const size = document.getElementById('test_size').value;
            const resultDiv = document.getElementById('test-result');
            
            resultDiv.innerHTML = '<p>Testing ' + size + 'MB POST request...</p>';
            
            // Create test data
            const testData = 'x'.repeat(size * 1024 * 1024);
            
            fetch(window.location.href, {
                method: 'POST',
                body: testData,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => {
                if (response.ok) {
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ SUCCESS: Server accepted ' + size + 'MB POST request!</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger">❌ FAILED: Server rejected ' + size + 'MB POST request (Status: ' + response.status + ')</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger">❌ ERROR: ' + error.message + '</div>';
            });
        }
    </script>
    
    <p><a href="javascript:history.back()">← Back to Application</a></p>
</body>
</html>