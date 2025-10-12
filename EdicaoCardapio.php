<?php
function editarMenu($id, $nome, $descricao, $valor){
    $jsonFile = 'cardapioTeste.json';
    if (empty($nome)|| empty($descricao)||$valor<=0){
        return ['aviso'=> 'Nome, descricao ou valor invalidos'];
    }
    $menuItems = json_decode(file_get_contents($jsonFile), true);
    if (isset($menuItems[$id])){
        $menuItems[$id]=[
            'nome'=>$nome,
            'descricao'=>$descricao,
            'valor'=> floatval($valor)
        ];
        file_put_contents($jsonFile, json_encode($menuItems, JSON_PRETTY_PRINT));
        return ['aviso'=>'Item atualizado com sucesso !'];
    }
    return['aviso'=> 'Item nao encontrado, tente novamente!'];
}
?>