<?php
require_once 'jsonStorage.php';
require_once 'prato.php';

class CardapioManager {
    private $storage;

    public function __construct() {
        $this->storage = new JsonStorage('data/cardapio.json');
    }

    // Incluir item (fluxo principal)
    public function addItem($name, $price, $ingredients) {
        $menu = $this->storage->read();
        // Verifica duplicado (fluxo secundário)
        foreach ($menu as $item) {
            if (strtolower($item['name']) === strtolower($name)) {
                throw new Exception("Item duplicado: '$name' já existe no cardápio.");
            }
        }
        // Gera ID único
        $id = count($menu) + 1;
        $ingredientsArray = array_map('trim', explode(',', $ingredients));
        $newItem = new Prato($id, $name, (float)$price, $ingredientsArray);
        $menu[] = $newItem->toArray();
        $this->storage->write($menu);
        return "Item '$name' adicionado com sucesso.";
    }

    // Editar item
    public function editItem($id, $name, $price, $ingredients) {
        $menu = $this->storage->read();
        foreach ($menu as &$item) {
            if ($item['id'] == $id) {
                $item['name'] = $name;
                $item['price'] = $price;
                $item['ingredients'] = $ingredients;
                $this->storage->write($menu);
                return "Item ID $id editado com sucesso.";
            }
        }
        throw new Exception("Item ID $id não encontrado.");
    }

    // Remover item
    public function removeItem($id) {
        $menu = $this->storage->read();
        foreach ($menu as $key => $item) {
            if ($item['id'] == $id) {
                unset($menu[$key]);
                $this->storage->write(array_values($menu)); // Reindexa array
                return "Item ID $id removido com sucesso.";
            }
        }
        throw new Exception("Item ID $id não encontrado.");
    }

    // Exibir cardápio atualizado
    public function getMenu() {
        return $this->storage->read();
    }
}