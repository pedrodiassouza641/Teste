<?php
// 1. Captura o ID do canal que o aplicativo quer assistir.
// Se você chamar: canais.php?id=21236, ele vai entender.
$id_canal = isset($_GET['id']) ? $_GET['id'] : '21236';

$username = "278385497";
$password = "095291134";

// 2. Monta a URL original do canal
$url_original = "https://pop25.live/{$username}/{$password}/{$id_canal}";

// 3. Faz a requisição rápida APENAS para pegar o cabeçalho de redirecionamento (Location)
// Importante: Passamos os dados do cliente (como o IP dele e o User-Agent) para o token ser gerado para ELE, e não para o Reader
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_original);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

// Repassa o navegador do usuário para o pop25 para evitar o 403 do token
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
}

$resposta = curl_exec($ch);
curl_close($ch);

// 4. Captura a URL com o token gerado pelo pop25
$url_com_token = "";
if (preg_match('/^Location:\s*(.*)$/mi', $resposta, $matches)) {
    $url_com_token = trim($matches[1]);
}

// 5. Se o token foi gerado, trocamos o IP inválido pelo novo e redirecionamos o player do IPTV
if (!empty($url_com_token)) {
    $url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);
    header("Location: " . $url_corrigida);
    exit;
} else {
    // Se o pop25 bloquear o Reader por IP de qualquer forma, usamos o redirecionamento forçado por texto (Plano B)
    // Esse método tenta mandar o aplicativo direto pro IP novo usando a estrutura padrão, caso o cURL faleça.
    $url_forcada = "http://38.135.26.210/{$username}/{$password}/{$id_canal}";
    header("Location: " . $url_forcada);
    exit;
}
?>
