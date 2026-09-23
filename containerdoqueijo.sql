-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/09/2026 às 16:12
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
(3, '1', 'chefe', NULL, NULL, NULL, NULL),
(4, 'Rodrigo', 'funcionario', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

CREATE TABLE `itens_venda` (
  `idItem` int(11) NOT NULL,
  `idVenda` int(11) NOT NULL,
  `idProduto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valorUnitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_venda`
--

INSERT INTO `itens_venda` (`idItem`, `idVenda`, `idProduto`, `quantidade`, `valorUnitario`) VALUES
(1, 3, 27, 1, 79.99),
(2, 4, 24, 1, 39.99),
(3, 5, 24, 1, 39.99),
(4, 6, 23, 1, 99.90),
(5, 7, 27, 1, 79.99),
(6, 8, 23, 1, 99.90);

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
  `categoria` enum('frios','defumados','doces','bebidas','queijos') NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `idFornecedor` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `nome`, `codigoDeBarras`, `valor`, `validade`, `estoque`, `Armazenamento`, `categoria`, `imagem`, `idFornecedor`) VALUES
(26, 'Queijo Fresco 500g ', 8, 24.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa8618563462_21c80348-2feb-4477-aaba-2020e17f1a6e.jpeg', NULL),
(25, 'Requeijão de Colher ', 7, 19.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa86165dfb53_1e2aa7ce-3b63-447c-838b-890176620dc8.jpeg', NULL),
(19, 'Queijo Recheado com requeijão ', 1, 29.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa86089a4e18_c875ebfe-7a67-4c5a-b9af-ba9724cc13ac.jpeg', NULL),
(20, 'Requeijão Scala 400g', 2, 19.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa860aa8a1e0_2aea2284-f65e-4234-95c2-f7e6cdc6aa57.jpeg', NULL),
(21, 'Kit 4 Queijos', 3, 34.99, NULL, 133, 'Refrigerado', 'queijos', '../imagens/6aa860c51dc32_2a460709-432c-458c-8db3-e39c3b199bd7.jpeg', NULL),
(22, 'Salaminho Fatiado ', 4, 8.99, NULL, 10, 'Refrigerado', 'defumados', '../imagens/6aa860e7d9eed_711f8246-79bc-4507-9b3d-3757d979f47a.jpeg', NULL),
(23, 'Queijo Azul ', 5, 99.90, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa86105d65e8_8c865777-613a-4063-957d-5b1bba7415f3.jpeg', NULL),
(24, 'Queijo Fresco ', 6, 39.99, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa86133900f8_274255c8-8aa9-4bea-b398-6b48e2efd482.jpeg', NULL),
(27, 'Queijo parmesão ', 9, 79.99, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa8619fb1b51_25b9a594-75b1-4127-9884-39a8184e915c.jpeg', NULL),
(28, 'Queijo Minas Padrão ', 10, 39.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa861bf729b9_f94eb560-ac1b-4d51-903c-5d41147abb3f.jpeg', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `cpf` char(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `idFornecedor` int(11) DEFAULT NULL,
  `idFuncionario` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nome`, `foto`, `cpf`, `email`, `senha`, `idFornecedor`, `idFuncionario`) VALUES
(1, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$VgiEz..hrP9eJGhHrVdbQ..bjYK/F7tuBR9ndy4F6/NOapSiP8eWy', NULL, NULL),
(2, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$wV95HDGlgL720gsUxGYrPOK8.jY/4g99ceCewYq1OBTkfUDD2gSz.', NULL, NULL),
(3, 'fellipy', '', '540.689.518-45', 'fellipysilva986@gmail.com', '$2y$10$HBGpE83aucpb39yvNykqCu5IEHVKn3japM8Q9ycZ9J84dQ.2v6J7i', NULL, NULL),
(4, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$RuIYqoZ5wOS7URETUjJw.OZNfjZhyEoNCPw8zxu5GWWx.gNmHf4Ha', NULL, NULL);

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
  `idProduto` int(11) DEFAULT NULL,
  `idCliente` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`idVendas`, `valor`, `data`, `formaDePagamento`, `idProduto`, `idCliente`) VALUES
(1, 79.99, '2026-09-20', 'Cartao', NULL, NULL),
(2, 79.99, '2026-09-20', 'Cartao', NULL, NULL),
(3, 79.99, '2026-09-20', 'Dinheiro', NULL, NULL),
(4, 39.99, '2026-09-20', 'Crediario', NULL, NULL),
(5, 39.99, '2026-09-20', 'Cartao', NULL, NULL),
(6, 99.90, '2026-09-21', 'Pix', NULL, NULL),
(7, 79.99, '2026-09-22', 'Dinheiro', NULL, NULL),
(8, 99.90, '2026-09-23', 'Crediario', NULL, NULL);

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
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`idItem`);

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
  MODIFY `idFuncionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `idProduto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `vendaprodutos`
--
ALTER TABLE `vendaprodutos`
  MODIFY `idVendaProdutos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `idVendas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
