<?php

namespace Dougl\DataLayer;

use PDO;
use PDOException;

class DatabaseFactory 
{    
    /**
     * Instância PDO
     * 
     * @var PDO
     */
    protected static $pdo;

    /**
     * Erro de conexão
     * 
     * @var PDOException
     */
    protected static $error;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        //
    }
    
    /**
     * Retorna uma conexão PDO
     *
     * @return PDO|String
     */
    public static function getInstance()
    {
        try {
            
            if ( !isset(self::$pdo) ) {
                self::$pdo = new PDO(
                    DB_CONFIG["driver"] . ":host=" . DB_CONFIG["host"] . ";dbname=" . DB_CONFIG["db_name"] . ";port=" . DB_CONFIG["port"],
                    DB_CONFIG["db_user"],
                    DB_CONFIG["db_passwd"],
                    [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ]
                );
            }
            return self::$pdo;

        } catch(PDOException $ex) {
            self::$error = $ex;
        }
    }
    
    /**
     * Retorna o erro de conexão
     *
     * @return PDOException|null
     */
    public static function getError()
    {
        return self::$error;
    }
}