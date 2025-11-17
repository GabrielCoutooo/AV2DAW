-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/11/2025 às 05:47
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `alucar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL DEFAULT '',
  `rg` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `carteira_trabalho` varchar(50) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `agencia_conta` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`id_admin`, `nome`, `email`, `senha_hash`, `cpf`, `rg`, `data_nascimento`, `genero`, `telefone`, `endereco`, `data_admissao`, `turno`, `carteira_trabalho`, `banco`, `agencia_conta`) VALUES
(1, 'Gabriel Couto', 'gabriel@alucar.com', '$2y$10$JdEoUNw1FBhnw6.wukmYyO/R0TxPWD4G.ThCW.9BRbkJcSVqWmiS.', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Jefferson Souza', 'jeff@alugar.com', '$2y$10$uV.sNxRck1Lw28ojM1AAxeMY5rOub/n77oB3dPc3.0L9f3YwUmi3.', '15763769740', '45.172.674-1', '2003-03-22', 'M', '2193453245', 'Rua Miguel de Frias 22', '2025-11-17', 'Manha', '123123', '123', '123412');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome`, `descricao`) VALUES
(1, 'Popular', 'Veículos mais procurados'),
(2, 'Recomendado', 'Veículos recomendados pela equipe'),
(3, 'SUV', 'Veículos utilitários esportivos'),
(4, 'Esportivo', 'Veículos esportivos de alta performance'),
(5, 'Executivo', 'Veículos para executivos'),
(6, 'Econômico', 'Veículos com baixo consumo'),
(7, 'Luxo', 'Veículos de luxo premium'),
(12, 'Sedan', 'Carro sedan (3 volumes)'),
(13, 'Hatch Médio', 'Hatchback médio'),
(14, 'Hatch Compacto', 'Hatchback compacto'),
(15, 'Compacto', 'Veículo compacto'),
(16, 'Picape Compacta', 'Picape de porte compacto');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `data_cadastro` date DEFAULT NULL,
  `avaliacao_media` decimal(2,1) DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `cpf`, `email`, `senha_hash`, `telefone`, `endereco`, `data_cadastro`, `avaliacao_media`) VALUES
