<?php
// 1. Faz a requisição para o pop25 original para ele gerar o token
$url_original = "https://pop25.live/278385497/095291134/21236";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_original);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Não deixa seguir o redirecionamento antigo
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$resposta = curl_exec($ch);

// 2. Pega a URL de redirecionamento gerada com o token
$info = curl_getinfo($ch);
$url_com_token = $info['redirect_url']; 
curl_close($ch);

// 3. Modifica o IP antigo pelo novo mantendo o token gerado intacto
$url_corrigida = str_replace("38.135.26.209", "38.135.26.210", $url_com_token);

// 4. Redireciona o seu aplicativo de IPTV para a URL certa com o token válido
header("Location: " . $url_corrigida);
exit;
?>
