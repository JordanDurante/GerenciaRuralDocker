CREATE TABLE propriedade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE cultura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE propriedade_cultura (
    id_propriedade INT,
    id_cultura INT,
    PRIMARY KEY (id_propriedade, id_cultura),
    FOREIGN KEY (id_propriedade) REFERENCES propriedade(id),
    FOREIGN KEY (id_cultura) REFERENCES cultura(id)
);

CREATE TABLE lancamento_financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('entrada', 'saida') NOT NULL,
    data DATE NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    quantidade DECIMAL(10, 2) NOT NULL,
    unidade VARCHAR(50) NOT NULL,
    id_cultura INT,
    id_propriedade INT,
    FOREIGN KEY (id_cultura) REFERENCES cultura(id),
    FOREIGN KEY (id_propriedade) REFERENCES propriedade(id)
);

CREATE TABLE login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    login VARCHAR(50) NOT NULL,
    senha VARCHAR(50) NOT NULL,
    id_propriedade INT,
    FOREIGN KEY (id_propriedade) REFERENCES propriedade(id)
);


INSERT INTO propriedade (id, nome) VALUES (1, 'Teste');

INSERT INTO login (id, nome, login, senha, id_propriedade) VALUES (1, 'Jordan', 'jordan', '202cb962ac59075b964b07152d234b70', 1);

INSERT INTO cultura (id, nome) VALUES (1, 'Fumo');

INSERT INTO propriedade_cultura (id_propriedade, id_cultura) VALUES (1, 1);

INSERT INTO lancamento_financeiro (id, tipo, data, valor, quantidade, unidade, id_cultura, id_propriedade) VALUES (1, 'entrada', '2025-06-30', 50.00, 10.00, 'KG', 1, 1);
