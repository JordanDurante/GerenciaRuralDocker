<?php
require_once '../../uteis/TelaPadrao.php';
require_once '../../../config/bd_connection.php';
error_reporting(1);

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit;
}

class Lancamento extends TelaPadrao {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function obterLancamentos($idPropriedade) {
        $stmt = $this->conn->prepare("SELECT lf.id, lf.tipo, lf.data, lf.valor, lf.quantidade, lf.unidade AS unidade, c.nome AS cultura, c.id as idCultura
                                      FROM lancamento_financeiro lf
                                      INNER JOIN cultura c ON lf.id_cultura = c.id
                                      WHERE lf.id_propriedade = ?");
        $stmt->bind_param("i", $idPropriedade);
        $stmt->execute();
        $result = $stmt->get_result();
        $lancamentos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $lancamentos;
    }

    private function excluirLancamento($idLancamento) {
        $stmt = $this->conn->prepare("DELETE FROM lancamento_financeiro WHERE id = ?");
        $stmt->bind_param("i", $idLancamento);
        $stmt->execute();
        $stmt->close();
    }

    private function salvarEdicaoLancamento($idLancamento, $tipo, $data, $valor, $quantidade, $idUnidade, $idCultura, $idPropriedade) {
        $stmt = $this->conn->prepare("UPDATE lancamento_financeiro SET tipo = ?, data = ?, valor = ?, quantidade = ?, id_unidade = ?, id_cultura = ?, id_propriedade = ? WHERE id = ?");
        $stmt->bind_param("ssddiiii", $tipo, $data, $valor, $quantidade, $idUnidade, $idCultura, $idPropriedade, $idLancamento);
        $stmt->execute();
        $stmt->close();
    }

    private function adicionarLancamento($tipo, $data, $valor, $quantidade, $idUnidade, $idCultura, $idPropriedade) {
        $stmt = $this->conn->prepare("INSERT INTO lancamento_financeiro (tipo, data, valor, quantidade, id_unidade, id_cultura, id_propriedade) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiii", $tipo, $data, $valor, $quantidade, $idUnidade, $idCultura, $idPropriedade);
        $stmt->execute();
        $stmt->close();
    }

    public function conteudo() {
        $idPropriedade = $_SESSION['id_propriedade'];
        $lancamentos = $this->obterLancamentos($idPropriedade);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['confirmar_excluir'])) {
                $idLancamento = intval($_POST['id']);
                $this->excluirLancamento($idLancamento);
                header("Location: lancamento.php");
                exit();
            } elseif (isset($_POST['editar'])) {
                $idLancamento = intval($_POST['id']);
                $tipo = $_POST['tipo'];
                $data = $_POST['data'];
                $valor = $_POST['valor'];
                $quantidade = $_POST['quantidade'];
                $unidade = $_POST['unidade'];
                $idUnidade = $_POST['idUnidade'];
                $idCultura = $_POST['idCultura'];
                $this->salvarEdicaoLancamento($idLancamento, $tipo, $data, $valor, $quantidade, $idUnidade, $idCultura, $idPropriedade);
                header("Location: lancamento.php");
                exit();
            } elseif (isset($_POST['adicionar'])) {
                $tipo = $_POST['tipo'];
                $data = $_POST['data'];
                $valor = $_POST['valor'];
                $quantidade = $_POST['quantidade'];
                $unidade = $_POST['unidade'];
                $idUnidade = $_POST['idUnidade'];
                $idCultura = $_POST['idCultura'];
                $this->adicionarLancamento($tipo, $data, $valor, $quantidade, $idUnidade, $idCultura, $idPropriedade);
                header("Location: lancamento.php");
                exit();
            }
        }
