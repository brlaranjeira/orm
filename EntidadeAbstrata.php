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
     * @var
     */
    protected static $manyToMany;
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
        if (!$execOk) {
            $conexao->rollBack();
            return null;
        }
        foreach ($clazz::$manyToMany as $k => $v) {
            $getter = self::getGetter($clazz, $k);
            $arr = $this->$getter();
            $ids = array(); //os que nao estiverem aqui serao deletados
            foreach ($arr as $elm) {
                if (!$elm->save($conexao, false)) {
                    $conexao->rollBack();
                    return null;
                }
                $ids[] = $elm->getId();
                //update ou insert da tabela intermediaria
                $colunas = implode(',',array_keys($v['tbRelColumnsToAttrs']));
                $sql = 'SELECT ' . $colunas . ' FROM produto_tem_propriedade WHERE id_produto = ? and id_propriedade = ?';
                $queryConn = ConexaoBD::getConexao();
                $stmt = $queryConn->prepare($sql);
                $stmt->execute(array($this->id,$elm->getId()));
                $relValues = $stmt->fetchObject();
                if ($relValues) { //update, caso esteja diferente
                    $colunasDiferentes = '';
                    $valoresNovos = array();
                    foreach ( $v['tbRelColumnsToAttrs'] as $coluna => $atributo ) {
                        $getter = self::getGetter($v['clEntityName'],$atributo);
                        $attrValue = $elm->$getter();
                        $bdValue = $relValues->$coluna;
                        if ($attrValue != $bdValue) {
                            $colunasDiferentes .= strlen($colunasDiferentes) > 0 ? ' , ' : ' ';
                            $colunasDiferentes .= $coluna . ' = ? ';
                            $valoresNovos[] = $attrValue;
                        }
                    }
                    if ( sizeof($valoresNovos) > 0 ) {
                        $sql = 'UPDATE ' . $v['tbRelName'] . ' SET ' . $colunasDiferentes . ' WHERE ' . $v['tbRelCurrentId'] . ' = ? AND ' . $v['tbRelOtherId'] . ' = ?';
                        $valoresNovos[] = $this->id;
                        $valoresNovos[] = $elm->getId();
                        $stmt = $conexao->prepare($sql);
                        $inseriu = $stmt->execute($valoresNovos);
                        if (!$inseriu) {
                            $conexao->rollBack();
                            return null;
                        }
                    }
                } else {//insert
                    $map = $v['tbRelColumnsToAttrs'];
                    $sql = 'INSERT INTO ' . $v['tbRelName'] . ' (';
                        $primeiro = true;
                    $osvalores = array();
                    foreach ($map as $coluna => $atributo) {
                        $sql .= $primeiro ? $coluna : ' , ' . $coluna;
                        $getter = self::getGetter($v['clEntityName'],$atributo);
                        $osvalores[] = $elm->$getter();
                    }
                    $sql .= ' , ' . $v['tbRelCurrentId'] . ' , ' . $v['tbRelOtherId'] . ') VALUES ( ? ';
                    $sql .= str_repeat(' , ? ', sizeof($map) + 1) . ' )';
                    $osvalores[] = $this->id;
                    $osvalores[] = $elm->getId();

                    $stmt = $conexao->prepare($sql);
                    $execOk = $stmt->execute($osvalores);
                    echo ';';
                }

            }
            echo '';
        }




        if ($doCommit && !$conexao->commit() ) {
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
            $setter = self::getSetter( $clazz , $key );
            /*if (array_key_exists($key,$clazz::$setters)) {
                $setter = $clazz::$setters[$key];
            }*/
            $object->$setter($attrVal);
        }
        $setter = 'setId';
        $id = isset($row[$clazz::$idName]) ? $row[$clazz::$idName] : $row['id'];
        $object->$setter($id);
        foreach ($clazz::$manyToMany as $k => $v) {
            $sql = 'SELECT * FROM ' . $v['tbRelName'] . ' WHERE ' . $v['tbRelCurrentId'] . ' = ?';
            $statement = ConexaoBD::getConexao()->prepare($sql);
            $statement->execute(array($id));
            $linhas = $statement->fetchAll();
            require_once ($v['clEntityName'] . '.php');
            $objArray = array();
            foreach ($linhas as $linha) {
                $cls = $v['clEntityName'];
                $current = $cls::getById($linha[$v['tbRelOtherId']]);
                foreach ($v['tbRelColumnsToAttrs'] as $kk => $vv) {
                    $setter = self::getSetter($clazz,$vv);
                    $current->$setter($linha[$kk]);
                }
                $objArray[] = $current;
                echo 'a';
            }
            $setter = self::getSetter($clazz,$k);
            $object->$setter($objArray);
        }
        return $object;
    }

    private static function getSetter( $clazz, $pname ) {
        if (array_key_exists($pname,$clazz::$setters)) {
            return $clazz::$setters[$pname];
        }
        return 'set' . strtoupper($pname[0]) . substr($pname,1);
    }
    private static function getGetter( $clazz, $pname ) {
        if (array_key_exists($pname,$clazz::$getters)) {
            return $clazz::$getters[$pname];
        }
        return 'get' . strtoupper($pname[0]) . substr($pname,1);
    }

}