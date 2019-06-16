<?php
    // error_reporting(0);
    //variables
    $params = '';

    if (!array_key_exists('path', $_GET)) {
        echo "Error.Path misssing.";
        exit;
    }
    
    //quebra o que vem na url na barra series/1
    $path = explode('/', $_GET['path']);
    
    if (count($path) == 0 || $path[0] == '')
    {
        echo "Error.Path misssing.";
        exit;
    }

    if(count($path) > 1)
    {
        $params = $path[1];
    }

    //obtem as informacoes do arquivo
    $contents = file_get_contents('db.json');

    $json = json_decode($contents, true);

    //obtem os tipos de requisicao da web
    $method = $_SERVER['REQUEST_METHOD'];
    
    // if ($method)
    // {
    //     // $obj["type"] = 1;
    //     $obj["method"] = $method;
    //     echo json_encode($obj);
    // }

    //verifica o tipo de conteudo da requisicao
    header('Content-type: application/json');
    
    //le o que tem no body
    $body = file_get_contents('php://input');
    
    function find_by_id ($vector, $params)
    {
        $encontrar = -1;
        foreach ($vector as $key => $obj) 
        {
            if($obj['id'] == $params)
            {   
                $encontrar = $key;
                break;
            }
        }
        return $encontrar;
    }

    if ($method === 'GET') 
    {
        if($json[$path[0]])
        {
            if ($params == '') 
            {
                echo json_encode($json[$path[0]]);
            } else {   
                $encontrar = find_by_id($json[$path[0]], $params);             
                if ($encontrar >= 0) 
                {
                echo json_encode($json[$path[0]][$encontrar]);
                } else {
                    echo "ERROR.";
                    exit;
                }
            }
        } else {
            echo '[]';
        }
    }

    if($method === "POST")
    {
        //body contem os dados post 
        $json_body = json_decode($body, true); //transforma o json em array
        $json_body['id'] = time(); //add o id ao array

        //verifica se tem informacoes no db.json 
        if (!$json[$path[0]]) 
        {
            //se nao tiver, seta array vazio
            $json[$path[0]] = [];
        }
        
        $json[$path[0]][] = $json_body; 
        echo json_encode($json_body);
        file_put_contents('db.json', json_encode($json));
    }

    if($method == "DELETE")
    {
        if($json[$path[0]])
        {
            if ($params == '') 
            {
                echo json_encode($json[$path[0]]);
            } else {   
                $encontrar = find_by_id($json[$path[0]], $params);
                if ($encontrar >= 0) 
                {
                    echo json_encode($json[$path[0]][$encontrar]);
                    unset($json[$path[0]][$encontrar]);
                    file_put_contents('db.json', json_encode($json));
                } else {
                    echo "ERROR.";
                    exit;
                }
            }
        } else {
            echo 'ERROR';
        }
    }

    if($method == "PUT")
    {
        if($json[$path[0]])
        {
            if ($params == '') 
            {
                echo json_encode($json[$path[0]]);
            } else {   
                $encontrar = find_by_id($json[$path[0]], $params);
                if ($encontrar >= 0) 
                {
                    $json_body = json_decode($body, true);
                    $json_body['id'] = $params; 
                    $json[$path[0]][$encontrar] = $json_body;
                    echo json_encode($json[$path[0]][$encontrar]);
                    file_put_contents('db.json', json_encode($json));
                } else {
                    echo "ERROR.";
                    exit;
                }
            }
        } else {
            echo 'ERROR';
        }
    }