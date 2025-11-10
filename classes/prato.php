<?php

class Prato {
    public $id;
    public $name;
    public $price;
    public $ingredients;

    public $removed = false;
    public function __construct($id, $name, $price, $ingredients) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->ingredients = $ingredients;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'ingredients' => $this->ingredients,
            'removed' => $this->removed
        ];
    }
}