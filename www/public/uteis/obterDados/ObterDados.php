<?php

function obterNoticias() {
    // Simulando a obten��o de not�cias de uma API ou base de dados
    $noticias = [
        ['titulo' => 'Not�cia 1', 'conteudo' => 'Conte�do da not�cia 1'],
        ['titulo' => 'Not�cia 2', 'conteudo' => 'Conte�do da not�cia 2'],
    ];
    return array_map(function($noticia) {
        return array_map('utf8_encode', $noticia);
    }, $noticias);
}

function obterCotacoes() {
    // Simulando a obten��o de cota��es de uma API ou base de dados
    $cotacoes = [
        ['produto' => 'Produto 1', 'preco' => 'R$ 10,00'],
        ['produto' => 'Produto 2', 'preco' => 'R$ 20,00'],
    ];
    return array_map(function($cotacao) {
        return array_map('utf8_encode', $cotacao);
    }, $cotacoes);
}

header('Content-Type: application/json');

if ($_GET['tipo'] == 'noticias') {
    $noticias = obterNoticias();
    error_log('Noticias: ' . print_r($noticias, true));
    echo json_encode($noticias);
} elseif ($_GET['tipo'] == 'cotacoes') {
    $cotacoes = obterCotacoes();
    error_log('Cotacoes: ' . print_r($cotacoes, true));
    echo json_encode($cotacoes);
} else {
    error_log('Tipo n�o especificado corretamente');
    echo json_encode([]);
}
?>
