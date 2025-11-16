-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/11/2025 às 20:12
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

INSERT INTO `admin` (`id_admin`, `nome`, `email`, `senha_hash`, `rg`, `data_nascimento`, `genero`, `telefone`, `endereco`, `data_admissao`, `turno`, `carteira_trabalho`, `banco`, `agencia_conta`) VALUES
(1, 'Admin Master', 'admin@alucar.com', '$2y$10$iFHGRM7i2foMbDit1II18u3W4Lvp/SozG0yM5j1lTzMrkyRzjhFmy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'jefferson', 'jeffersoncorreia425@gmail.com', '$2y$10$3g4Tdp5EZdnPtK4ZWCyIJuK//s.jIoIeTk5dgBtuPktn/Cob.oBte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
(2, 'Souza', '203.979.057-85', 'jeffersoncorreia425@gmail.com', '$2y$10$5G0G9speGg1biUxd2tlHMuIutEVRweOTknPdF/ed7HembHsiixrRK', '2127722233', '', '2025-11-16', 0.0);

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
(59, 2, 2, 1, NULL, '2025-11-16 14:54:39', '2025-12-16 14:54:39', '2025-11-16 18:55:15', 'Devolvido', 63315.00, NULL, 0.00),
(60, 2, 1, 1, NULL, '2025-11-16 17:23:46', '2025-12-01 17:23:46', '2025-11-16 19:04:17', 'Devolvido', 327.75, NULL, 0.00),
(61, 2, 1, 1, NULL, '2025-11-16 19:35:46', '2025-12-16 19:35:46', '2025-11-16 19:39:57', 'Devolvido', 621.00, NULL, 0.00),
(62, 2, 2, 1, NULL, '2025-11-16 19:47:11', '2025-11-23 19:47:11', NULL, 'Reservado', 16415.00, NULL, 0.00);

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
(1, '123', 'creta', 'Esportivo', 23.00, 'civicTypeR.png'),
(2, '123', 'creta', 'Popular', 2345.00, 'corolla.png');

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
(1, 59, '[\"Carro com defeito\",\"Veículo furtado\"]', '', '2025-11-16 16:38:50', 'Em Análise'),
(2, 59, '[\"Problema de pagamento\"]', '', '2025-11-16 16:40:32', 'Em Análise'),
(3, 59, '[\"Carro com defeito\",\"Veículo furtado\"]', '', '2025-11-16 17:20:36', 'Em Análise'),
(4, 61, '[\"Veículo furtado\"]', 'bandido na manguerinha pegou ali', '2025-11-16 19:36:52', 'Em Análise');

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
  `gps_rastreamento` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculo`
--

INSERT INTO `veiculo` (`id_veiculo`, `id_modelo`, `placa`, `ano`, `cor`, `tipo_transmissao`, `capacidade_pessoas`, `quilometragem_atual`, `disponivel`, `acessivel`, `gps_rastreamento`) VALUES
(1, 1, 'XRL9', 2012, 'preta', 'Manual', 5, 1, 1, 0, NULL),
(2, 2, 'VVVV', 2021, 'azul', 'Manual', 5, 123, 1, 0, NULL);

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
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

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
-- Índices de tabela `motorista_adicional`
--
ALTER TABLE `motorista_adicional`
  ADD PRIMARY KEY (`id_motorista_add`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- Índices de tabela `ocorrencia`
--
ALTER TABLE `ocorrencia`
  ADD PRIMARY KEY (`id_ocorrencia`),
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
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_locacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de tabela `modelo`
--
ALTER TABLE `modelo`
  MODIFY `id_modelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `motorista_adicional`
--
ALTER TABLE `motorista_adicional`
  MODIFY `id_motorista_add` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ocorrencia`
--
ALTER TABLE `ocorrencia`
  MODIFY `id_ocorrencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id_veiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `vistoria`
--
ALTER TABLE `vistoria`
  MODIFY `id_vistoria` int(11) NOT NULL AUTO_INCREMENT;

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
-- Restrições para tabelas `motorista_adicional`
--
ALTER TABLE `motorista_adicional`
  ADD CONSTRAINT `MOTORISTA_ADICIONAL_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `locacao` (`id_locacao`);

--
-- Restrições para tabelas `ocorrencia`
--
ALTER TABLE `ocorrencia`
  ADD CONSTRAINT `OCORRENCIA_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `locacao` (`id_locacao`);

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
