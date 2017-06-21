<?php

/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 6/20/17
 * Time: 6:54 PM
 */
class Propriedade extends EntidadeAbstrata {
    protected static $tbName = 'propriedade';
    protected static $dicionario = [
        "desc" => 'descricao'
    ];

    private $desc;
    private $value;

    /**
     * @return mixed
     */
    public function getDesc() {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc) {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }




}