-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 27/05/2026 às 12:58
-- Versão do servidor: 8.4.7
-- Versão do PHP: 8.3.28

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

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `idCliente` int NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` char(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idEndereco` int DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  KEY `idEndereco` (`idEndereco`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosclientes`
--

DROP TABLE IF EXISTS `enderecosclientes`;
CREATE TABLE IF NOT EXISTS `enderecosclientes` (
  `idEnderecoCliente` int NOT NULL,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoCliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `enderecosclientes`
--

INSERT INTO `enderecosclientes` (`idEnderecoCliente`, `rua`, `numero`, `bairro`, `cep`) VALUES
(0, 'RUA', 1, 'por do sol', '111111111');

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosfornecedores`
--

DROP TABLE IF EXISTS `enderecosfornecedores`;
CREATE TABLE IF NOT EXISTS `enderecosfornecedores` (
  `idEnderecoFornecedor` int NOT NULL AUTO_INCREMENT,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoFornecedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosfuncionarios`
--

DROP TABLE IF EXISTS `enderecosfuncionarios`;
CREATE TABLE IF NOT EXISTS `enderecosfuncionarios` (
  `idEnderecoFuncionrio` int NOT NULL AUTO_INCREMENT,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoFuncionrio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `idFornecedor` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` int DEFAULT NULL,
  `idEnderecoFornecedor` int DEFAULT NULL,
  PRIMARY KEY (`idFornecedor`),
  KEY `idEnderecoFornecedor` (`idEnderecoFornecedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE IF NOT EXISTS `funcionarios` (
  `idFuncionario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nimero` int DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idEnderecoFuncionario` int DEFAULT NULL,
  PRIMARY KEY (`idFuncionario`),
  KEY `idEnderecoFuncionario` (`idEnderecoFuncionario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `idProdutos` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoDeBarras` int DEFAULT NULL,
  `valor` float(10,2) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `estoque` int DEFAULT NULL,
  `iamgem` blob,
  `idFornecedor` int DEFAULT NULL,
  PRIMARY KEY (`idProdutos`),
  KEY `idFornecedor` (`idFornecedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` char(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int DEFAULT NULL,
  `idFuncionario` int DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idFornecedor` (`idFornecedor`),
  KEY `idFuncionario` (`idFuncionario`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nome`, `cpf`, `email`, `senha`, `idFornecedor`, `idFuncionario`) VALUES
(1, 'Rodrigo', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$VgiEz..hrP9eJGhHrVdbQ..bjYK/F7tuBR9ndy4F6/NOapSiP8eWy', NULL, NULL),
(2, 'Rodrigo', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$wV95HDGlgL720gsUxGYrPOK8.jY/4g99ceCewYq1OBTkfUDD2gSz.', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendaprodutos`
--

DROP TABLE IF EXISTS `vendaprodutos`;
CREATE TABLE IF NOT EXISTS `vendaprodutos` (
  `idVendaProdutos` int NOT NULL AUTO_INCREMENT,
  `quantidade` float(10,2) DEFAULT NULL,
  `idVenda` int DEFAULT NULL,
  `idProduto` int DEFAULT NULL,
  PRIMARY KEY (`idVendaProdutos`),
  KEY `idVenda` (`idVenda`),
  KEY `idProduto` (`idProduto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` int NOT NULL AUTO_INCREMENT,
  `valor` float(10,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `formaDePagamento` enum('Dinheiro','Cartao','Pix','Crediario') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descponto` float(10,2) DEFAULT NULL,
  `acrecimo` float(10,2) DEFAULT NULL,
  `idProduto` int DEFAULT NULL,
  `idCliente` int DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  KEY `idProduto` (`idProduto`),
  KEY `idCliente` (`idCliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
