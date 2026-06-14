<?php
// Configura o cabeçalho para o aplicativo de IPTV entender que isso é uma lista M3U
header('Content-Type: application/vnd.apple.mpegurl');
header('Content-Disposition: inline; filename="tv_channels_278385497_plus.m3u"');

$username = "278385497";
$password = "095291134";

// 1. Montamos a URL do Xtream Codes que gera a lista completa de canais
$url_lista_original = "http://pop25.live:80/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus";

// 2. Fazemos o cURL baixar o arquivo .m3u original enviado pelo pop25
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_lista_original);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Fingimos ser o Chrome para o servidor não desconfiar
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');

$conteudo_m3u = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 3. Se o servidor do pop25 responder com sucesso, fazemos a mágica da substituição
if ($http_code == 200 && !empty($conteudo_m3u)) {
    
    // Procuramos por "pop25.live" dentro de todos os links da lista e trocamos pelo novo IP "38.135.26.210"
    // Isso vai corrigir o link de TODOS os canais automaticamente antes de enviar para o seu aplicativo
    $lista_corrigida = str_replace("https://pop25.live", "http://38.135.26.210", $conteudo_m3u);
    $lista_corrigida = str_replace("http://pop25.live", "http://38.135.26.210", $lista_corrigida);
    
    // Também garante que se houver o IP antigo (.209) no texto, ele vire o novo (.210)
    $lista_corrigida = str_replace("38.135.26.209", "38.135.26.210", $lista_corrigida);

    // Entrega a lista prontinha e modificada para o aplicativo de IPTV
    echo $lista_corrigida;

} else {
    // Caso o servidor bloqueie o Reader no download da lista, usamos uma lista de contingência baseada em texto
    echo "#EXTM3U\n";
    echo "#EXTINF:-1, [ERRO] O servidor pop25 bloqueou o Reader (Erro HTTP " . $http_code . ")\n";
    echo "http://38.135.26.210:80/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus\n";
}
exit;
?>
