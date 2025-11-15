-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 12/11/2025 às 13:18
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `Alucar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `CLIENTE`
--

CREATE TABLE `CLIENTE` (
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `FILIAL`
--

CREATE TABLE `FILIAL` (
  `id_filial` int(11) NOT NULL,
  `nome_filial` varchar(100) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `HABILITACAO`
--

CREATE TABLE `HABILITACAO` (
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
-- Estrutura para tabela `LOCACAO`
--

CREATE TABLE `LOCACAO` (
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `MODELO`
--

CREATE TABLE `MODELO` (
  `id_modelo` int(11) NOT NULL,
  `nome_modelo` varchar(100) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `preco_diaria_base` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `MOTORISTA_ADICIONAL`
--

CREATE TABLE `MOTORISTA_ADICIONAL` (
  `id_motorista_add` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `numero_habilitacao` varchar(20) NOT NULL,
  `habilitacao_valida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `PAGAMENTO`
--

CREATE TABLE `PAGAMENTO` (
  `id_pagamento` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `forma_pagamento` enum('Pix','Link de Pagamento','Cartão Débito','Cartão Crédito') NOT NULL,
  `data_pagamento` datetime NOT NULL,
  `status` enum('Aprovado','Pendente','Recusado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `PROMOCAO`
--

CREATE TABLE `PROMOCAO` (
  `id_promocao` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `percentual_desconto` decimal(5,2) DEFAULT NULL,
  `valor_fixo_desconto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `PROMOCAO_MODELO`
--

CREATE TABLE `PROMOCAO_MODELO` (
  `id_promocao` int(11) NOT NULL,
  `id_modelo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `REVISAO`
--

CREATE TABLE `REVISAO` (
  `id_revisao` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `data_agendamento` date DEFAULT NULL,
  `quilometragem_revisao` int(11) DEFAULT NULL,
  `descricao_servico` text DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `VEICULO`
--

CREATE TABLE `VEICULO` (
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `VISTORIA`
--

CREATE TABLE `VISTORIA` (
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
-- Índices de tabela `CLIENTE`
--
ALTER TABLE `CLIENTE`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `FILIAL`
--
ALTER TABLE `FILIAL`
  ADD PRIMARY KEY (`id_filial`);

--
-- Índices de tabela `HABILITACAO`
--
ALTER TABLE `HABILITACAO`
  ADD PRIMARY KEY (`id_habilitacao`),
  ADD UNIQUE KEY `id_cliente` (`id_cliente`),
  ADD UNIQUE KEY `numero_registro` (`numero_registro`);

--
-- Índices de tabela `LOCACAO`
--
ALTER TABLE `LOCACAO`
  ADD PRIMARY KEY (`id_locacao`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_veiculo` (`id_veiculo`),
  ADD KEY `id_filial_retirada` (`id_filial_retirada`),
  ADD KEY `id_filial_devolucao` (`id_filial_devolucao`);

--
-- Índices de tabela `MODELO`
--
ALTER TABLE `MODELO`
  ADD PRIMARY KEY (`id_modelo`);

--
-- Índices de tabela `MOTORISTA_ADICIONAL`
--
ALTER TABLE `MOTORISTA_ADICIONAL`
  ADD PRIMARY KEY (`id_motorista_add`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- Índices de tabela `PAGAMENTO`
--
ALTER TABLE `PAGAMENTO`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- Índices de tabela `PROMOCAO`
--
ALTER TABLE `PROMOCAO`
  ADD PRIMARY KEY (`id_promocao`);

--
-- Índices de tabela `PROMOCAO_MODELO`
--
ALTER TABLE `PROMOCAO_MODELO`
  ADD PRIMARY KEY (`id_promocao`,`id_modelo`),
  ADD KEY `id_modelo` (`id_modelo`);

--
-- Índices de tabela `REVISAO`
--
ALTER TABLE `REVISAO`
  ADD PRIMARY KEY (`id_revisao`),
  ADD KEY `id_veiculo` (`id_veiculo`);

--
-- Índices de tabela `VEICULO`
--
ALTER TABLE `VEICULO`
  ADD PRIMARY KEY (`id_veiculo`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `id_modelo` (`id_modelo`);

--
-- Índices de tabela `VISTORIA`
--
ALTER TABLE `VISTORIA`
  ADD PRIMARY KEY (`id_vistoria`),
  ADD KEY `id_locacao` (`id_locacao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `CLIENTE`
--
ALTER TABLE `CLIENTE`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `FILIAL`
--
ALTER TABLE `FILIAL`
  MODIFY `id_filial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `HABILITACAO`
--
ALTER TABLE `HABILITACAO`
  MODIFY `id_habilitacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `LOCACAO`
--
ALTER TABLE `LOCACAO`
  MODIFY `id_locacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `MODELO`
--
ALTER TABLE `MODELO`
  MODIFY `id_modelo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `MOTORISTA_ADICIONAL`
--
ALTER TABLE `MOTORISTA_ADICIONAL`
  MODIFY `id_motorista_add` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `PAGAMENTO`
--
ALTER TABLE `PAGAMENTO`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `PROMOCAO`
--
ALTER TABLE `PROMOCAO`
  MODIFY `id_promocao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `REVISAO`
--
ALTER TABLE `REVISAO`
  MODIFY `id_revisao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `VEICULO`
--
ALTER TABLE `VEICULO`
  MODIFY `id_veiculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `VISTORIA`
--
ALTER TABLE `VISTORIA`
  MODIFY `id_vistoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `HABILITACAO`
--
ALTER TABLE `HABILITACAO`
  ADD CONSTRAINT `HABILITACAO_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `CLIENTE` (`id_cliente`);

--
-- Restrições para tabelas `LOCACAO`
--
ALTER TABLE `LOCACAO`
  ADD CONSTRAINT `LOCACAO_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `CLIENTE` (`id_cliente`),
  ADD CONSTRAINT `LOCACAO_ibfk_2` FOREIGN KEY (`id_veiculo`) REFERENCES `VEICULO` (`id_veiculo`),
  ADD CONSTRAINT `LOCACAO_ibfk_3` FOREIGN KEY (`id_filial_retirada`) REFERENCES `FILIAL` (`id_filial`),
  ADD CONSTRAINT `LOCACAO_ibfk_4` FOREIGN KEY (`id_filial_devolucao`) REFERENCES `FILIAL` (`id_filial`);

--
-- Restrições para tabelas `MOTORISTA_ADICIONAL`
--
ALTER TABLE `MOTORISTA_ADICIONAL`
  ADD CONSTRAINT `MOTORISTA_ADICIONAL_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `LOCACAO` (`id_locacao`);

--
-- Restrições para tabelas `PAGAMENTO`
--
ALTER TABLE `PAGAMENTO`
  ADD CONSTRAINT `PAGAMENTO_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `LOCACAO` (`id_locacao`);

--
-- Restrições para tabelas `PROMOCAO_MODELO`
--
ALTER TABLE `PROMOCAO_MODELO`
  ADD CONSTRAINT `PROMOCAO_MODELO_ibfk_1` FOREIGN KEY (`id_promocao`) REFERENCES `PROMOCAO` (`id_promocao`),
  ADD CONSTRAINT `PROMOCAO_MODELO_ibfk_2` FOREIGN KEY (`id_modelo`) REFERENCES `MODELO` (`id_modelo`);

--
-- Restrições para tabelas `REVISAO`
--
ALTER TABLE `REVISAO`
  ADD CONSTRAINT `REVISAO_ibfk_1` FOREIGN KEY (`id_veiculo`) REFERENCES `VEICULO` (`id_veiculo`);

--
-- Restrições para tabelas `VEICULO`
--
ALTER TABLE `VEICULO`
  ADD CONSTRAINT `VEICULO_ibfk_1` FOREIGN KEY (`id_modelo`) REFERENCES `MODELO` (`id_modelo`);

--
-- Restrições para tabelas `VISTORIA`
--
ALTER TABLE `VISTORIA`
  ADD CONSTRAINT `VISTORIA_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `LOCACAO` (`id_locacao`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
