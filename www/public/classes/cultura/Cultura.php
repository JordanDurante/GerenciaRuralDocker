<?php
require_once '../../uteis/TelaPadrao.php';
require_once '../../../config/bd_connection.php';
error_reporting(1);

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit;
}

class Cultura extends TelaPadrao {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function obterCulturas($idPropriedade) {
        $stmt = $this->conn->prepare("SELECT c.id, c.nome
                                      FROM cultura c
                                      LEFT JOIN propriedade_cultura pc ON c.id = pc.id_cultura
                                      WHERE pc.id_propriedade = ?");
        $stmt->bind_param("i", $idPropriedade);
        $stmt->execute();
        $result = $stmt->get_result();
        $culturas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $culturas;
    }

    private function excluirCultura($idCultura) {
        $stmt = $this->conn->prepare("DELETE FROM cultura WHERE id = ?");
        $stmt->bind_param("i", $idCultura);
        $stmt->execute();

        $stmt->close();
    }

    private function salvarEdicaoCultura($idCultura, $nome) {
        $stmt = $this->conn->prepare("UPDATE cultura SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $idCultura);
        $stmt->execute();
        $stmt->close();
    }

    private function adicionarCultura($nome, $idPropriedade) {
        // Adicionar nova cultura
        $stmt = $this->conn->prepare("INSERT INTO cultura (nome, id_propriedade) VALUES (?,?)");
        $stmt->bind_param("si", $nome, $idPropriedade);
        $stmt->execute();
        $idCultura = $stmt->insert_id;
        $stmt->close();

    }

    public function conteudo() {
        $idPropriedade = $_SESSION['id_propriedade'];
        $culturas = $this->obterCulturas($idPropriedade);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['confirmar_excluir'])) {
                $idCultura = intval($_POST['id']);
                $this->excluirCultura($idCultura);
                header("Location: cultura.php");
                exit();
            } elseif (isset($_POST['editar'])) {
                $idCultura = intval($_POST['id']);
                $nome = $_POST['nome'];
                $this->salvarEdicaoCultura($idCultura, $nome);
                header("Location: cultura.php");
                exit();
            } elseif (isset($_POST['adicionar'])) {
                $nome = $_POST['nome'];
                $this->adicionarCultura($nome, $idPropriedade);
                header("Location: cultura.php");
                exit();
            }
        }
?>
<h1>Culturas</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>A��es</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($culturas as $cultura): ?>
        <tr>
            <td><?php echo htmlspecialchars($cultura['id'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($cultura['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarExclusaoModal<?php echo $cultura['id']; ?>">
                    Excluir
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $cultura['id']; ?>">
                    Editar
                </button>

                <div class="modal fade" id="editarModal<?php echo $cultura['id']; ?>" tabindex="-1" aria-labelledby="editarModalLabel<?php echo $cultura['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarModalLabel<?php echo $cultura['id']; ?>">Editar Cultura</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo $cultura['id']; ?>">
                                    <div class="mb-3">
                                        <label for="nome<?php echo $cultura['id']; ?>" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="nome<?php echo $cultura['id']; ?>" name="nome" value="<?php echo htmlspecialchars($cultura['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <button type="submit" name="editar" class="btn btn-success">Salvar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="confirmarExclusaoModal<?php echo $cultura['id']; ?>" tabindex="-1" aria-labelledby="confirmarExclusaoModalLabel<?php echo $cultura['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmarExclusaoModalLabel<?php echo $cultura['id']; ?>">Confirmar Exclus�o</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Tem certeza que deseja excluir a cultura <?php echo htmlspecialchars($cultura['nome'], ENT_QUOTES, 'UTF-8'); ?>?
                            </div>
                            <div class="modal-footer">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo $cultura['id']; ?>">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" name="confirmar_excluir" class="btn btn-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarModal">
    Adicionar Nova Cultura
</button>

<div class="modal fade" id="adicionarModal" tabindex="-1" aria-labelledby="adicionarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarModalLabel">Adicionar Nova Cultura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="nomeAdicionar" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeAdicionar" name="nome" required>
                    </div>
                    <button type="submit" name="adicionar" class="btn btn-success">Adicionar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php
    }
}

session_start();

$pagina = new Cultura($conn);
$pagina->renderizar([$pagina, 'conteudo']);
?>
