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
require_once ("EntidadeX.php");


//$entidadex = EntidadeX::getByAttr('produto',2);
//var_dump($entidadex);

$coca = Produto::getById(1);
$gpc = new GPC();
$gpc->setGpc(substr(strval(time()),2,8));
$gpc->setDescricao('teste xyzw');
$coca->setGPC($gpc);
$x = $coca->save();

$json = $coca->asJSON();

echo '.';

//$seeds = array('http://cosmos.bluesoft.com.br/produtos/027084033953','http://cosmos.bluesoft.com.br/produtos/7891022853155');
//$ret = Crawler::crawlCosmos($seeds,1000,'barcodes_crawl.txt');
//echo implode(',',$ret);
