<?php

namespace DgCrud\DgCrud;

trait CrudTrait
{
    /**
     * Insere uma nova linha e retorna o último ID
     *
     * @param  array $arrayData
     * @return int|null
     */
    public function create( array $arrayData )
    {
        try {
            foreach ($arrayData as $k => $v) {
                $columns[] = $k;
                $values[] = '?';
            }
    
            $count = 1;
            $columns = implode(', ', $columns);
            $values = implode(', ', $values);
    
            $stm = $this->pdo->prepare("INSERT INTO {$this->table_name} ({$columns}) VALUES ({$values})");
            foreach ($arrayData as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }
            
            $stm->execute();
            return $this->pdo->lastInsertId();
        
        } catch(\PDOException $ex) {
            $this->fail = $ex;
            return null;
        }
    }
    
    /**
     * Retorna uma ou mais linhas da tabela
     *
     * @param  string $sql
     * @param  array $arrayParams
     * @param  bool $fetchAll
     * @return object|null
     */
    public function read( string $sql, array $arrayParams = null, $fetchAll = true )
    {
        try {
            
            $stm = $this->pdo->prepare(sprintf($sql, $this->table_name));
            if ( !empty($arrayParams) ) {
                $count = 1;
                foreach ($arrayParams as $v) {
                    $stm->bindValue($count, $v);
                    $count++;
                }
            }
            
            $stm->execute();

            if (!$stm->rowCount()) {
                return null;
            }

            return (!$fetchAll ? $stm->fetch() : $stm->fetchAll());

        } catch(\PDOException $ex) {
            $this->fail = $ex;
            return null;
        }
    }
    
    /**
     * Atualiza uma ou mais linhas e retorna a quantidade afetada
     *
     * @param  array $arrayData
     * @param  array $arrayCondition
     * @return int|null
     */
    public function update( array $arrayData, array $arrayCondition )
    {
        try {
            foreach ($arrayData as $k => $v) {
                $news[] = $k;
            }
    
            foreach ($arrayCondition as $k => $v) {
                $conds[] = $k;
            }
    
            $count = 1;
            $news = implode("=?, ", $news) . "=?";
            $conds = implode("? AND ", $conds) . "?";
    
            $stm = $this->pdo->prepare("UPDATE {$this->table_name} SET {$news} WHERE {$conds}");
            foreach ($arrayData as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }
    
            foreach ($arrayCondition as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }

            $stm->execute();
            return ($stm->rowCount() ?? 1);

        } catch (\PDOException $ex) {
            $this->fail = $ex;
            return null;
        }
    }
    
    /**
     * Exclui uma ou mais linhas
     *
     * @param  array $arrayCondition
     * @return bool
     */
    public function delete( array $arrayCondition )
    {
        try {
            foreach ($arrayCondition as $k => $v) {
                $conds[] = $k;
            }
    
            $count = 1;
            $conds = implode("? AND ", $conds) . "?";
    
            $stm = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE {$conds}");
            foreach ($arrayCondition as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }

            $stm->execute();
            return true;

        } catch (\PDOException $ex) {
            $this->fail = $ex;
            return false;
        }
    }
}