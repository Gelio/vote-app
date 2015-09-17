<?php

class OutputHandler
{
    protected $file;

    public function __construct($filename = "output.txt") {
        $this->file = fopen($filename, "w");
    }

    public function write($data) {
        if(is_array($data))
            $data = print_r($data, true);

        fwrite($this->file, $data."\n");
    }

    function __destruct() {
        fclose($this->file);
    }
}
?>
