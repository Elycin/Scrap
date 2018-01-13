<?php
/**
 * Created by PhpStorm.
 * User: elyci
 * Date: 1/12/2018
 * Time: 7:37 PM
 */

namespace App\Library;


class ClamAV
{
    private $socket_path;
    private $buffer_length = 1024;
    private $socket;
    private $character_prefix = "n";

    public function __construct($override_socket_path = '/var/run/clamav/clamd.ctl')
    {
        $this->socket_path = env('CLAMAV_SOCKET', $override_socket_path);
    }

    public function daemonIsListening()
    {
        return is_file($this->socket_path);
    }

    public function isFileVirus($file_path)
    {
        return $this->send("nSCAN " . trim($file_path));
    }

    public function __call($name, $arguments)
    {
        $pending_command = trim(sprintf(
                "%s%s %s",
                $this->character_prefix, strtoupper($name), $arguments[0]
            )) . "\n";
        return $this->send($pending_command);
    }

    private function connect()
    {
        try {
            return fsockopen("unix://" . $this->socket_path);
        } catch (\Exception $exception) {
            return $this->exceptionSocketDoesNotExist();
        }
    }

    public function setBufferLength($buffer_length)
    {
        $this->buffer_length = $buffer_length;
    }

    public function getBufferLength()
    {
        return $this->buffer_length;
    }

    private function send($query)
    {
        $this->socket = $this->connect();
        fwrite($this->socket, $query);
        $response = fread($this->socket, $this->buffer_length);
        return ($response != "UNKNOWN COMMAND\n") ? $response : new \Exception("ClamAV Daemon returned Unknown Command: " . $query);
    }

    private function exceptionSocketDoesNotExist()
    {
        return new \Exception(sprintf("IPC Socket File %s does not exist.", $this->socket_path));
    }


}