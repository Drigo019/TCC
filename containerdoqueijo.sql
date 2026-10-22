-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 22/10/2026 às 02:46
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
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` char(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `rua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `bairro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `rua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `bairro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoFornecedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecosfuncionarios`
--

DROP TABLE IF EXISTS `enderecosfuncionarios`;
CREATE TABLE IF NOT EXISTS `enderecosfuncionarios` (
  `idEnderecoFuncionrio` int NOT NULL AUTO_INCREMENT,
  `rua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `bairro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` char(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEnderecoFuncionrio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `idFornecedor` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` enum('chefe','funcionario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoDeBarras` int DEFAULT NULL,
  `valor` float(10,2) DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `estoque` int DEFAULT NULL,
  `Armazenamento` enum('Refrigerado','Normal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('frios','defumados','doces','bebidas','queijos') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  UNIQUE KEY `uq_produtos_codigo_barras` (`codigoDeBarras`),
  KEY `idFornecedor` (`idFornecedor`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `nome`, `codigoDeBarras`, `valor`, `validade`, `estoque`, `Armazenamento`, `categoria`, `imagem`, `idFornecedor`) VALUES
(26, 'Queijo Fresco 500g ', 8, 24.99, NULL, 5, 'Refrigerado', 'queijos', '../imagens/6aa8618563462_21c80348-2feb-4477-aaba-2020e17f1a6e.jpeg', NULL),
(25, 'Requeijão de Colher ', 7, 19.99, NULL, 7, 'Refrigerado', 'queijos', '../imagens/6aa86165dfb53_1e2aa7ce-3b63-447c-838b-890176620dc8.jpeg', NULL),
(19, 'Queijo Recheado com requeijão ', 1, 29.99, NULL, 9, 'Refrigerado', 'queijos', '../imagens/6aa86089a4e18_c875ebfe-7a67-4c5a-b9af-ba9724cc13ac.jpeg', NULL),
(20, 'Requeijão Scala 400g', 2, 19.99, NULL, 2, 'Refrigerado', 'queijos', '../imagens/6aa860aa8a1e0_2aea2284-f65e-4234-95c2-f7e6cdc6aa57.jpeg', NULL),
(21, 'Kit 4 Queijos', 3, 34.99, NULL, 7, 'Refrigerado', 'queijos', '../imagens/6aa860c51dc32_2a460709-432c-458c-8db3-e39c3b199bd7.jpeg', NULL),
(22, 'Salaminho Fatiado ', 4, 8.99, NULL, 7, 'Refrigerado', 'defumados', '../imagens/6aa860e7d9eed_711f8246-79bc-4507-9b3d-3757d979f47a.jpeg', NULL),
(23, 'Queijo Azul ', 5, 99.90, NULL, 10, 'Refrigerado', 'queijos', '../imagens/6aa86105d65e8_8c865777-613a-4063-957d-5b1bba7415f3.jpeg', NULL),
(24, 'Queijo Fresco ', 6, 39.99, NULL, 3, 'Refrigerado', 'queijos', '../imagens/6aa86133900f8_274255c8-8aa9-4bea-b398-6b48e2efd482.jpeg', NULL),
(27, 'Queijo parmesão ', 9, 79.99, NULL, 8, 'Refrigerado', 'queijos', '../imagens/6aa8619fb1b51_25b9a594-75b1-4127-9884-39a8184e915c.jpeg', NULL),
(28, 'Queijo Minas Padrão ', 10, 39.99, NULL, 9, 'Refrigerado', 'queijos', '../imagens/6aa861bf729b9_f94eb560-ac1b-4d51-903c-5d41147abb3f.jpeg', NULL),
(35, 'Doce de leite pérola', 100, 19.99, NULL, 30, '', 'doces', '../imagens/6ad9741173a515.39022775_6ad8a88f6d7da2.63391930_WhatsApp Image 2026-10-20 at 08.28.40.jpeg', NULL),
(36, 'Doce de leite vimilk', 101, 36.90, NULL, 30, '', 'doces', '../imagens/6ad974754a1c17.84146652_6ad8a8d47819a9.84064073_WhatsApp Image 2026-10-20 at 08.27.27.jpeg', NULL),
(37, 'Vinho Bordô', 102, 29.90, NULL, 30, '', 'bebidas', '../imagens/6ad974eb5b9497.26745335_6ad8a9fa90d6a1.49128536_WhatsApp Image 2026-10-19 at 18.13.50 (2).jpeg', NULL),
(38, 'Cachaça de Alambique', 103, 49.99, NULL, 30, '', 'bebidas', '../imagens/6ad97509a3b303.77317006_6ad8a94f962785.62270568_WhatsApp Image 2026-10-19 at 18.13.50.jpeg', NULL),
(39, 'Cachaça vale da Canastra', 104, 49.99, NULL, 30, '', 'bebidas', '../imagens/6ad97559f420e5.84239669_6ad8a98bc7c544.03668804_WhatsApp Image 2026-10-19 at 18.13.50 (1).jpeg', NULL),
(40, 'Mel com favo', 105, 29.99, NULL, 30, '', 'doces', '../imagens/6ad975ac48e303.19849816_6ad8a831d660d5.63951384_WhatsApp Image 2026-10-20 at 08.19.51.jpeg', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

DROP TABLE IF EXISTS `recuperacao_senha`;
CREATE TABLE IF NOT EXISTS `recuperacao_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `token_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expira_em` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT '0',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `idx_recuperacao_cliente` (`idUsuario`),
  KEY `idx_recuperacao_expiracao` (`expira_em`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `idUsuario`, `token_hash`, `expira_em`, `utilizado`, `criado_em`) VALUES
(3, 9, 'd0e9d0a13797b8813851dc72a1c71b269e7d5592fe49435347b7a373cab27326', '2026-10-21 23:17:59', 0, '2026-10-21 23:02:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` char(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idFornecedor` int DEFAULT NULL,
  `idFuncionario` int DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idFornecedor` (`idFornecedor`),
  KEY `idFuncionario` (`idFuncionario`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nome`, `foto`, `cpf`, `email`, `senha`, `idFornecedor`, `idFuncionario`) VALUES
(1, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$VgiEz..hrP9eJGhHrVdbQ..bjYK/F7tuBR9ndy4F6/NOapSiP8eWy', NULL, NULL),
(2, 'Rodrigo', '', '111.111.111-11', 'rcarvalho15022009@gmail.com', '$2y$10$wV95HDGlgL720gsUxGYrPOK8.jY/4g99ceCewYq1OBTkfUDD2gSz.', NULL, NULL),
(3, 'fellipy', '', '540.689.518-45', 'fellipysilva986@gmail.com', '$2y$10$HBGpE83aucpb39yvNykqCu5IEHVKn3japM8Q9ycZ9J84dQ.2v6J7i', NULL, NULL),
(9, 'Leonardo Luis', '', '180.096.986-44', 'leonardoluis1427@gmail.com', '$2y$10$TRFLKZGpwthHrj4Xzr49p.S2/B84yM1lc2mAmphIO9CzHZUk31fHC', NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(21, 1.00, 31, 25),
(22, 1.00, 32, 26),
(23, 1.00, 35, 27);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` int NOT NULL AUTO_INCREMENT,
  `valor` float(10,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `formaDePagamento` enum('Dinheiro','Cartao','Debito','Credito','Pix','Crediario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descponto` float(10,2) DEFAULT NULL,
  `acrecimo` float(10,2) DEFAULT NULL,
  `idProduto` int DEFAULT NULL,
  `idCliente` int DEFAULT NULL,
  `nomeCliente` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataVencimento` date DEFAULT NULL,
  `statusCrediario` enum('Pendente','Pago') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataPagamento` date DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  KEY `idProduto` (`idProduto`),
  KEY `idCliente` (`idCliente`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`idVendas`, `valor`, `data`, `formaDePagamento`, `descponto`, `acrecimo`, `idProduto`, `idCliente`, `nomeCliente`, `dataVencimento`, `statusCrediario`, `dataPagamento`) VALUES
(1, 6.90, '2026-10-19', 'Pix', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(3, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(4, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(5, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(6, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(7, 39.99, '2026-10-19', 'Dinheiro', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(8, 24.99, '2026-10-19', 'Cartao', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(9, 8.99, '2026-10-19', 'Pix', NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(10, 19.99, '2026-10-19', 'Crediario', NULL, NULL, NULL, 5, NULL, NULL, 'Pago', '2026-10-21'),
(11, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 6, 'Leo', NULL, NULL, NULL),
(12, 29.99, '2026-10-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 29.99, '2026-10-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 29.99, '2026-10-20', 'Debito', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 19.99, '2026-10-20', 'Credito', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 24.99, '2026-10-20', 'Debito', NULL, NULL, NULL, 6, 'Leo', NULL, NULL, NULL),
(17, 24.99, '2026-10-20', 'Credito', NULL, NULL, NULL, 6, 'Leo', NULL, NULL, NULL),
(18, 29.99, '2026-10-20', 'Pix', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 6, 'Leo', NULL, NULL, NULL),
(20, 64.97, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(21, 79.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(22, 24.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(23, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(24, 39.99, '2026-10-20', 'Debito', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(25, 73.97, '2026-10-20', 'Debito', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(26, 39.99, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(27, 29.99, '2026-10-20', 'Pix', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 29.99, '2026-10-20', 'Pix', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 63.97, '2026-10-20', 'Crediario', NULL, NULL, NULL, NULL, NULL, NULL, 'Pago', '2026-10-21'),
(30, 54.98, '2026-10-20', 'Dinheiro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 59.98, '2026-10-20', 'Debito', NULL, NULL, NULL, 5, 'Leo', NULL, NULL, NULL),
(32, 24.99, '2026-10-21', 'Dinheiro', NULL, NULL, NULL, 9, 'Leo', NULL, NULL, NULL),
(33, 19.99, '2026-10-21', 'Dinheiro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 29.99, '2026-10-21', 'Crediario', NULL, NULL, NULL, NULL, 'Leonardo Luis Barbosa Guilherme', '2026-10-22', 'Pago', '2026-10-21'),
(35, 79.99, '2026-10-21', 'Crediario', NULL, NULL, NULL, 9, 'Leo', NULL, 'Pago', '2026-10-21'),
(36, 19.99, '2026-10-21', 'Crediario', NULL, NULL, NULL, NULL, 'Leo', '2026-10-22', 'Pago', '2026-10-21'),
(37, 34.99, '2026-10-21', 'Crediario', NULL, NULL, NULL, NULL, 'Leo', '2026-10-22', 'Pendente', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
