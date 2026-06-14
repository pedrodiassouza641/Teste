<?php
// Configura o cabeçalho para o aplicativo de IPTV entender que isso é uma lista M3U
header('Content-Type: application/vnd.apple.mpegurl');
header('Content-Disposition: inline; filename="lista.m3u"');

$url_original = "https://pop25.live/278385497/095291134/21236";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_original);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Mantém a leitura dos cabeçalhos de resposta
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Não deixa seguir automaticamente para capturarmos o link intermediário

// Força o cURL a usar o mesmo User-Agent que o seu Chrome usou para gerar o token
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');

// Define um timeout maior, já que você mencionou que o link "demora um pouco" para responder
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$resposta = curl_exec($ch);
curl_close($ch);

// Procura a linha "Location:" dentro do cabeçalho retornado pelo servidor
$url_com_token = "";
if (preg_match('/^Location:\s*(.*)$/mi', $resposta, $matches)) {
    $url_com_token = trim($matches[1]);
}

// Se o método anterior falhar, tenta o método nativo do cURL como segunda opção
if (empty($url_com_token)) {
    $info = curl_getinfo($ch);
    $url_com_token = $info['redirect_url'];
}

// Se encontramos a URL com o token, fazemos a substituição e baixamos a lista
if (!empty($url_com_token)) {
    
    // Altera o IP antigo (209) pelo novo IP (210)
    $url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);

    // Faz a nova requisição para baixar o arquivo M3U final usando o IP novo e o token gerado
    $ch_lista = curl_init();
    curl_setopt($ch_lista, CURLOPT_URL, $url_corrigida);
    curl_setopt($ch_lista, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_lista, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch_lista, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_lista, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');
    
    $conteudo_m3u = curl_exec($ch_lista);
    curl_close($ch_lista);

    // Exibe o conteúdo do arquivo M3U na tela
    echo $conteudo_m3u;

} else {
    // Se ainda assim der erro, exibe o que o servidor respondeu para podermos analisar
    echo "#EXTM3U\n#EXTINF:-1,Erro ao capturar o redirecionamento. Resposta recebida:\n";
    echo "# " . substr(strip_tags($resposta), 0, 200);
}
exit;
?>
