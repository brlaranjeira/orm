<?php

/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 6/20/17
 * Time: 4:23 PM
 */
class GPC extends EntidadeAbstrata {

    protected static $dicionario = [
        "gpc" => "gpc",
        "descricao" => "descricao"
    ];
    protected static $tbName = 'gpc';

    /**
     * @var string
     */
    private $gpc;
    /**
     * @var string
     */
    private $descricao;



    /**
     * @return string
     */
    public function getGpc() {
        return $this->gpc;
    }

    /**
     * @param string $gpc
     */
    public function setGpc($gpc) {
        $this->gpc = $gpc;
    }

    /**
     * @return string
     */
    public function getDescricao() {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

}