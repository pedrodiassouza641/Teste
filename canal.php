<?php
// Configura o cabeçalho para o aplicativo de IPTV entender que isso é uma lista M3U
header('Content-Type: application/vnd.apple.mpegurl');
header('Content-Disposition: inline; filename="lista.m3u"');

//// 1. Faz a requisição para o pop25 original simulando um navegador real
$url_original = "https://pop25.live/278385497/095291134/21236";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_original);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

// ESSA LINHA SIMULA UM NAVEGADOR CHROME NO WINDOWS:
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

$resposta = curl_exec($ch);

// 2. Pega a URL de redirecionamento gerada com o token
$info = curl_getinfo($ch);
$url_com_token = $info['redirect_url']; 
curl_close($ch);

if (!empty($url_com_token)) {
    // 3. Modifica o IP antigo pelo novo mantendo o token gerado intacto
    $url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);

    // 4. Nova etapa: Faz o cURL baixar o CONTEÚDO (o m3u_plus) da URL corrigida
    $ch_lista = curl_init();
    curl_setopt($ch_lista, CURLOPT_URL, $url_corrigida);
    curl_setopt($ch_lista, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_lista, CURLOPT_FOLLOWLOCATION, true); // Permite seguir redirecionamentos internos se houver
    curl_setopt($ch_lista, CURLOPT_SSL_VERIFYPEER, false); // Evita problemas com SSL
    
    $conteudo_m3u = curl_exec($ch_lista);
    curl_close($ch_lista);

    // 5. Exibe o texto da lista M3U na tela para o aplicativo ler
    echo $conteudo_m3u;
} else {
    echo "#EXTM3U\n#EXTINF:-1,Erro ao gerar o token original";
}
exit;
?>
