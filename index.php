<?php
/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 12/09/16
 * Time: 14:10
 */

require_once ("EntidadeAbstrata.php");
require_once ("GPC.php");
require_once ("NCM.php");
require_once ("Produto.php");
require_once ("Propriedade.php");


$todos = Propriedade::getAll();
var_dump($todos);

//$seeds = array('http://cosmos.bluesoft.com.br/produtos/027084033953','http://cosmos.bluesoft.com.br/produtos/7891022853155');
//$ret = Crawler::crawlCosmos($seeds,1000,'barcodes_crawl.txt');
//echo implode(',',$ret);
