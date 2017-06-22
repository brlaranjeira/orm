<?php

/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 6/22/17
 * Time: 12:33 PM
 */
class EntidadeX extends EntidadeAbstrata {

    protected static $tbName = 'entidadex';
    protected static $dicionario = [
        'attr1' => 'col1',
        'attr2' => 'col2',
        'attr3' => 'col3',
        'attr4' => 'col4',
        'produto' => 'id_produto'
    ];
    protected static $idName = 'identificador';

    private $attr1;
    private $attr2;
    private $attr3;
    private $attr4;
    private $produto;

    /**
     * @return mixed
     */
    public function getAttr1()
    {
        return $this->attr1;
    }

    /**
     * @param mixed $attr1
     */
    public function setAttr1($attr1)
    {
        $this->attr1 = $attr1;
    }

    /**
     * @return mixed
     */
    public function getAttr2()
    {
        return $this->attr2;
    }

    /**
     * @param mixed $attr2
     */
    public function setAttr2($attr2)
    {
        $this->attr2 = $attr2;
    }

    /**
     * @return mixed
     */
    public function getAttr3()
    {
        return $this->attr3;
    }

    /**
     * @param mixed $attr3
     */
    public function setAttr3($attr3)
    {
        $this->attr3 = $attr3;
    }

    /**
     * @return mixed
     */
    public function getAttr4()
    {
        return $this->attr4;
    }

    /**
     * @param mixed $attr4
     */
    public function setAttr4($attr4)
    {
        $this->attr4 = $attr4;
    }

    /**
     * @return mixed
     */
    public function getProduto()
    {
        return $this->produto;
    }

    /**
     * @param mixed $produto
     */
    public function setProduto($produto) {
        $this->produto = $produto;
    }



}