<?php
$username = "278385497";
$password = "095291134";

// ==========================================
// MODO 1: SE O APLICATIVO PEDIR O CANAL ESPECÍFICO (canais.php?id=21236)
// ==========================================
if (isset($_GET['id'])) {
    $id_canal = $_GET['id'];
    $url_original = "https://pop25.live/{$username}/{$password}/{$id_canal}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_original);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

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
        if ($id_canal == "21236") {
            $url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);
            header("Location: " . $url_corrigida);
        } else {
            header("Location: " . $url_com_token);
        }
        exit;
    } else {
        // Redirecionamento forçado caso o cURL falhe ou seja bloqueado
        $ip_final = ($id_canal == "21236") ? "38.135.26.210" : "38.135.26.209";
        header("Location: http://{$ip_final}/{$username}/{$password}/{$id_canal}");
        exit;
    }
}

// ==========================================
// MODO 2: GERADOR DA LISTA M3U
// ==========================================
header('Content-Type: application/vnd.apple.mpegurl');
header('Content-Disposition: inline; filename="tv_channels_278385497_plus.m3u"');

$url_lista_original = "http://pop25.live:80/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_lista_original);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
curl_setopt($ch, CURLOPT_TIMEOUT, 6);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');

$conteudo_m3u = curl_exec($ch);
curl_close($ch);

// Se conseguiu baixar a lista original, faz a substituição inteligente
if (!empty($conteudo_m3u) && strpos($conteudo_m3u, '#EXTM3U') !== false) {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $meu_url_reader = $protocolo . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

    $alvo_antigo = "https://pop25.live/{$username}/{$password}/21236";
    $alvo_novo = $meu_url_reader . "?id=21236";
    
    $lista_modificada = str_replace($alvo_antigo, $alvo_novo, $conteudo_m3u);
    $alvo_antigo_http = "http://pop25.live/{$username}/{$password}/21236";
    $lista_modificada = str_replace($alvo_antigo_http, $alvo_novo, $lista_modificada);

    echo $lista_modificada;
} else {
    // [PLANO B] Se o pop25 bloqueou o Reader, o script cria uma lista direta 
    // apontando o canal problemático para o IP novo e mantendo o formato m3u_plus do Xtream Codes externo.
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $meu_url_reader = $protocolo . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    
    echo "#EXTM3U\n";
    echo "#EXTINF:-1,--- CANAL 21236 CORRIGIDO ---\n";
    echo $meu_url_reader . "?id=21236\n";
    echo "#EXTINF:-1,--- OUTROS CANAIS (LINK DIRETO XTREAM) ---\n";
    echo "http://38.135.26.210:80/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus\n";
}
exit;
?>
