-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 23-Set-2026 às 15:15
-- Versão do servidor: 5.7.36
-- versão do PHP: 7.4.26

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
-- Estrutura da tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `idCliente` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` char(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idEndereco` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  KEY `idEndereco` (`idEndereco`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecosclientes`
--

DROP TABLE IF EXISTS `enderecosclientes`;
CREATE TABLE IF NOT EXISTS `enderecosclientes` (
  `idEnderecoCliente` int(11) NOT NULL,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoCliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `enderecosclientes`
--

INSERT INTO `enderecosclientes` (`idEnderecoCliente`, `rua`, `numero`, `bairro`, `cep`) VALUES
(0, 'Av.Gilberto Vergueiro da Silva', 45, 'Chararas palmeirinhas', '13737');

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecosfornecedores`
--

DROP TABLE IF EXISTS `enderecosfornecedores`;
CREATE TABLE IF NOT EXISTS `enderecosfornecedores` (
  `idEnderecoFornecedor` int(11) NOT NULL AUTO_INCREMENT,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoFornecedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecosfuncionarios`
--

DROP TABLE IF EXISTS `enderecosfuncionarios`;
CREATE TABLE IF NOT EXISTS `enderecosfuncionarios` (
  `idEnderecoFuncionrio` int(11) NOT NULL AUTO_INCREMENT,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoFuncionrio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `idFornecedor` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` int(11) DEFAULT NULL,
  `idEnderecoFornecedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`idFornecedor`),
  KEY `idEnderecoFornecedor` (`idEnderecoFornecedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionarios`
--

DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE IF NOT EXISTS `funcionarios` (
  `idFuncionario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` enum('chefe','funcionario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idEnderecoFuncionario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idFuncionario`),
  KEY `idEnderecoFuncionario` (`idEnderecoFuncionario`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `funcionarios`
--

INSERT INTO `funcionarios` (`idFuncionario`, `nome`, `cargo`, `numero`, `email`, `senha`, `idEnderecoFuncionario`) VALUES
(1, 'Rodrigo Jesus de Carvalho', 'funcionario', NULL, NULL, NULL, NULL),
(2, 'Rodrigo Jesus de Carvalho', 'funcionario', NULL, NULL, NULL, NULL),
(3, '1', 'chefe', NULL, NULL, NULL, NULL),
(4, 'Rodrigo', 'funcionario', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens_venda`
--

DROP TABLE IF EXISTS `itens_venda`;
CREATE TABLE IF NOT EXISTS `itens_venda` (
  `idItem` int(11) NOT NULL AUTO_INCREMENT,
  `idVenda` int(11) NOT NULL,
  `idProduto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valorUnitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idItem`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `itens_venda`
--

INSERT INTO `itens_venda` (`idItem`, `idVenda`, `idProduto`, `quantidade`, `valorUnitario`) VALUES
(1, 3, 27, 1, '79.99'),
(2, 4, 24, 1, '39.99'),
(3, 5, 24, 1, '39.99'),
(4, 6, 23, 1, '99.90'),
(5, 7, 27, 1, '79.99'),
(6, 8, 23, 1, '99.90'),
(7, 9, 23, 1, '99.90'),
(8, 9, 21, 1, '34.99');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `idProduto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoDeBarras` int(11) DEFAULT NULL,
  `valor` float(10,2) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `Armazenamento` enum('Refrigerado','Normal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('frios','defumados','doces','bebidas','queijos') COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  KEY `idFornecedor` (`idFornecedor`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `nome`, `codigoDeBarras`, `valor`, `validade`, `estoque`, `Armazenamento`, `categoria`, `imagem`, `idFornecedor`) VALUES
(26, 'Queijo Fresco 500g ', 8, 24.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa8618563462_21c80348-2feb-4477-aaba-2020e17f1a6e.jpeg', NULL),
(25, 'Requeijao de Colher ', 7, 19.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa86165dfb53_1e2aa7ce-3b63-447c-838b-890176620dc8.jpeg', NULL),
(19, 'Queijo Recheado com requeijao ', 1, 29.99, NULL, 1, 'Refrigerado', 'queijos', '../imagens/6aa86089a4e18_c875ebfe-7a67-4c5a-b9af-ba9724cc13ac.jpeg', NULL),
(20, 'Requeijao Scala 400g', 2, 19.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa860aa8a1e0_2aea2284-f65e-4234-95c2-f7e6cdc6aa57.jpeg', NULL),
(21, 'Kit 4 Queijos', 3, 34.99, NULL, 132, 'Refrigerado', 'queijos', '../imagens/6aa860c51dc32_2a460709-432c-458c-8db3-e39c3b199bd7.jpeg', NULL),
(22, 'Salaminho Fatiado ', 4, 8.99, NULL, 10, 'Refrigerado', 'defumados', '../imagens/6aa860e7d9eed_711f8246-79bc-4507-9b3d-3757d979f47a.jpeg', NULL),
(23, 'Queijo Azul ', 5, 99.90, NULL, 7, 'Refrigerado', 'queijos', '../imagens/6aa86105d65e8_8c865777-613a-4063-957d-5b1bba7415f3.jpeg', NULL),
(24, 'Queijo Fresco ', 6, 39.99, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa86133900f8_274255c8-8aa9-4bea-b398-6b48e2efd482.jpeg', NULL),
(27, 'Queijo parmesao ', 9, 79.99, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa8619fb1b51_25b9a594-75b1-4127-9884-39a8184e915c.jpeg', NULL),
(28, 'Queijo Minas Padrao ', 10, 39.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa861bf729b9_f94eb560-ac1b-4d51-903c-5d41147abb3f.jpeg', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` char(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int(11) DEFAULT NULL,
  `idFuncionario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idFornecedor` (`idFornecedor`),
  KEY `idFuncionario` (`idFuncionario`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nome`, `foto`, `cpf`, `email`, `senha`, `idFornecedor`, `idFuncionario`) VALUES
(1, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$VgiEz..hrP9eJGhHrVdbQ..bjYK/F7tuBR9ndy4F6/NOapSiP8eWy', NULL, NULL),
(2, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$wV95HDGlgL720gsUxGYrPOK8.jY/4g99ceCewYq1OBTkfUDD2gSz.', NULL, NULL),
(3, 'fellipy', '', '540.689.518-45', 'fellipysilva986@gmail.com', '$2y$10$HBGpE83aucpb39yvNykqCu5IEHVKn3japM8Q9ycZ9J84dQ.2v6J7i', NULL, NULL),
(4, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$RuIYqoZ5wOS7URETUjJw.OZNfjZhyEoNCPw8zxu5GWWx.gNmHf4Ha', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendaprodutos`
--

DROP TABLE IF EXISTS `vendaprodutos`;
CREATE TABLE IF NOT EXISTS `vendaprodutos` (
  `idVendaProdutos` int(11) NOT NULL AUTO_INCREMENT,
  `quantidade` float(10,2) DEFAULT NULL,
  `idVenda` int(11) DEFAULT NULL,
  `idProduto` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVendaProdutos`),
  KEY `idVenda` (`idVenda`),
  KEY `idProduto` (`idProduto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` int(11) NOT NULL AUTO_INCREMENT,
  `valor` float(10,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `formaDePagamento` enum('Dinheiro','Cartao','Debito','Credito','Pix','Crediario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `idProduto` int(11) DEFAULT NULL,
  `idCliente` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  KEY `idProduto` (`idProduto`),
  KEY `idCliente` (`idCliente`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `vendas`
--

INSERT INTO `vendas` (`idVendas`, `valor`, `data`, `formaDePagamento`, `idProduto`, `idCliente`) VALUES
(1, 79.99, '2026-09-20', 'Cartao', NULL, NULL),
(2, 79.99, '2026-09-20', 'Cartao', NULL, NULL),
(3, 79.99, '2026-09-20', 'Dinheiro', NULL, NULL),
(4, 39.99, '2026-09-20', 'Crediario', NULL, NULL),
(5, 39.99, '2026-09-20', 'Cartao', NULL, NULL),
(6, 99.90, '2026-09-21', 'Pix', NULL, NULL),
(7, 79.99, '2026-09-22', 'Dinheiro', NULL, NULL),
(8, 99.90, '2026-09-23', 'Crediario', NULL, NULL),
(9, 134.89, '2026-09-23', 'Pix', NULL, NULL),
(10, 29.99, '2026-09-23', 'Pix', NULL, NULL),
(11, 29.99, '2026-09-23', 'Cartao', NULL, NULL),
(12, 29.99, '2026-09-23', 'Cartao', NULL, NULL),
(13, 29.99, '2026-09-23', 'Cartao', NULL, NULL),
(14, 29.99, '2026-09-23', 'Debito', NULL, NULL),
(15, 29.99, '2026-09-23', 'Credito', NULL, NULL),
(16, 59.98, '2026-09-23', 'Pix', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
