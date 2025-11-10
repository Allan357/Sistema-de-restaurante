<?php

require_once '../classes/cardapioManager.php';

$manager = new CardapioManager();


    if ($_POST['action'] === 'add') {
        echo $manager->addItem($_POST['name'], $_POST['price'], $_POST['ingredients']);
    }
    elseif ($_POST['action'] === 'edit') {
        echo $manager->editItem($_POST['id'], $_POST['name'], $_POST['price'], $_POST['ingredients']);
    }
    elseif ($_POST['action'] === 'remove') {
        echo $manager->removeItem($_POST['id']);
    }
    $menu = $manager->getMenu();
    echo print_r($menu, true);
