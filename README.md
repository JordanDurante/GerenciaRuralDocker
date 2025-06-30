# Infraestrutura Docker com Apache para Backend e Frontend
___

## Requisitos do trabalho
- Colocar o seu de POOW1 no GitHub. Pode ser também qualquer outro projeto passado de outra disciplina desde que tenha uma interface Web e Banco de Dados.
- O projeto em si não será objeto de avaliação, apenas o seu funcionamento no ambiente virtualizado.
- Escrever um Dockerfile para build de uma imagem para clonar o projeto do GitHub, compilar o projeto e instalar todos os módulos necessários para sua execução.
- Expor o serviço na porta 8080, bem como os logs do servidor em um volume que pode ser acessado no SO Host.
- Colocar o comando correto para inicialização do projeto no ENTRYPOINT.
- Criar um docker-compose.yml também para facilitar o processo de build-deploy da aplicação.
- Entregar os códigos, juntamente com um vídeo (ou link para o vídeo) demonstrando o funcionamento.

## Pré-requisitos

Para executar este projeto, certifique-se de que tem os seguintes softwares instalados na sua máquina:

* [**Docker**](https://www.docker.com/products/docker-desktop/)
* [**Docker Compose**](https://docs.docker.com/compose/install/) (geralmente já vem incluído no Docker Desktop)
* **Git**


##Como rodar o projeto com Docker

### Passo 1: Clonar este Repositório
```bash
git clone https://github.com/JordanDurante/GerenciaRuralDocker

```

## Passo 2: Abra o diretório
```bash
cd GerenciaRuralDocker
```

## Passo 3: Construa e Execute a Aplicação
```bash
docker compose up --build -d
```

## Banco de dados

### Passo 1: Copiar o 1.sql para dentro do container

```bash
docker cp ./www/1.sql gerenciaruraldocker-db-1:/tmp/1.sql
```

### Passo 2: - Entrar no container do banco
```bash
docker exec -it gerenciaruraldocker-db-1 bash
```

### Passo 3: - Acessar o MySQL dentro do container
```bash
mysql -u usuario -p gerenciarural
# senha: senha
```

### Passo 3: - Executar o script
```bash
source /tmp/1.sql;
```

## Acesso

Assim que terminar de buildar a execução, a aplicação estara dispónivel em:

http://localhost:8080


### Login de demonstração
- Usuário: admin
- Senha: 123



## Parar e remover os containers
```bash
docker compose down
```

## Logs do servidor
Os logs do Apache ficam disponíveis no diretório local `./logs`, montado como volume a partir de `/var/log/apache2` no container.
