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
        "descricao" => "descricao"
    ];

    protected static $manyToMany = [
        'propriedades' => [
            "tbRelName" => "produto_tem_propriedade",
            "tbRelCurrentId" => "id_produto",
            "tbRelOtherId" => "id_propriedade",
            "tbRelDicionario" => [
                "valor" => 'value'
            ],
            "clEntityName" => "Propriedade"
        ]
    ];

    protected static $hasMany = [
        'entidadesX' => [
            'clEntityName' => 'EntidadeX',
            'clCurrentId' => 'produto'
        ]
    ];

    protected static $hasOne = [
        'gpc' => [
            'clEntityName' => 'GPC',
            'tbForeignKey' => 'id_gpc'
        ], 'ncm' => [
            'clEntityName' => 'NCM',
            'tbForeignKey' => 'id_ncm'
        ]
    ];



    private $descricao;
    private $gpc;
    private $ncm;
    private $propriedades;
    private $entidadesX;

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
        //require_once ("GPC.php");
        //$this->gpc = is_object($gpc) ? $gpc : GPC::getById($gpc);
        $this->gpc = $gpc;
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
        //require_once ("NCM.php");
        //$this->ncm = is_object($ncm) ? $ncm : NCM::getById($ncm);
        $this->ncm = $ncm;
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

    /**
     * @return mixed
     */
    public function getEntidadesX() {
        return $this->entidadesX;
    }

    /**
     * @param mixed $entidadesX
     */
    public function setEntidadesX($entidadesX) {
        $this->entidadesX = $entidadesX;
    }




}