-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 21/10/2026 às 02:03
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
(0, 'Av.Gilberto Vergueiro da Silva', 45, 'Chararas palmeirinhas', '13737');

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
  `cargo` enum('chefe','funcionario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idEnderecoFuncionario` int DEFAULT NULL,
  PRIMARY KEY (`idFuncionario`),
  KEY `idEnderecoFuncionario` (`idEnderecoFuncionario`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `idProduto` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoDeBarras` int DEFAULT NULL,
  `valor` float(10,2) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `estoque` int DEFAULT NULL,
  `Armazenamento` enum('Refrigerado','Normal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('frios','defumados','doces','bebidas','queijos') COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  UNIQUE KEY `uq_produtos_codigo_barras` (`codigoDeBarras`),
  KEY `idFornecedor` (`idFornecedor`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `nome`, `codigoDeBarras`, `valor`, `validade`, `estoque`, `Armazenamento`, `categoria`, `imagem`, `idFornecedor`) VALUES
(26, 'Queijo Fresco 500g ', 8, 24.99, NULL, 4, 'Refrigerado', 'queijos', '../imagens/6aa8618563462_21c80348-2feb-4477-aaba-2020e17f1a6e.jpeg', NULL),
(25, 'Requeijão de Colher ', 7, 19.99, NULL, 7, 'Refrigerado', 'queijos', '../imagens/6aa86165dfb53_1e2aa7ce-3b63-447c-838b-890176620dc8.jpeg', NULL),
(19, 'Queijo Recheado com requeijão ', 1, 29.99, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa86089a4e18_c875ebfe-7a67-4c5a-b9af-ba9724cc13ac.jpeg', NULL),
(20, 'Requeijão Scala 400g', 2, 19.99, NULL, 4, 'Refrigerado', 'queijos', '../imagens/6aa860aa8a1e0_2aea2284-f65e-4234-95c2-f7e6cdc6aa57.jpeg', NULL),
(21, 'Kit 4 Queijos', 3, 34.99, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa860c51dc32_2a460709-432c-458c-8db3-e39c3b199bd7.jpeg', NULL),
(22, 'Salaminho Fatiado ', 4, 8.99, NULL, 7, 'Refrigerado', 'defumados', '../imagens/6aa860e7d9eed_711f8246-79bc-4507-9b3d-3757d979f47a.jpeg', NULL),
(23, 'Queijo Azul ', 5, 99.90, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa86105d65e8_8c865777-613a-4063-957d-5b1bba7415f3.jpeg', NULL),
(24, 'Queijo Fresco ', 6, 39.99, NULL, 3, 'Refrigerado', 'queijos', '../imagens/6aa86133900f8_274255c8-8aa9-4bea-b398-6b48e2efd482.jpeg', NULL),
(27, 'Queijo parmesão ', 9, 79.99, NULL, 9, 'Refrigerado', 'queijos', '../imagens/6aa8619fb1b51_25b9a594-75b1-4127-9884-39a8184e915c.jpeg', NULL),
(28, 'Queijo Minas Padrão ', 10, 39.99, NULL, 9, 'Refrigerado', 'queijos', '../imagens/6aa861bf729b9_f94eb560-ac1b-4d51-903c-5d41147abb3f.jpeg', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` char(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int DEFAULT NULL,
  `idFuncionario` int DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idFornecedor` (`idFornecedor`),
  KEY `idFuncionario` (`idFuncionario`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nome`, `foto`, `cpf`, `email`, `senha`, `idFornecedor`, `idFuncionario`) VALUES
(1, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$VgiEz..hrP9eJGhHrVdbQ..bjYK/F7tuBR9ndy4F6/NOapSiP8eWy', NULL, NULL),
(2, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$wV95HDGlgL720gsUxGYrPOK8.jY/4g99ceCewYq1OBTkfUDD2gSz.', NULL, NULL),
(3, 'fellipy', '', '540.689.518-45', 'fellipysilva986@gmail.com', '$2y$10$HBGpE83aucpb39yvNykqCu5IEHVKn3japM8Q9ycZ9J84dQ.2v6J7i', NULL, NULL),
(4, 'Leonardo Luis', '', '919.219.291-21', 'leonardoluis1427@gmail.com', '$2y$10$SeypIpJGJvoJklRJfUFzjuLuHEyvR1.o4o8Mtrg97a47UXMk58Mc6', NULL, NULL),
(5, 'Leonardo', '', '180.096.986-44', 'leonardoluis1427@gmail.com', '$2y$10$h09bzOC8WJrlnPYjnQG/Y.UZFysZzOZlJ4YNW8Y8DM1rL14PYhhvq', NULL, NULL),
(6, 'Leonardo Luis', '', '180.096.986-44', 'leonardoluis1427@gmail.com', '$2y$10$qQ8cCJ4ovuA98Fgd222bU.9YUJ8G3UNT1NCK0yxYSVQ0.NUtFLZ0i', NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendaprodutos`
--

INSERT INTO `vendaprodutos` (`idVendaProdutos`, `quantidade`, `idVenda`, `idProduto`) VALUES
(1, 1.00, 7, 24),
(2, 1.00, 8, 26),
(3, 1.00, 9, 22),
(4, 1.00, 10, 25),
(5, 1.00, 11, 24),
(6, 1.00, 16, 26),
(7, 1.00, 17, 26),
(8, 1.00, 19, 24),
(9, 1.00, 20, 26),
(10, 1.00, 20, 20),
(11, 1.00, 20, 25),
(12, 1.00, 21, 27),
(13, 1.00, 22, 26),
(14, 1.00, 23, 24),
(15, 1.00, 24, 24),
(16, 1.00, 25, 26),
(17, 1.00, 25, 24),
(18, 1.00, 25, 22),
(19, 1.00, 26, 28),
(20, 1.00, 31, 24),
(21, 1.00, 31, 25);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` int NOT NULL AUTO_INCREMENT,
  `valor` float(10,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `formaDePagamento` enum('Dinheiro','Cartao','Debito','Credito','Pix','Crediario') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descponto` float(10,2) DEFAULT NULL,
  `acrecimo` float(10,2) DEFAULT NULL,
  `idProduto` int DEFAULT NULL,
  `idCliente` int DEFAULT NULL,
  `nomeCliente` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  KEY `idProduto` (`idProduto`),
  KEY `idCliente` (`idCliente`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`idVendas`, `valor`, `data`, `formaDePagamento`, `descponto`, `acrecimo`, `idProduto`, `idCliente`, `nomeCliente`) VALUES
(1, 6.90, '2026-10-19', 'Pix', NULL, NULL, NULL, NULL, NULL),
(2, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL),
(3, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL),
(4, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL),
(5, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL),
(6, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL),
(7, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL),
(8, 24.99, '2026-10-19', 'Cartao', NULL, NULL, NULL, 5, NULL),
(9, 8.99, '2026-10-19', 'Pix', NULL, NULL, NULL, 5, NULL),
(10, 19.99, '2026-10-19', 'Crediario', NULL, NULL, NULL, 5, NULL),
(11, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 6, 'Leo'),
(12, 29.99, '2026-10-20', '', NULL, NULL, NULL, NULL, NULL),
(13, 29.99, '2026-10-20', '', NULL, NULL, NULL, NULL, NULL),
(14, 29.99, '2026-10-20', 'Debito', NULL, NULL, NULL, NULL, NULL),
(15, 19.99, '2026-10-20', 'Credito', NULL, NULL, NULL, NULL, NULL),
(16, 24.99, '2026-10-20', 'Debito', NULL, NULL, NULL, 6, 'Leo'),
(17, 24.99, '2026-10-20', 'Credito', NULL, NULL, NULL, 6, 'Leo'),
(18, 29.99, '2026-10-20', 'Pix', NULL, NULL, NULL, NULL, NULL),
(19, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 6, 'Leo'),
(20, 64.97, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo'),
(21, 79.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo'),
(22, 24.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo'),
(23, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo'),
(24, 39.99, '2026-10-20', 'Debito', NULL, NULL, NULL, 5, 'Leo'),
(25, 73.97, '2026-10-20', 'Debito', NULL, NULL, NULL, 5, 'Leo'),
(26, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo'),
(27, 29.99, '2026-10-20', 'Pix', NULL, NULL, NULL, NULL, NULL),
(28, 29.99, '2026-10-20', 'Pix', NULL, NULL, NULL, NULL, NULL),
(29, 63.97, '2026-10-20', 'Crediario', NULL, NULL, NULL, NULL, NULL),
(30, 54.98, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, NULL, NULL),
(31, 59.98, '2026-10-20', 'Debito', NULL, NULL, NULL, 5, 'Leo');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
