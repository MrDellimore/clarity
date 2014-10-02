<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/2/14
 * Time: 12:02 PM
 */

namespace Api\Magento\Model;


class Ssh2CronTabManager {

    private $connection;
    private $path;
    private $handle;
    private $cronFile;

    public function __construct($host=NULL, $port=NULL, $username=NULL, $password=NULL)
    {
        $path_length = strrpos(__FILE__, "/");
        $this->path  = substr(__FILE__, 0, $path_length) . '/';
        $this->handle    = 'crontab.txt';
        $this->cronFile = "{$this->path}{$this->handle}";
        try {
            if ( is_null($host) || is_null($port) || is_null($username) || is_null($password) ) {
                throw new \Exception("Please specify the host, port, username and password!");
            }

            $this->connection = @ssh2_connect($host, $port);
            if ( ! $this->connection ) {
                throw new \Exception("The SSH2 connection could not be established.");
            }
            $authentication = @ssh2_auth_password($this->connection, $username, $password);
            if ( ! $authentication ) {
                throw new \Exception("Could not authenticate '{$username}' using password: '{$password}'.");
            }
        }
        catch ( \Exception $e ) {
            $this->error_message($e->getMessage());
        }
    }

    public function exec()
    {
        $argument_count = func_num_args();

        try {
            if ( ! $argument_count ) {
                throw new \Exception("There is nothing to execute, no arguments specified.");
            }
        }
        catch{

        }
    }

    public function write_to_file() {}

    public function remove_file() {}

    public function append_cronjob() {}

    public function remove_cronjob() {}

    public function remove_crontab() {}

    private function crontab_file_exists() {}

    private function error_message() {}
} 