<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/2/14
 * Time: 12:02 PM
 */

namespace Api\Magento\Model;


class Ssh2CronTabManager {

//    private $connection;
    private $path;
    private $handle;
    private $cronFile;
    private $resource;

    public function __construct()//$host=NULL, $port=NULL, $username=NULL, $password=NULL
    {
        $path_length = strrpos(__FILE__, "/");
        $this->path  = substr(__FILE__, 0, $path_length) . '/';
        $this->handle    = 'crontab.txt';
        $this->cronFile = "{$this->path}{$this->handle}";
        $this->crontab_file_exists();
//        $this->resource = fopen($this->cronFile, 'x+');
//        fopen($this->cronFile, 'x+');

//        echo __FILE__ . ' ' . $path_length . ' ' . $this->path . ' ' . $this->cronFile . "\n";
    }

    public function exec()
    {
        $argument_count = func_num_args();

        try {
            if ( ! $argument_count ) {
                throw new \Exception("There is nothing to execute, no arguments specified.");
            }
            $arguments = func_get_args();

            $command_string = ($argument_count > 1) ? implode(" && ", $arguments) : $arguments[0];
            system($command_string);
//            echo $command_string;
//            $stream = @ssh2_exec($this->connection, $command_string);
//            $stream = shell_exec($command_string);
//            if ( ! $stream ) {
//                throw new \Exception("Unable to execute the specified commands: <br />{$command_string}");
//            }
        }
        catch ( \Exception $e ) {
            $this->error_message($e->getMessage());
        }
        return $this;
    }

    public function write_to_file($path=NULL, $handle=NULL)
    {
//        var_dump($this->crontab_file_exists());
        if ( ! $this->crontab_file_exists() )
        {
            $this->handle = (is_null($handle)) ? $this->handle : $handle;
            $this->path   = (is_null($path))   ? $this->path   : $path;

            $this->cronFile = "{$this->path}{$this->handle}";
            $init_cron = "crontab -l > {$this->cronFile} && [ -f {$this->cronFile} ] || > {$this->cronFile}";
            $this->exec($init_cron);
        }
        return $this;
    }

    public function remove_file()
    {
        if ( $this->crontab_file_exists() ) {
            $this->exec("rm {$this->cronFile}");
        }
        return $this;
    }

    public function append_cronjob($cron_jobs = Null)
    {
        if ( is_null($cron_jobs) ) {
            $this->error_message("Nothing to append!  Please specify a cron job or an array of cron jobs.");
        }

        $append_cronfile = "echo '";

        $append_cronfile .= (is_array($cron_jobs)) ? implode("\n", $cron_jobs) : $cron_jobs;

//        fwrite($this->resource, $cron_jobs);

        $append_cronfile .= "'  >> {$this->cronFile}";
        $install_cron = "crontab {$this->cronFile}";

        $this->write_to_file()->exec($append_cronfile, $install_cron)->remove_file();
        return $this;
    }

    public function remove_cronjob($cron_jobs = Null)
    {
        if ( is_null($cron_jobs) ) {
            $this->error_message("Nothing to remove!  Please specify a cron job or an array of cron jobs.");
        }

        $this->write_to_file();
        $cron_array = file($this->cronFile, FILE_IGNORE_NEW_LINES);

        if ( empty($cron_array) ) {
            $this->error_message("Nothing to remove!  The cronTab is already empty.");
        }
        $original_count = count($cron_array);
        if ( is_array($cron_jobs) ) {
            foreach ( $cron_jobs as $cron_regex ) {
                $cron_array = preg_grep($cron_regex, $cron_array, PREG_GREP_INVERT);
            }
        } else {
            $cron_array = preg_grep($cron_jobs, $cron_array, PREG_GREP_INVERT);
        }
        return ($original_count === count($cron_array)) ? $this->remove_file() : $this->remove_crontab()->append_cronjob($cron_array);
    }

    public function remove_crontab()
    {
        $this->exec("crontab -r")->remove_file();

        return $this;
    }

    private function crontab_file_exists()
    {

//        if ( file_exists($this->cronFile) ) {
//            return true;
//        } else {
//            system('touch '. $this->cronFile);
//        }
        return file_exists($this->cronFile);
    }

    private function error_message($error)
    {
        die("<pre style='color:#EE2711'>ERROR: {$error}</pre>");
    }
}

//$crontab = new Ssh2CronTabManager('11.11.111.111', '22', 'my_username', 'my_password');
//$crontab->append_cronjob('30 8 * * 6 home/path/to/command/the_command.sh >/dev/null 2>&1');
//
//$new_cronjobs = array(
//    '0 0 1 * * home/path/to/command/the_command.sh',
//    '30 8 * * 6 home/path/to/command/the_command.sh >/dev/null 2>&1'
//);
//
//$crontab->append_cronjob($new_cronjobs);
