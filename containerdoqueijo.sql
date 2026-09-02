-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/09/2026 às 16:15
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
-- Banco de dados: `containerdoqueijo`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `idCliente` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cpf` char(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `idEndereco` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosclientes`
--

CREATE TABLE `enderecosclientes` (
  `idEnderecoCliente` int(11) NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cep` char(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `enderecosclientes`
--

INSERT INTO `enderecosclientes` (`idEnderecoCliente`, `rua`, `numero`, `bairro`, `cep`) VALUES
(0, 'Av.Gilberto Vergueiro da Silva', 45, 'Chararas palmeirinhas', '13737');

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosfornecedores`
--

CREATE TABLE `enderecosfornecedores` (
  `idEnderecoFornecedor` int(11) NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cep` char(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosfuncionarios`
--

CREATE TABLE `enderecosfuncionarios` (
  `idEnderecoFuncionrio` int(11) NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cep` char(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

CREATE TABLE `fornecedores` (
  `idFornecedor` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `telefone` int(11) DEFAULT NULL,
  `idEnderecoFornecedor` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `idFuncionario` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cargo` enum('chefe','funcionario') NOT NULL,
  `numero` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `idEnderecoFuncionario` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`idFuncionario`, `nome`, `cargo`, `numero`, `email`, `senha`, `idEnderecoFuncionario`) VALUES
(1, 'Rodrigo Jesus de Carvalho', 'funcionario', NULL, NULL, NULL, NULL),
(2, 'Rodrigo Jesus de Carvalho', 'funcionario', NULL, NULL, NULL, NULL),
(3, '1', 'chefe', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `idProduto` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `codigoDeBarras` int(11) DEFAULT NULL,
  `valor` float(10,2) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `Armazenamento` enum('Refrigerado','Normal') NOT NULL,
  `Categoria` enum('Frio','Defumado','Doce','Bebida','Queijo') NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `idFornecedor` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `nome`, `codigoDeBarras`, `valor`, `validade`, `estoque`, `Armazenamento`, `Categoria`, `imagem`, `idFornecedor`) VALUES
(4, 'Rodrigo                    ', 12345, 11.00, NULL, 1, 'Refrigerado', 'Queijo', 'imagens/6a95954b01698_Captura de tela 2026-08-14 145128.png', NULL),
(3, 'Queijo Fresco', 1234, 25.00, NULL, 5, 'Refrigerado', 'Queijo', 'imagens/6a959483799e1_Captura de tela 2026-08-31 114515.png', NULL),
(5, 'mariany cristina guerra', 123456, 1.00, NULL, 1, 'Refrigerado', 'Queijo', 'imagens/6a9597fe3e2c0_Captura de tela 2026-08-15 102514.png', NULL),
(6, '1', 1, 1.00, NULL, 1, 'Refrigerado', 'Bebida', '../imagens/6a95d8c562d7e_Captura de tela 2026-08-23 235251.png', NULL),
(7, '1', 1, 1.00, NULL, 1, 'Refrigerado', 'Bebida', '../imagens/6a95d9a2043b7_provoloneArtesanal.jpeg', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cpf` char(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `idFornecedor` int(11) DEFAULT NULL,
  `idFuncionario` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nome`, `cpf`, `email`, `senha`, `idFornecedor`, `idFuncionario`) VALUES
(1, 'Rodrigo', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$VgiEz..hrP9eJGhHrVdbQ..bjYK/F7tuBR9ndy4F6/NOapSiP8eWy', NULL, NULL),
(2, 'Rodrigo', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$wV95HDGlgL720gsUxGYrPOK8.jY/4g99ceCewYq1OBTkfUDD2gSz.', NULL, NULL),
(3, 'fellipy', '540.689.518-45', 'fellipysilva986@gmail.com', '$2y$10$HBGpE83aucpb39yvNykqCu5IEHVKn3japM8Q9ycZ9J84dQ.2v6J7i', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendaprodutos`
--

CREATE TABLE `vendaprodutos` (
  `idVendaProdutos` int(11) NOT NULL,
  `quantidade` float(10,2) DEFAULT NULL,
  `idVenda` int(11) DEFAULT NULL,
  `idProduto` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `idVendas` int(11) NOT NULL,
  `valor` float(10,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `formaDePagamento` enum('Dinheiro','Cartao','Pix','Crediario') DEFAULT NULL,
  `descponto` float(10,2) DEFAULT NULL,
  `acrecimo` float(10,2) DEFAULT NULL,
  `idProduto` int(11) DEFAULT NULL,
  `idCliente` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idCliente`),
  ADD KEY `idEndereco` (`idEndereco`);

--
-- Índices de tabela `enderecosclientes`
--
ALTER TABLE `enderecosclientes`
  ADD PRIMARY KEY (`idEnderecoCliente`);

--
-- Índices de tabela `enderecosfornecedores`
--
ALTER TABLE `enderecosfornecedores`
  ADD PRIMARY KEY (`idEnderecoFornecedor`);

--
-- Índices de tabela `enderecosfuncionarios`
--
ALTER TABLE `enderecosfuncionarios`
  ADD PRIMARY KEY (`idEnderecoFuncionrio`);

--
-- Índices de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`idFornecedor`),
  ADD KEY `idEnderecoFornecedor` (`idEnderecoFornecedor`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`idFuncionario`),
  ADD KEY `idEnderecoFuncionario` (`idEnderecoFuncionario`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`idProduto`),
  ADD KEY `idFornecedor` (`idFornecedor`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD KEY `idFornecedor` (`idFornecedor`),
  ADD KEY `idFuncionario` (`idFuncionario`);

--
-- Índices de tabela `vendaprodutos`
--
ALTER TABLE `vendaprodutos`
  ADD PRIMARY KEY (`idVendaProdutos`),
  ADD KEY `idVenda` (`idVenda`),
  ADD KEY `idProduto` (`idProduto`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`idVendas`),
  ADD KEY `idProduto` (`idProduto`),
  ADD KEY `idCliente` (`idCliente`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `enderecosfornecedores`
--
ALTER TABLE `enderecosfornecedores`
  MODIFY `idEnderecoFornecedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecosfuncionarios`
--
ALTER TABLE `enderecosfuncionarios`
  MODIFY `idEnderecoFuncionrio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `idFornecedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `idFuncionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `idProduto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `vendaprodutos`
--
ALTER TABLE `vendaprodutos`
  MODIFY `idVendaProdutos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `idVendas` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
