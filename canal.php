<?php
// 1. Faz a requisição para o pop25 original
$url_original = "https://pop25.live/278385497/095291134/21236";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_original);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');

$resposta = curl_exec($ch);
curl_close($ch);

$url_com_token = "";
if (preg_match('/^Location:\s*(.*)$/mi', $resposta, $matches)) {
    $url_com_token = trim($matches[1]);
}

// Se o servidor aceitar a requisição inicial e gerar a URL
if (!empty($url_com_token)) {
    // Corrige o IP mantendo o token
    $url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);
    
    // Redireciona o aplicativo direto para o link final corrigido
    header("Location: " . $url_corrigida);
    exit;
} else {
    // Se até a requisição inicial for bloqueada com 403, usaremos a Solução 2
    header("HTTP/1.1 403 Forbidden");
    echo "Erro: O servidor de origem bloqueou a conexao do Reader.";
    exit;
}
?>
