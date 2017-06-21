<?php

/**
 * Created by PhpStorm.
 * User: Camila
 * Date: 05/12/2016
 * Time: 16:51
 */
abstract class EntidadeAbstrata {

    const BLACK_LIST = 0;
    const WHITE_LIST = 1;
    /**
     * @var
     */
    protected static $dicionario;
    /**
     * @var array nomes dos metodos getters para os objetos
     */
    protected static $getters;

    /**
     * @var array nomes dos metodos setters para os objetos
     */
    protected static $setters;
    /**
     * @var string nome da coluna do id da tabela
     */
    protected static $idName;
    /**
     * @var integer valor do id do elemento
     */
    protected $id;
    /**
     * @var string nome da tabela correspondente
     */
    protected static $tbName;



    public function save( $conexao=null , $doCommit=true , $attrList=array() , $listType=self::BLACK_LIST ) {
        require_once ("ConexaoBD.php");
        $clazz = get_called_class();
        if ($listType==self::BLACK_LIST) {
            $subDicionario = $clazz::$dicionario;
            foreach ($attrList as $attr ) {
                unset($subDicionario[$attr]);
            }
        } else {
            foreach ($attrList as $attr) {
                $subDicionario[$attr] = $clazz::$dicionario[$attr];
            }
        }

        if (!isset($this->id)) { //vai inserir, pois nao tem id ainda
            $sql = 'INSERT INTO ' . $clazz::$tbName . ' (' . implode(',',array_values($subDicionario)) . ') VALUES ( ';
            for ($i=0;$i<sizeof($subDicionario);$i++) {
                $sql .= $i == 0 ? ' ? ' : ', ? ';
            }
            $sql .= ' )';
        } else { //vai atualizar, pois ja tem id
            $sql = 'UPDATE ' . $clazz::$tbName . ' SET ';
            $colunas = array_values($subDicionario);
            for ($i=0; $i < sizeof($subDicionario); $i++) {
                $sql .= $i == 0 ? ' ' : ' , ';
                $sql .= $colunas[$i] . ' = ? ';
            }
            $sql .= 'WHERE ';
            $sql .= isset($clazz::$idName) ? $clazz::$idName : 'id';
            $sql .= ' = ? ';
        }
        $osvalores = array();
        foreach ($subDicionario as $attr => $col) {
            $metodo = 'get' . strtoupper($attr[0]) . substr($attr,1);
            $valor = $this->$metodo();
            if (is_object($valor)) {
                $getter = $clazz::$getters[$attr];
                if (!isset($getter)) {
                    $getter = 'getId';
                }
                $valor = $valor->$getter();
            }
            $osvalores[] = $valor;
        }
        if (isset($this->id)) {
            $osvalores[] = $this->id;
        }
        $conexao = isset($conexao) ? $conexao : ConexaoBD::getConexao();
        if (!$conexao->inTransaction()) {
            $conexao->beginTransaction();
        }
        $statement = $conexao->prepare($sql);
        $execOk = $statement->execute($osvalores);
        $idInserido = isset($this->id) ? $this->id : $conexao->lastInsertId();
        if (!$execOk || ( $doCommit && !$conexao->commit() ) ) { //erro se nao executou ou se precisa fazer commit e falhou
            $conexao->rollBack();
            return null;
        }
        $ret = isset($this->id) ? true : $idInserido;
        $this->id = $idInserido;
        return $ret;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return EntidadeAbstrata[]
     */
    public static function getAll() {
        require_once("ConexaoBD.php");
        $clazz = get_called_class();
        $sql = 'SELECT * from ' . $clazz::$tbName;
        $statement = ConexaoBD::getConexao()->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll();
        $objects = array();
        foreach ($rows as $row) {
            $objects[] = self::rowToObject( $row, $clazz );
        }
        return $objects;
    }

    public static function getById( $id ) {
        $ret = self::getByAttr('id' , $id );
        return $ret[0];
    }


    public static function getByAttr($attrs , $values, $operators = '=' ) {
        require_once("ConexaoBD.php");
        $operators = is_array($operators) ? $operators : array($operators);
        $clazz = get_called_class();
        $sql = 'SELECT * from ' . $clazz::$tbName;
        $attrs = is_array($attrs) ? $attrs : array($attrs);
        $values = is_array($values) ? $values : array($values);
        $len = min(sizeof($attrs),sizeof($values));
        for ( $i = 0; $i < $len; $i ++ ) {
            $colName = $attrs[$i] != 'id' ? $clazz::$dicionario[$attrs[$i]] : isset($clazz::$idName) ? $clazz::$idName : 'id';
            $op = isset($operators[$i]) ? $operators[$i] : '=';
            $op = (isset($operators) && isset($operators[$i])) ? $operators[$i] : '=';
            $sql .= $i != 0 ? ' AND ' : ' WHERE ';
            $sql .= $colName . ' ' . $op . ' ? ';
        }
        $statement = ConexaoBD::getConexao()->prepare($sql);
        $statement->execute(array_slice($values,0,$len));
        $rows = $statement->fetchAll();
        $objects = array();
        foreach ($rows as $row) {
            $objects[] = self::rowToObject( $row, $clazz );
        }
        return $objects;
    }

    private static function rowToObject( $row, $clazz ) {
        $object = new $clazz();
        foreach ( $clazz::$dicionario as $key => $value ) {
            $attrVal = $row[$value];
            $setter = 'set' . strtoupper($key[0]) . substr($key,1);
            if (array_key_exists($key,$clazz::$setters)) {
                $setter = $clazz::$setters[$key];
            }
            $object->$setter($attrVal);
        }
        $setter = 'setId';
        $object->$setter(isset($row[$clazz::$idName]) ? $row[$clazz::$idName] : $row['id']);
        return $object;
    }

}