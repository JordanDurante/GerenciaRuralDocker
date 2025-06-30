<?php
require_once '../../uteis/TelaPadrao.php';
error_reporting(1);

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit;
}

class TelaInicial extends TelaPadrao {
    public function conteudo() {
?>
<h1>Tela Inicial</h1>
<div class="news-section">
    <h2>Últimas Notícias</h2>
    <div class="row" id="news-content">
        <!-- Notícias serão carregadas aqui -->
    </div>
</div>
<div class="quotes-section mt-4">
    <h2>Cotações de Produtos</h2>
    <div class="row" id="quotes-content">
        <!-- Cotações serão carregadas aqui -->
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("../../uteis/obterDados/ObterDados.php?tipo=noticias")
            .then(response => response.json())
            .then(data => {
                const newsContent = document.getElementById("news-content");
                if (data.length === 0) {
                    newsContent.innerHTML = "<p>Nenhuma notícia disponível.</p>";
                } else {
                    data.forEach(noticia => {
                        const noticiaElement = document.createElement("div");
                        noticiaElement.className = "col-md-6 mb-4";
                        noticiaElement.innerHTML = `
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">${noticia.titulo}</h5>
                                    <p class="card-text">${noticia.conteudo}</p>
                                </div>
                            </div>
                        `;
                        newsContent.appendChild(noticiaElement);
                    });
                }
            })
            .catch(error => console.error('Erro ao carregar notícias:', error));

        fetch("../../uteis/obterDados/ObterDados.php?tipo=cotacoes")
            .then(response => response.json())
            .then(data => {
                const quotesContent = document.getElementById("quotes-content");
                if (data.length === 0) {
                    quotesContent.innerHTML = "<p>Nenhuma cotação disponível.</p>";
                } else {
                    const cotacoesElement = document.createElement("div");
                    cotacoesElement.className = "col-12";
                    cotacoesElement.innerHTML = data.map(cotacao => `
                        <span>${cotacao.produto}: ${cotacao.preco}</span>
                    `).join(' | ');
                    quotesContent.appendChild(cotacoesElement);
                }
            })
            .catch(error => console.error('Erro ao carregar cotações:', error));
    });
</script>
<?php
    }
}

$pagina = new TelaInicial();
$pagina->renderizar([$pagina, 'conteudo']);
?>
