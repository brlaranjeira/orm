<?php

/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 6/20/17
 * Time: 6:19 PM
 */
class NCM extends EntidadeAbstrata {
    protected static $tbName = 'ncm';
    protected static $dicionario = [
        "ncm" => 'ncm',
        "desc" => 'descricao',
        "descFull" => 'descricao_full'
    ];

    private $ncm;
    private $desc;
    private $descFull;

    /**
     * @return mixed
     */
    public function getNcm()
    {
        return $this->ncm;
    }

    /**
     * @param mixed $ncm
     */
    public function setNcm($ncm)
    {
        $this->ncm = $ncm;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getDescFull()
    {
        return $this->descFull;
    }

    /**
     * @param mixed $descFull
     */
    public function setDescFull($descFull)
    {
        $this->descFull = $descFull;
    }



}