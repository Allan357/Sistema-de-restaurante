<?php
class JsonStorage {
    private $file;

    public function __construct($file) {
        $this->file = __DIR__ . '/../data/' . $file;
    }

    public function read() {
        if (!file_exists($this->file)) {
            return [];
        }
        $content = file_get_contents($this->file);
        return json_decode($content, true) ?: [];
    }

    public function write($data) {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>