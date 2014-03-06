<?php

namespace Bee\Utils;

use PDO;
use Exception;

class HashManager {

    /**
     * @var HashManager
     */
    private static $instance;

    /**
     * @var PDO
     */
    private $pdoConnection;

	/**
	 * @static
	 * @param \PDO $pdoConnection
	 * @return HashManager
	 */
    public static function getInstance(PDO $pdoConnection=null) {
        if (is_null(self::$instance)) {
            self::$instance = new HashManager($pdoConnection);
        }
        return self::$instance;
    }

    private function __construct(PDO $pdoConnection) {
        $this->pdoConnection = $pdoConnection;
    }

    public function getHash($id, $group=null) {
        try {
            $stmt = 'SELECT * FROM `bee_hashes` WHERE `id` = :id';
            $params = array();
            $params['id'] = $id;
            if (!is_null($group)) {
                $stmt .= ' AND `group` = :grp';
                $params['grp'] = $group;
            } else {
                $stmt .= ' AND `group` IS NULL';
            }

            $stmt = $this->pdoConnection->prepare($stmt);
            $stmt->execute($params);
            if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception('hash not found');
            }

            if (!array_key_exists('hash', $row)) {
                throw new Exception('row incomplete');
            }
            return $row['hash'];

        } catch (Exception $e) {
            $hash = $this->createHash();
            if ($this->persistHash($this->createHash(), $id, $group)) {
                return $hash;
            }
            return false;
        }
    }

    public function getId($hash, $group=null) {
        try {
            $stmt = 'SELECT * FROM `bee_hashes` WHERE `hash` = :hash';
            $params = array();
            $params['hash'] = $hash;
            if (!is_null($group)) {
                $stmt .= ' AND `group` = :grp';
                $params['grp'] = $group;
            } else {
                $stmt .= ' AND `group` IS NULL';
            }

            $stmt = $this->pdoConnection->prepare($stmt);
            $stmt->execute($params);
            if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception('hash not found');
            }

            if (!array_key_exists('id', $row)) {
                return false;
            }
            return $row['id'];

        } catch (Exception $e) {
            return false;
        }
    }

    private function createHash() {
        return preg_replace('/\./', 'b', uniqid('', true));
    }

    private function persistHash($hash, $id, $group=null) {
        try {
            $this->getPdoConnection()->beginTransaction();

            $fields = '(`id`, `hash`';
            $values = '(:id, :hash';
            $params = array();
            $params['id'] = $id;
            $params['hash'] = $hash;
            if (!is_null($group)) {
                $fields .= ', `group`';
                $values .= ', :grp';
                $params['grp'] = $group;
            }
            $fields .= ')';
            $values .= ')';

            $stmt = 'INSERT INTO `bee_hashes` '.$fields.' VALUES '.$values;

            $stmt = $this->pdoConnection->prepare($stmt);

            if (!$stmt->execute($params)) {
                throw new Exception('some exception');
            }

            $this->getPdoConnection()->commit();
            return true;


        } catch (Exception $e) {
            $this->getPdoConnection()->rollback();
            return false;
        }
    }

    /**
     * @param PDO $pdoConnection
     */
    public function setPdoConnection($pdoConnection) {
        $this->pdoConnection = $pdoConnection;
    }

    /**
     * @return PDO
     */
    public function getPdoConnection() {
        return $this->pdoConnection;
    }
}