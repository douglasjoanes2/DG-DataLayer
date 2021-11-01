<?php

namespace DgCrud\DgCrud;

use PDOException;

class DataLayer
{    
    use CrudTrait;

    /**
     * Instancia da classe PDO
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO exception
     *
     * @var PDOException
     */
    protected $fail;
    
    /**
     * Nome da tabela
     *
     * @var string
     */
    protected $table_name;

    /**
     * Chave primária
     *
     * @var string
     */
    protected $primary_key;
    
    /**
     * Colunas da tabela
     *
     * @var object
     */
    protected $data;
    
    /**
     * Campos obrigatórios
     *
     * @var array
     */
    protected $required;
    
    /**
     * Timestamp
     *
     * @var bool
     */
    protected $timestamp;
    
    /**
     * __construct
     *
     * @param  string $table_name
     * @param  array $required
     * @param  string $primary_key
     * @param  bool $timestamp
     * @return void
     */
    public function __construct( string $table_name = '', array $required = [], string $primary_key = 'id', bool $timestamp = true )
    {
        $this->table_name  = $table_name;
        $this->required    = $required;
        $this->primary_key = $primary_key;
        $this->timestamp   = $timestamp;
        $this->pdo         = DatabaseFactory::getInstance();
    }

    /**
     * Define o nome da tabela
     *
     * @param  string $table_name
     * @return void
     */
    public function setTableName( string $table_name )
    {
        $this->table_name = $table_name;
    }
    
    /**
     * Retona o nome da tabela 
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }
    
    /**
     * Define o valor de uma determinada coluna
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set( $name, $value )
    {
        if ( empty($this->data) ) {
            $this->data = new \stdClass();
        }
        $this->data->$name = $value;
    }
    
    /**
     * Retorna o valor de uma determinada coluna
     *
     * @param  mixed $name
     * @return void
     */
    public function __get( $name )
    {
        return ($this->data->$name ?? null);
    }
    
    /**
     * Retorna todas as linhas da tabela
     *
     * @return object|null
     */
    public function findAll()
    {
        $res = $this->read("SELECT * FROM %s");
        if ( !$res ) {
            return '';
        }
        return $res;
    }
    
    /**
     * Retorna uma linha da tabela baseado na sua chave primária
     *
     * @return object|null
     */
    public function findByPrimaryKey()
    {
        $primary = $this->primary_key;
        $res     = $this->read("SELECT * FROM %s WHERE {$this->primary_key}=?", [$this->data->$primary], false);
        if ( !$res ) {
            return '';
        }
        return $res;
    }

    /**
     * Insere ou atualiza uma ou mais linhas
     * 
     * @return bool
     */
    public function save()
    {
        $primary = $this->primary_key;
        $id      = null;

        try {
            
            /** Verifica os campos obrigatórios */
            if ( !$this->required() ) {
                throw new \Exception('Preencha os campos necessários.');
            }

            $date_now = (new \DateTime())->format('Y-m-d H:i:s');

            /** Se for um update */
            if ( !empty($this->data->$primary) ) {
                $this->data->updated_at = $date_now;
                $id = $this->data->$primary;
                $this->update($this->safe(), ["{$this->primary_key}=" => $id]);
            }

            /** Se for um create */
            if ( empty($this->data->$primary) ) {
                $this->data->created_at = $date_now;
                $this->data->updated_at = $date_now;
                $id = $this->create($this->safe());
            }

            if ( !$id ) {
                return false;
            }

            $this->data = $this->findByPrimaryKey($id);
            return true;

        } catch(\Exception $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * Prepara os dados para serem salvos
     *
     * @return array|null
     */
    protected function safe()
    {
        $safe = (array)$this->data;
        unset($safe[$this->primary_key]);
        return $safe;
    }
    
    /**
     * Valida os campos obrigatórios
     *
     * @return bool
     */
    protected function required()
    {
        $data = (array)$this->data;
        foreach ($this->required as $field) {
            if ( empty($data[$field]) ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Deleta um registro da tabela.
     * 
     * É nescessário informar a chave primária.
     *
     * @return bool
     */
    public function destroy()
    {
        $primary = $this->primary_key;
        $id      = $this->data->$primary;

        if ( empty($id) ) {
            return false;
        }
        return $this->delete(["{$this->primary_key}=" => $id]);
    }
}