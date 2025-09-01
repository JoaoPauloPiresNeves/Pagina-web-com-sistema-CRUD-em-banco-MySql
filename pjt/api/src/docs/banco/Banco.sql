  -- MySQL Workbench Forward Engineering

  SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
  SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
  SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

  -- -----------------------------------------------------
  -- Schema pjt2b
  -- -----------------------------------------------------
  CREATE SCHEMA IF NOT EXISTS `pjt2b` DEFAULT CHARACTER SET utf8;
  USE `pjt2b`;

  -- -----------------------------------------------------
  -- Table `clientes`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `clientes` (
    `idclientes` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome_cliente` VARCHAR(100) NOT NULL,
    `email_cliente` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`idclientes`),
    UNIQUE INDEX `idclientes_UNIQUE` (`idclientes`),
    UNIQUE INDEX `email_cliente_UNIQUE` (`email_cliente`)
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Table `produtos`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `produtos` (
    `idprodutos` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome_produto` VARCHAR(45) NOT NULL,
    `preco_produto` DOUBLE(6,2) UNSIGNED NOT NULL,
    PRIMARY KEY (`idprodutos`),
    UNIQUE INDEX `idprodutos_UNIQUE` (`idprodutos`),
    UNIQUE INDEX `nome_produto_UNIQUE` (`nome_produto`)
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Table `pedidos`
  -- -----------------------------------------------------
  CREATE TABLE IF NOT EXISTS `pedidos` (
    `idpedidos` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `data_pedido` DATE NOT NULL,
    `clientes_idclientes` INT UNSIGNED NOT NULL,
    `produtos_idprodutos` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`idpedidos`),
    UNIQUE INDEX `idpedidos_UNIQUE` (`idpedidos`),
    INDEX `fk_pedidos_clientes_idx` (`clientes_idclientes`),
    INDEX `fk_pedidos_produtos1_idx` (`produtos_idprodutos`),
    CONSTRAINT `fk_pedidos_clientes`
      FOREIGN KEY (`clientes_idclientes`)
      REFERENCES `clientes` (`idclientes`)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_pedidos_produtos1`
      FOREIGN KEY (`produtos_idprodutos`)
      REFERENCES `produtos` (`idprodutos`)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
  ) ENGINE = InnoDB;

  -- -----------------------------------------------------
  -- Dados para a tabela `clientes`
  -- -----------------------------------------------------
  START TRANSACTION;
  INSERT INTO `clientes` (`idclientes`, `nome_cliente`, `email_cliente`) VALUES 
  (1, 'Gabrielly', 'gaby@gmail.com'),
  (2, 'João Paulo', 'joãop@gmail.com'),
  (3, 'Lucas', 'lucas@gmail.com'),
  (4, 'Vinicius', 'vinicius@gmail.com'),
  (5, 'Isabela', 'isabela@gmail.com'),
  (6, 'Marcos', 'marcos@gmail.com'),
  (7, 'Mariana', 'mary@gmail.com'),
  (8, 'Eduardo', 'eduardo@gmail.com'),
  (9, 'Amanda', 'amanda@gmail.com'),
  (10, 'Pedro', 'pedro@gmail.com'),
  (11, 'Arthur', 'arthur@gmail.com'),
  (12, 'Felipe', 'felipe@gmail.com');
  COMMIT;

  -- -----------------------------------------------------
  -- Dados para a tabela `produtos`
  -- -----------------------------------------------------
  START TRANSACTION;
  INSERT INTO `produtos` (`idprodutos`, `nome_produto`, `preco_produto`) VALUES 
  (1, 'Guitarra Tagima Stratocaster', 560.99),
  (2, 'Caixa de som Jbl', 1245.99),
  (3, 'Paleta de guitarra', 13.87),
  (4, 'Cabo P10', 24.89),
  (5, 'Amplificador Tube', 6999.99),
  (6, 'Pedal de Distorção', 567.99),
  (7, 'Kit 5 cordas para guitarra elétrica', 56.89),
  (8, 'Capo para guitarra', 99.99),
  (9, 'Capa bag para guitarra', 57.36),
  (10, 'Pedal Multiefeitos', 4679.99);
  COMMIT;

  -- -----------------------------------------------------
  -- Dados para a tabela `pedidos`
  -- -----------------------------------------------------
  START TRANSACTION;
  INSERT INTO `pedidos` (`data_pedido`, `clientes_idclientes`, `produtos_idprodutos`) VALUES
  ('2025-02-16', 1, 1),
  ('2025-01-08', 1, 2),
  ('2025-05-14', 2, 3),
  ('2025-03-30', 2, 4),
  ('2025-04-02', 2, 5),
  ('2025-04-02', 3, 6),
  ('2025-05-01', 4, 7),
  ('2025-01-23', 5, 8),
  ('2025-03-11', 5, 9),
  ('2025-04-27', 5, 10),
  ('2025-04-27', 6, 1),
  ('2025-02-07', 7, 2),
  ('2025-02-07', 8, 3),
  ('2025-02-07', 9, 4),
  ('2025-03-06', 10, 5),
  ('2025-01-17', 10, 6),
  ('2025-05-20', 11, 7),
  ('2025-02-25', 11, 8),
  ('2025-04-11', 11, 9),
  ('2025-04-11', 12, 10);
  COMMIT;

  -- Finalizando
  SET SQL_MODE=@OLD_SQL_MODE;
  SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
  SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
