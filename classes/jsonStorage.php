<?php

class JsonStorage {

    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([])); // Cria arquivo vazio se não existir
        }
    }

    public function read() {
        $content = file_get_contents($this->filePath);
        return json_decode($content, true) ?: [];
    }

    public function write($data) {
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
