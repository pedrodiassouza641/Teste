<?php
$username = "278385497";
$password = "095291134";

// ==========================================
// MODO 1: SE O APLICATIVO PEDIR UM CANAL ESPECÍFICO (Ex: canais.php?id=21236)
// ==========================================
if (isset($_GET['id'])) {
    $id_canal = $_GET['id'];
    $url_original = "https://pop25.live/{$username}/{$password}/{$id_canal}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_original);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    }

    $resposta = curl_exec($ch);
    curl_close($ch);

    $url_com_token = "";
    if (preg_match('/^Location:\s*(.*)$/mi', $resposta, $matches)) {
        $url_com_token = trim($matches[1]);
    }

    if (!empty($url_com_token)) {
        // Se for o canal problemático, faz a troca do IP antigo pelo novo
        if ($id_canal == "21236") {
            $url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);
            header("Location: " . $url_corrigida);
        } else {
            // Se for qualquer outro canal, segue o redirecionamento original gerado
            header("Location: " . $url_com_token);
        }
        exit;
    } else {
        // Plano B caso o cURL dê erro de timeout ou bloqueio temporário
        $ip_final = ($id_canal == "21236") ? "38.135.26.210" : "38.135.26.209";
        header("Location: http://{$ip_final}/{$username}/{$password}/{$id_canal}");
        exit;
    }
}

// ==========================================
// MODO 2: COMPORTAMENTO PADRÃO (Gera o arquivo M3U alterando apenas o canal 21236)
// ==========================================
header('Content-Type: application/vnd.apple.mpegurl');
header('Content-Disposition: inline; filename="tv_channels_278385497_plus.m3u"');

$url_lista_original = "http://pop25.live:80/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_lista_original);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');

$conteudo_m3u = curl_exec($ch);
curl_close($ch);

if (!empty($conteudo_m3u)) {
    // Descobre qual é o domínio atual do seu Reader dinamicamente
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $meu_url_reader = $protocolo . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

    // Altera EXCLUSIVAMENTE o link do canal 21236 para passar pelo seu Reader
    $alvo_antigo = "https://pop25.live/{$username}/{$password}/21236";
    $alvo_novo = $meu_url_reader . "?id=21236";
    
    $lista_modificada = str_replace($alvo_antigo, $alvo_novo, $conteudo_m3u);

    // Também faz o mesmo para o caso de estar com http simples no arquivo original
    $alvo_antigo_http = "http://pop25.live/{$username}/{$password}/21236";
    $lista_modificada = str_replace($alvo_antigo_http, $alvo_novo, $lista_modificada);

    echo $lista_modificada;
} else {
    echo "#EXTM3U\n#EXTINF:-1, Erro ao carregar lista mae.";
}
exit;
?>
