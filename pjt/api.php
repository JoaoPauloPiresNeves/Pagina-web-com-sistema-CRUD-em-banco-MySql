<?php
/*
* API REST didática desenvolvida com o objetivo de
* ensinar, de forma simples e prática, os principais conceitos da arquitetura REST
* e do padrão de projeto MVC (Model-View-Controller).
*
* A API realiza o CRUD completo (Create, Read, Update, Delete) das tabelas `cargo` e `funcionario`,
* sendo ideal para estudantes e desenvolvedores que estão começando com PHP moderno e boas práticas de organização.
*
* A construção passo a passo desta API está disponível gratuitamente na playlist do YouTube:
* https://www.youtube.com/playlist?list=PLpdOJd7P4_HsiLH8b5uyFAaaox4r547qe
*
* @author      Hélio Esperidião
* @copyright   Copyright (c) 2025 Hélio Esperidião
* @license     GPL (GNU General Public License)
* @website http://helioesperidiao.com
* @github https://github.com/helioesperidiao
* @linkedin https://www.linkedin.com/in/helioesperidiao/
* @youtube https://www.youtube.com/c/HélioEsperidião
* 
*/
require_once "api/src/routes/Roteador.php";

(new Roteador())->start();
