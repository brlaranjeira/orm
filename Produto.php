<?php

/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 6/20/17
 * Time: 6:34 PM
 */
class Produto extends EntidadeAbstrata {

    protected static $tbName = 'produto';

    protected static $dicionario = [
        "descricao" => "descricao",
        "gpc" => "id_gpc",
        "ncm" => "id_ncm"
    ];

    protected static $manyToMany = [
        'propriedades' => [
            "tbRelName" => "produto_tem_propriedade",
            "tbRelCurrentId" => "id_produto",
            "tbRelOtherId" => "id_propriedade",
            "tbRelColumnsToAttrs" => [
                "valor" => 'value'
            ],
            "clEntityName" => "Propriedade"
        ]
    ];


    private $descricao;
    private $gpc;
    private $ncm;
    private $propriedades;

    /**
     * @return mixed
     */
    public function getDescricao() {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    /**
     * @return mixed
     */
    public function getGpc() {
        return $this->gpc;
    }

    /**
     * @param mixed $gpc
     */
    public function setGpc($gpc) {
        require_once ("GPC.php");
        $this->gpc = is_object($gpc) ? $gpc : GPC::getById($gpc);
    }

    /**
     * @return mixed
     */
    public function getNcm() {
        return $this->ncm;
    }

    /**
     * @param mixed $ncm
     */
    public function setNcm($ncm) {
        require_once ("NCM.php");
        $this->ncm = is_object($ncm) ? $ncm : NCM::getById($ncm);
    }

    /**
     * @return Propriedade[]
     */
    public function getPropriedades() {
        return $this->propriedades;
    }

    /**
     * @param $propriedades Propriedade[]
     */
    public function setPropriedades( $propriedades ) {
        $this->propriedades = $propriedades;
    }

    /**
     * @param $propriedade Propriedade
     */
    public function addPropriedade( $propriedade ) {
        $this->propriedades[] = $propriedade;
    }


}