?>
<h1>Lan�amentos Financeiros</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Data</th>
            <th>Valor</th>
            <th>Quantidade</th>
            <th>Unidade</th>
            <th>Cultura</th>
            <th>A��es</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lancamentos as $lancamento): ?>
        <tr>
            <td><?php echo htmlspecialchars($lancamento['id'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($lancamento['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($lancamento['data'])), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo 'R$ ' . number_format($lancamento['valor'], 2, ',', '.'); ?></td>
            <td><?php echo number_format($lancamento['quantidade'], 2, ',', '.'); ?></td>
            <td><?php echo htmlspecialchars($lancamento['unidade'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($lancamento['cultura'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarExclusaoModal<?php echo $lancamento['id']; ?>">
                    Excluir
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $lancamento['id']; ?>">
                    Editar
                </button>

                <div class="modal fade" id="editarModal<?php echo $lancamento['id']; ?>" tabindex="-1" aria-labelledby="editarModalLabel<?php echo $lancamento['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarModalLabel<?php echo $lancamento['id']; ?>">Editar Lan�amento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo $lancamento['id']; ?>">
                                    <div class="mb-3">
                                        <label for="tipo<?php echo $lancamento['id']; ?>" class="form-label">Tipo</label>
                                        <select class="form-select" id="tipo<?php echo $lancamento['id']; ?>" name="tipo" required>
                                            <option value="entrada" <?php echo ($lancamento['tipo'] == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                                            <option value="saida" <?php echo ($lancamento['tipo'] == 'saida') ? 'selected' : ''; ?>>Sa�da</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="data<?php echo $lancamento['id']; ?>" class="form-label">Data</label>
                                        <input type="date" class="form-control" id="data<?php echo $lancamento['id']; ?>" name="data" value="<?php echo $lancamento['data']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="valor<?php echo $lancamento['id']; ?>" class="form-label">Valor</label>
                                        <input type="text" class="form-control" id="valor<?php echo $lancamento['id']; ?>" name="valor" value="<?php echo $lancamento['valor']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantidade<?php echo $lancamento['id']; ?>" class="form-label">Quantidade</label>
                                        <input type="text" class="form-control" id="quantidade<?php echo $lancamento['id']; ?>" name="quantidade" value="<?php echo $lancamento['quantidade']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="idUnidade<?php echo $lancamento['id']; ?>" class="form-label">Unidade</label>
                                        <select class="form-select" id="unidade<?php echo $lancamento['id']; ?>" name="idUnidade" required>
                                            <?php
                                            $stmt = $this->conn->prepare("SELECT id, nome, descricao FROM unidade");
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            while ($res = $result->fetch_assoc()) {
                                                $unidade = $res['nome'] . ($res['descricao'] ? "(" . $res['descricao'] . ")" : ""); ?>
                                                <option value="<?php echo $res['id']; ?>" <?php echo ($lancamento['idUnidade'] == $res['id']) ? 'selected' : ''; ?>><?php echo $unidade; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="idCultura<?php echo $lancamento['id']; ?>" class="form-label">Cultura</label>
                                        <select class="form-select" id="id_cultura<?php echo $lancamento['id']; ?>" name="idCultura" required>
                                            <?php
                                            $stmt = $this->conn->prepare("SELECT id, nome FROM cultura WHERE id_propriedade = ?");
                                            $stmt->bind_param("i", $idPropriedade);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            while ($res = $result->fetch_assoc()) { ?>
                                                <option value="<?php echo $res['id']; ?>" <?php echo ($lancamento['idCultura'] == $res['id']) ? 'selected' : ''; ?>><?php echo $res['nome']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="editar" class="btn btn-success">Salvar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="confirmarExclusaoModal<?php echo $lancamento['id']; ?>" tabindex="-1" aria-labelledby="confirmarExclusaoModalLabel<?php echo $lancamento['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmarExclusaoModalLabel<?php echo $lancamento['id']; ?>">Confirmar Exclus�o</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Tem certeza que deseja excluir este lan�amento?
                            </div>
                            <div class="modal-footer">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo $lancamento['id']; ?>">
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
    Adicionar Novo
</button>

<div class="modal fade" id="adicionarModal" tabindex="-1" aria-labelledby="adicionarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarModalLabel">Adicionar Novo Lan�amento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Sa�da</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data" required>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade</label>
                        <input type="text" class="form-control" id="quantidade" name="quantidade" required>
                    </div>
                    <div class="mb-3">
                        <label for="idUnidade" class="form-label">Unidade</label>
                        <select class="form-select" id="unidade" name="idUnidade" required>
                            <?php
                            $stmt = $this->conn->prepare("SELECT id, nome, descricao FROM unidade");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($res = $result->fetch_assoc()) {
                                $unidade = $res['nome'] . ($res['descricao'] ? "(" . $res['descricao'] . ")" : ""); ?>
                                <option value="<?php echo $res['id']; ?>"><?php echo $unidade; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="idCultura" class="form-label">Cultura</label>
                        <select class="form-select" id="id_cultura" name="idCultura" required>
                            <?php
                            $stmt = $this->conn->prepare("SELECT id, nome FROM cultura WHERE id_propriedade = ?");
                            $stmt->bind_param("i", $idPropriedade);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($res = $result->fetch_assoc()) { ?>
                                <option value="<?php echo $res['id']; ?>"><?php echo $res['nome']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" name="adicionar" class="btn btn-success">Adicionar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
    }
}

$lancamento = new Lancamento($conn);
$lancamento->renderizar([$lancamento, 'conteudo']);
?>

