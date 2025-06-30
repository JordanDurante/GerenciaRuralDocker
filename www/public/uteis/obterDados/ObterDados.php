<?php

function obterNoticias() {
    // Simulando a obtenção de notícias de uma API ou base de dados
    $noticias = [
        ['titulo' => 'Notícia 1', 'conteudo' => 'Conteúdo da notícia 1'],
        ['titulo' => 'Notícia 2', 'conteudo' => 'Conteúdo da notícia 2'],
    ];
    return array_map(function($noticia) {
        return array_map('utf8_encode', $noticia);
    }, $noticias);
}

function obterCotacoes() {
    // Simulando a obtenção de cotações de uma API ou base de dados
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
    error_log('Tipo não especificado corretamente');
    echo json_encode([]);
}
?>
