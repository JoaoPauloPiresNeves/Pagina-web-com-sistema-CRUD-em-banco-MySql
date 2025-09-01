CREATE TABLE `cargo` (
  `idCargo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomeCargo` varchar(64) NOT NULL,
  PRIMARY KEY (`idCargo`),
  UNIQUE KEY `idCargo_UNIQUE` (`idCargo`),
  UNIQUE KEY `nomeCargo_UNIQUE` (`nomeCargo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `cargo` (`idCargo`, `nomeCargo`) VALUES ('1', 'Administrador');
INSERT INTO `cargo` (`idCargo`, `nomeCargo`) VALUES ('4', 'Analista de Sistemas Jr');
INSERT INTO `cargo` (`idCargo`, `nomeCargo`) VALUES ('5', 'Auditor de dados Pleno');
INSERT INTO `cargo` (`idCargo`, `nomeCargo`) VALUES ('6', 'Auditor de dados Pleno Jr');
INSERT INTO `cargo` (`idCargo`, `nomeCargo`) VALUES ('2', 'Técnico em Informática Jr');
INSERT INTO `cargo` (`idCargo`, `nomeCargo`) VALUES ('3', 'Técnico em Informática Pleno');


CREATE TABLE `funcionario` (
  `idFuncionario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomeFuncionario` varchar(128) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `senha` varchar(64) DEFAULT NULL,
  `recebeValeTransporte` tinyint(1) DEFAULT NULL,
  `Cargo_idCargo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idFuncionario`),
  UNIQUE KEY `idFuncionario_UNIQUE` (`idFuncionario`),
  KEY `fk_Funcionario_Cargo_idx` (`Cargo_idCargo`),
  CONSTRAINT `fk_Funcionario_Cargo` FOREIGN KEY (`Cargo_idCargo`) REFERENCES `cargo` (`idCargo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



