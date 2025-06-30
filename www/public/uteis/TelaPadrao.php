<?php
class TelaPadrao {
    protected function cabecalho() {
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <title>Título Padrão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../../uteis/include/TelaPadrao.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="../../classes/telaInicial/TelaInicial.php">
            <img src="../../../imagens/logo.jpeg" alt="Logo" width="40" height="30" class="d-inline-block align-text-top">
            Pagina Inicial
        </a>
        <?php if (isset($_SESSION['nome'])): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../../classes/cultura/Cultura.php">Culturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../../classes/lancamento/Lancamento.php">Movimentações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../classes/financeiro/TelaGestaoFinanceira.php">Financeiro</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../../classes/login/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container-sm custom-container rounded-top-4 bg-secondary-subtle">
<?php
    }

    protected function rodape() {
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<?php
    }

    public function renderizar($conteudoCallback) {
        $_SESSION['id_propriedade'] = 1;

        $this->cabecalho();
        $conteudoCallback();
        $this->rodape();
    }
}
?>
