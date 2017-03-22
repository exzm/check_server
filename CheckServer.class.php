<?php

class System
{
    protected function exec($command)
    {
        return exec($command);
    }
}

final class CheckServer extends System
{
    private $maxLoadAverage = 50;
    private $minFreeDisk = 50;
    private $result = [];

    public function setEmail($email)
    {
        $this->adminEmail = $email;
    }

    public function setMinFreeDisk($gb)
    {
        $this->minFreeDisk = $gb;
    }

    public function setLoadAverage($load)
    {
        $this->maxLoadAverage = $load;
    }

    private function chechApache()
    {
        return $this->exec('ps -A|grep apache2|wc -l');
    }

    private function chechNginx()
    {
        return $this->exec('ps -A|grep nginx|wc -l');
    }

    private function checkMysql()
    {
        return $this->exec('ps -A|grep mysql|wc -l');
    }

    private function checkLoad()
    {
        return $this->exec("uptime | grep -o 'load average.*' | cut -c 15-18");
    }

    private function getFreeDisk($disk = '$PWD')
    {
        return round($this->exec('df ' . $disk . ' | awk \'/[0-9]%/{print $(NF-2)}\'') / 1024 / 1024);
    }

    public function run()
    {

        if (!$this->chechApache()) {
            $this->result[] = 'При проверке обнаружено, что веб-сервер Apache не был запущен!';
        }

        if (!$this->chechNginx()) {
            $this->result[] = 'При проверке обнаружено, что веб-сервер Nginx не был запущен!';
        }

        if (!$this->checkMysql()) {
            $this->result[] = 'При проверке обнаружено, что MySQL-сервер не был запущен!';
        }

        if ($this->checkLoad() > $this->maxLoadAverage) {
            $this->result[] = "ВНИМАНИЕ!!! Слишком большая нагрузка! {$this->checkLoad()}";
        }

        if ($this->getFreeDisk() < $this->minFreeDisk) {
            $this->result[] = "Свободное место на диске {$this->getFreeDisk()} GB";
        }

        if ($this->getFreeDisk('/var/lib/mysql') < $this->minFreeDisk) {
            $this->result[] = "Свободное место на диске MYSQL {$this->getFreeDisk('/var/lib/mysql')} GB";
        }

        return $this->result;
    }

}