<?php
require_once '../../uteis/TelaPadrao.php';
require_once '../../../config/bd_connection.php';
error_reporting(1);

class Login extends TelaPadrao {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function validarLogin($login, $senha) {
        $stmt = $this->conn->prepare("SELECT nome, login, id_propriedade FROM login WHERE login = ? AND senha = ?");
        $senha = md5($senha);
        $stmt->bind_param("ss", $login, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['id_propriedade'] = $user['id_propriedade'];
            return true;
        } else {
            return false;
        }
    }

    public function mostrarConteudo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $login = $_POST['login'];
            $senha = $_POST['senha'];

            if ($this->validarLogin($login, $senha)) {
                header("Location: ../../public/classes/telaInicial/TelaInicial.php");
                exit;
            } else {
                echo "<div class='alert alert-danger' role='alert'>Login ou senha inválidos.</div>";
            }
        }
?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Login</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login</label>
                        <input type="text" class="form-control" id="login" name="login" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </form>
            </div>
        </div>
<?php
    }
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'];
            $senha = $_POST['senha'];

            if ($this->validarLogin($login, $senha)) {
                // Redireciona antes de qualquer saída
                header("Location: /public/classes/telaInicial/TelaInicial.php");
                exit;
            }
        }
    }

}

session_start();
$tela = new Login($conn);
$tela->autenticar(); 
$tela->renderizar([$tela, 'mostrarConteudo']);
?>