(1, 'Gabriel Couto', '161.691.267-79', 'gabrielccsilva@gmail.com', '$2y$10$v84JBPqkdlo.hOY.NWeVv.IKHaS7RZjfbTuVMG8l94IuCJ2cPrUVu', '21988339569', '', '2025-11-13', 0.0),
(2, 'Jefferson Souza', '161.691.255-65', 'jeffcoxinhas@gmail.com', '$2y$10$GQf3wNwzSr47P9np/kCDiuNOuGHJ5Mskg9Ro9skWqYzEMZlOd6O/a', '2198345456', '', '2025-11-13', 0.0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `filial`
--

CREATE TABLE `filial` (
  `id_filial` int(11) NOT NULL,
  `nome_filial` varchar(100) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `filial`
--

INSERT INTO `filial` (`id_filial`, `nome_filial`, `endereco`, `telefone`, `email`) VALUES
(1, 'Matriz Rio de Janeiro', 'Rua Exemplo, 123 - Centro', '(21) 9999-0000', 'contato@alucar.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `habilitacao`
--

CREATE TABLE `habilitacao` (
  `id_habilitacao` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `numero_registro` varchar(20) NOT NULL,
  `data_emissao` date DEFAULT NULL,
  `data_validade` date NOT NULL,
  `categoria` varchar(5) NOT NULL,
  `imagem_cnh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `locacao`
--

CREATE TABLE `locacao` (
  `id_locacao` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `id_filial_retirada` int(11) NOT NULL,
  `id_filial_devolucao` int(11) DEFAULT NULL,
  `data_hora_retirada` datetime NOT NULL,
  `data_hora_prevista_devolucao` datetime NOT NULL,
  `data_hora_real_devolucao` datetime DEFAULT NULL,
  `status` enum('Reservado','Retirado','Devolvido','Cancelado') NOT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `quilometragem_maxima` int(11) DEFAULT NULL,
  `multa_excesso_dias` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `locacao`
--

INSERT INTO `locacao` (`id_locacao`, `id_cliente`, `id_veiculo`, `id_filial_retirada`, `id_filial_devolucao`, `data_hora_retirada`, `data_hora_prevista_devolucao`, `data_hora_real_devolucao`, `status`, `valor_total`, `quilometragem_maxima`, `multa_excesso_dias`) VALUES
(1, 1, 24, 1, NULL, '2025-11-16 21:00:48', '2025-11-17 21:00:48', NULL, '', 401.92, NULL, 0.00),
(2, 1, 19, 1, NULL, '2025-11-16 22:00:33', '2025-11-17 22:00:33', NULL, 'Retirado', 183.72, NULL, 0.00),
(3, 1, 22, 1, NULL, '2025-11-16 23:15:47', '2025-11-19 23:15:47', '2025-11-16 23:17:57', '', 740.13, NULL, 0.00),
(4, 1, 21, 1, NULL, '2025-11-16 23:22:10', '2025-11-19 23:22:10', '2025-11-16 23:27:07', 'Devolvido', 281.91, NULL, 0.00),
(5, 1, 15, 1, NULL, '2025-11-17 00:15:08', '2025-11-18 00:15:08', NULL, 'Retirado', 331.01, NULL, 0.00),
(6, 1, 7, 1, NULL, '2025-11-17 00:45:51', '2025-11-24 00:45:51', NULL, 'Reservado', NULL, NULL, 0.00),
(7, 1, 20, 1, NULL, '2025-11-17 00:52:16', '2025-11-27 00:52:16', '2025-11-17 04:53:10', 'Devolvido', 1359.20, NULL, 0.00),
(8, 1, 7, 1, NULL, '2025-11-17 01:19:44', '2025-11-24 01:19:44', NULL, 'Reservado', 1195.55, NULL, 0.00);

--
-- Acionadores `locacao`
--
DELIMITER $$
CREATE TRIGGER `after_locacao_insert` AFTER INSERT ON `locacao` FOR EACH ROW BEGIN
    IF NEW.status = 'Retirado' THEN
        UPDATE veiculo SET status_veiculo = 'Alugado' WHERE id_veiculo = NEW.id_veiculo;
    ELSEIF NEW.status = 'Reservado' THEN
        UPDATE veiculo SET status_veiculo = 'Disponível' WHERE id_veiculo = NEW.id_veiculo;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_locacao_update` AFTER UPDATE ON `locacao` FOR EACH ROW BEGIN
    IF NEW.status = 'Retirado' THEN
        UPDATE veiculo SET status_veiculo = 'Alugado' WHERE id_veiculo = NEW.id_veiculo;
    ELSEIF NEW.status = 'Devolvido' OR NEW.status = 'Cancelado' THEN
        -- Verificar se não há outras locações ativas para este veículo
        IF (SELECT COUNT(*) FROM locacao WHERE id_veiculo = NEW.id_veiculo AND status = 'Retirado' AND id_locacao != NEW.id_locacao) = 0 THEN
            UPDATE veiculo SET status_veiculo = 'Disponível' WHERE id_veiculo = NEW.id_veiculo;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelo`
--

CREATE TABLE `modelo` (
  `id_modelo` int(11) NOT NULL,
  `nome_modelo` varchar(100) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `preco_diaria_base` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modelo`
--

INSERT INTO `modelo` (`id_modelo`, `nome_modelo`, `marca`, `categoria`, `preco_diaria_base`, `imagem`) VALUES
(1, 'Civic Type R', 'Honda', 'Esportivo', 100.00, 'carro69192a8d1d23a.png'),
(5, 'Civic Type R', 'Honda', 'Esportivo', 100.00, NULL),
(6, 'Argo', 'Fiat', 'Sedan', 150.00, 'argo.png'),
(7, 'Argo', 'Fiat', 'Sedan', 150.00, 'argo.png'),
(8, 'Argo', 'Fiat', 'Sedan', 150.00, NULL),
(9, 'Argo', 'Fiat', 'Sedan', 150.00, NULL),
(10, 'Corolla', 'Toyota', 'Sedan', 185.00, 'corolla.png'),
(11, 'Corolla', 'Toyota', 'Sedan', 185.00, 'corolla.png'),
(12, 'Corolla', 'Toyota', 'Sedan', 185.00, 'corolla.png'),
(13, 'Corolla', 'Toyota', 'Sedan', 185.00, 'corolla.png'),
(14, 'Corolla', 'Toyota', 'Sedan', 185.00, 'corolla.png'),
(15, 'Corolla', 'Toyota', 'Sedan', 185.00, 'corolla.png'),
(16, 'Civic Type R', 'Honda', 'Esportivo', 285.00, 'civicTypeR.png'),
(17, 'Corolla', 'Toyota', 'Sedan', 150.00, 'corolla.png'),
(18, 'Corolla', 'Toyota', 'Sedan', 150.00, 'corolla.png'),
(19, 'Corolla', 'Toyota', 'Sedan', 150.00, 'corolla.png'),
(20, 'Corolla', 'Toyota', 'Sedan', 150.00, 'corolla.png'),
(21, 'Creta', 'Hyndai', 'SUV', 120.00, 'creta.png'),
(22, 'Fusca', 'Volkswagen', 'Sedan', 80.00, 'fusca.png'),
(23, 'Gol', 'Volkswagen', 'SUV', 220.00, 'gol.png'),
(24, 'HB 20', 'Hyndai', 'Sedan', 220.00, 'hb20.png'),
(25, 'Kicks', 'Nissan', 'SUV', 350.00, 'kicks.png'),
(26, 'Novo Fusca', 'Volkswagen', 'Hatch Médio', 200.00, 'novofusca.jpg'),
(27, 'Onix', 'Chevrolet', 'Compacto', 240.00, 'onix.png'),
(28, 'Polo', 'Volkswagen', 'Hatch Compacto', 190.00, 'polo.png'),
(29, 'Saveiro', 'Volkswagen', 'Picape Compacta', 215.00, 'saveiro.png'),
(30, 'Strada', 'Fiat', 'Picape Compacta', 215.00, 'strada.png'),
(31, '911 GT 2', 'Porche', 'Esportivo', 1000.00, 'porche911gt2.png'),
(32, '718 Cayman GT4 RS', 'Porche', 'Esportivo', 850.00, 'Porsche718CaymanGt4Rs.png'),
(33, 'Palio', 'Fiat', 'Compacto', 100.00, 'palio.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelo_categoria`
--

CREATE TABLE `modelo_categoria` (
  `id_modelo` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modelo_categoria`
--

INSERT INTO `modelo_categoria` (`id_modelo`, `id_categoria`) VALUES
(1, 2),
(1, 4),
(5, 2),
(5, 4),
(6, 1),
(6, 12),
(7, 1),
(7, 12),
(8, 1),
(8, 12),
(9, 1),
(9, 12),
(10, 2),
(10, 12),
(11, 2),
(11, 12),
(12, 2),
(12, 12),
(13, 2),
(13, 12),
(14, 2),
(14, 12),
(15, 2),
(15, 12),
(16, 2),
(16, 4),
(17, 2),
(17, 12),
(18, 2),
(18, 12),
(19, 2),
(19, 12),
(20, 2),
(20, 12),
(21, 2),
(21, 3),
(22, 1),
(22, 12),
(23, 1),
(23, 3),
(23, 12),
(24, 1),
(24, 12),
(25, 2),
(25, 3),
(26, 2),
(26, 13),
(27, 1),
(27, 2),
(27, 15),
(28, 1),
(28, 2),
(28, 14),
(29, 2),
(29, 16),
(30, 2),
(30, 16),
(31, 4),
(32, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `motorista_adicional`
--

CREATE TABLE `motorista_adicional` (
  `id_motorista_add` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `numero_habilitacao` varchar(20) NOT NULL,
  `habilitacao_valida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ocorrencia`
--

CREATE TABLE `ocorrencia` (
  `id_ocorrencia` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `tipos_selecionados` text DEFAULT NULL COMMENT 'Ex: Carro com defeito, Problema de pagamento',
  `detalhes_adicionais` text DEFAULT NULL,
  `data_registro` datetime NOT NULL,
  `status_ocorrencia` enum('Em Análise','Resolvida','Rejeitada') NOT NULL DEFAULT 'Em Análise'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ocorrencia`
--

INSERT INTO `ocorrencia` (`id_ocorrencia`, `id_locacao`, `tipos_selecionados`, `detalhes_adicionais`, `data_registro`, `status_ocorrencia`) VALUES
(0, 1, '[\"Veículo furtado\"]', 'Dois lerdao passou armado e roubou o carro de moto', '2025-11-16 21:19:41', 'Em Análise'),
(0, 3, '[\"Carro com defeito\"]', 'Carro esta sem stepestepe e o pneu do carro furou', '2025-11-16 23:25:57', 'Em Análise');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `forma_pagamento` enum('Pix','Link de Pagamento','Cartão Débito','Cartão Crédito') NOT NULL,
  `data_pagamento` datetime NOT NULL,
  `status` enum('Aprovado','Pendente','Recusado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `promocao`
--

CREATE TABLE `promocao` (
  `id_promocao` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `percentual_desconto` decimal(5,2) DEFAULT NULL,
  `valor_fixo_desconto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `promocao_modelo`
--

CREATE TABLE `promocao_modelo` (
  `id_promocao` int(11) NOT NULL,
  `id_modelo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `revisao`
--

CREATE TABLE `revisao` (
  `id_revisao` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `data_agendamento` date DEFAULT NULL,
  `quilometragem_revisao` int(11) DEFAULT NULL,
  `descricao_servico` text DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculo`
--

CREATE TABLE `veiculo` (
  `id_veiculo` int(11) NOT NULL,
  `id_modelo` int(11) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `ano` int(11) DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `tipo_transmissao` enum('Manual','Automática') NOT NULL,
  `capacidade_pessoas` int(11) DEFAULT NULL,
  `quilometragem_atual` int(11) DEFAULT 0,
  `disponivel` tinyint(1) DEFAULT 1,
  `acessivel` tinyint(1) DEFAULT 0,
  `gps_rastreamento` varchar(255) DEFAULT NULL,
  `status_veiculo` enum('Disponível','Alugado','Manutenção','Indisponível') DEFAULT 'Disponível'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculo`
--

INSERT INTO `veiculo` (`id_veiculo`, `id_modelo`, `placa`, `ano`, `cor`, `tipo_transmissao`, `capacidade_pessoas`, `quilometragem_atual`, `disponivel`, `acessivel`, `gps_rastreamento`, `status_veiculo`) VALUES
(7, 7, 'YKO2S94', 2025, 'Prata', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(15, 16, '0', 2024, 'Azul', 'Manual', 5, 0, 1, 0, NULL, 'Alugado'),
(19, 20, 'ACP4V44', 2023, 'Branco', 'Manual', 5, 0, 1, 0, NULL, 'Alugado'),
(20, 21, 'LDR8U99', 2018, 'Prata', 'Manual', 5, 20, 1, 0, NULL, 'Disponível'),
(21, 22, 'EMH1I32', 1940, 'Verde', 'Manual', 5, 20, 1, 0, NULL, 'Disponível'),
(22, 23, 'MFQ4Z03', 2024, 'Prata', 'Manual', 5, 1, 0, 0, NULL, 'Alugado'),
(23, 24, 'NYM8W74', 2018, 'Prata', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(24, 25, 'QUM5G52', 2020, 'Cinza Escuro', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(25, 26, 'QXL6I69', 2017, 'Branco', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(26, 27, 'RJIF10', 2019, 'Branco', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(27, 28, 'PQO2S28', 2025, 'Prata', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(28, 29, 'HJTF29', 2020, 'Azul', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(29, 30, 'NAL-354', 2015, 'Prata', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(30, 31, 'CTN-420', 2025, 'Cinza Escuro', 'Manual', 5, 0, 1, 0, NULL, 'Disponível'),
(31, 32, 'NRK-024', 2025, 'Laranja', 'Manual', 5, 0, 0, 0, NULL, 'Manutenção'),
(32, 33, 'KUV6962', 2008, 'Prata', 'Manual', 5, 0, 0, 0, NULL, 'Manutenção');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vistoria`
--

CREATE TABLE `vistoria` (
  `id_vistoria` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `tipo_vistoria` enum('Retirada','Devolução') NOT NULL,
  `data_hora` datetime NOT NULL,
  `nivel_combustivel` enum('Cheio','Abaixo') NOT NULL,
  `avarias_registradas` text DEFAULT NULL,
  `multas_registradas` text DEFAULT NULL,
  `acessorios_confirmados` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vistoria`
--

INSERT INTO `vistoria` (`id_vistoria`, `id_locacao`, `tipo_vistoria`, `data_hora`, `nivel_combustivel`, `avarias_registradas`, `multas_registradas`, `acessorios_confirmados`) VALUES
(1, 3, 'Devolução', '2025-11-16 23:17:57', 'Cheio', NULL, NULL, 1),
(2, 4, 'Devolução', '2025-11-16 23:24:26', 'Cheio', NULL, NULL, 1),
(3, 4, 'Devolução', '2025-11-16 23:27:07', 'Cheio', 'Avarias/Obs:  | Itens NÃO OK: ', NULL, 1),
(4, 7, 'Devolução', '2025-11-17 04:53:10', 'Cheio', NULL, NULL, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `filial`
--
ALTER TABLE `filial`
  ADD PRIMARY KEY (`id_filial`);

--
-- Índices de tabela `habilitacao`
--
ALTER TABLE `habilitacao`
  ADD PRIMARY KEY (`id_habilitacao`),
  ADD UNIQUE KEY `id_cliente` (`id_cliente`),
  ADD UNIQUE KEY `numero_registro` (`numero_registro`);

--
-- Índices de tabela `locacao`
--
ALTER TABLE `locacao`
  ADD PRIMARY KEY (`id_locacao`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_veiculo` (`id_veiculo`),
  ADD KEY `id_filial_retirada` (`id_filial_retirada`),
  ADD KEY `id_filial_devolucao` (`id_filial_devolucao`);

--
-- Índices de tabela `modelo`
--
ALTER TABLE `modelo`
  ADD PRIMARY KEY (`id_modelo`);

--
-- Índices de tabela `modelo_categoria`
--
ALTER TABLE `modelo_categoria`
  ADD PRIMARY KEY (`id_modelo`,`id_categoria`),
  ADD KEY `fk_categoria` (`id_categoria`);

--
-- Índices de tabela `motorista_adicional`
--
ALTER TABLE `motorista_adicional`
  ADD PRIMARY KEY (`id_motorista_add`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- Índices de tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- Índices de tabela `promocao`
--
ALTER TABLE `promocao`
  ADD PRIMARY KEY (`id_promocao`);

--
-- Índices de tabela `promocao_modelo`
--
ALTER TABLE `promocao_modelo`
  ADD PRIMARY KEY (`id_promocao`,`id_modelo`),
  ADD KEY `id_modelo` (`id_modelo`);

--
-- Índices de tabela `revisao`
--
ALTER TABLE `revisao`
  ADD PRIMARY KEY (`id_revisao`),
  ADD KEY `id_veiculo` (`id_veiculo`);

--
-- Índices de tabela `veiculo`
--
ALTER TABLE `veiculo`
  ADD PRIMARY KEY (`id_veiculo`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `id_modelo` (`id_modelo`);

--
-- Índices de tabela `vistoria`
--
ALTER TABLE `vistoria`
  ADD PRIMARY KEY (`id_vistoria`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `filial`
--
ALTER TABLE `filial`
  MODIFY `id_filial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `habilitacao`
--
ALTER TABLE `habilitacao`
  MODIFY `id_habilitacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `locacao`
--
ALTER TABLE `locacao`
  MODIFY `id_locacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `modelo`
--
ALTER TABLE `modelo`
  MODIFY `id_modelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `motorista_adicional`
--
ALTER TABLE `motorista_adicional`
  MODIFY `id_motorista_add` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `promocao`
--
ALTER TABLE `promocao`
  MODIFY `id_promocao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `revisao`
--
ALTER TABLE `revisao`
  MODIFY `id_revisao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `veiculo`
--
ALTER TABLE `veiculo`
  MODIFY `id_veiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `vistoria`
--
ALTER TABLE `vistoria`
  MODIFY `id_vistoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `habilitacao`
--
ALTER TABLE `habilitacao`
  ADD CONSTRAINT `HABILITACAO_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);

--
-- Restrições para tabelas `locacao`
--
ALTER TABLE `locacao`
  ADD CONSTRAINT `LOCACAO_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `LOCACAO_ibfk_2` FOREIGN KEY (`id_veiculo`) REFERENCES `veiculo` (`id_veiculo`),
  ADD CONSTRAINT `LOCACAO_ibfk_3` FOREIGN KEY (`id_filial_retirada`) REFERENCES `filial` (`id_filial`),
  ADD CONSTRAINT `LOCACAO_ibfk_4` FOREIGN KEY (`id_filial_devolucao`) REFERENCES `filial` (`id_filial`);

--
-- Restrições para tabelas `modelo_categoria`
--
ALTER TABLE `modelo_categoria`
  ADD CONSTRAINT `fk_modelo_categoria_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_modelo_categoria_modelo` FOREIGN KEY (`id_modelo`) REFERENCES `modelo` (`id_modelo`) ON DELETE CASCADE;

--
-- Restrições para tabelas `motorista_adicional`
--
ALTER TABLE `motorista_adicional`
  ADD CONSTRAINT `MOTORISTA_ADICIONAL_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `locacao` (`id_locacao`);

--
-- Restrições para tabelas `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `PAGAMENTO_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `locacao` (`id_locacao`);

--
-- Restrições para tabelas `promocao_modelo`
--
ALTER TABLE `promocao_modelo`
  ADD CONSTRAINT `PROMOCAO_MODELO_ibfk_1` FOREIGN KEY (`id_promocao`) REFERENCES `promocao` (`id_promocao`),
  ADD CONSTRAINT `PROMOCAO_MODELO_ibfk_2` FOREIGN KEY (`id_modelo`) REFERENCES `modelo` (`id_modelo`);

--
-- Restrições para tabelas `revisao`
--
ALTER TABLE `revisao`
  ADD CONSTRAINT `REVISAO_ibfk_1` FOREIGN KEY (`id_veiculo`) REFERENCES `veiculo` (`id_veiculo`);

--
-- Restrições para tabelas `veiculo`
--
ALTER TABLE `veiculo`
  ADD CONSTRAINT `VEICULO_ibfk_1` FOREIGN KEY (`id_modelo`) REFERENCES `modelo` (`id_modelo`);

--
-- Restrições para tabelas `vistoria`
--
ALTER TABLE `vistoria`
  ADD CONSTRAINT `VISTORIA_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `locacao` (`id_locacao`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
