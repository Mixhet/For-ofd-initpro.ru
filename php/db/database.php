<?php

$conn = new mysqli('localhost', 'root', '', 'database_php');

if($conn->connect_error){
    die("Ошибка: " . $conn->connect_error);
}

// Создаем таблицы
function createTable()
{
    global $conn;

    $table_1 = 'CREATE TABLE procedures (
            id INTEGER AUTO_INCREMENT PRIMARY KEY, 
            procedure_number VARCHAR(30) NOT NULL, 
            oos_procedure_number VARCHAR(30) NOT NULL,
            link_procedure VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL
            );';

    $table_2 = 'CREATE TABLE attachment ( 
            id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            link text NOT NULL,
            procedure_id int NOT NULL,
            FOREIGN KEY (procedure_id) REFERENCES procedures(id)
            );';

    if($conn->query($table_1)){
        // echo "Таблица procedures успешно создана<br>";
    } else{
        deleteAll();
    }

    if($conn->query($table_2)){
        // echo "Таблица attachment успешно создана<br>";
    } else{
        deleteAll();
    }
}

//Добавляем данные процедур
function insertProcedures($procedure_number, $oos_procedure_number, $link_procedure, $email)
{
    global $conn;

    $sql = 'INSERT INTO procedures (procedure_number, oos_procedure_number, link_procedure, email) VALUES (?, ?, ?, ?);';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $procedure_number, $oos_procedure_number, $link_procedure, $email);
    if($stmt->execute()){
        // echo "Данные успешно добавлены procedures";
    } else{
        echo "Ошибка: " . $conn->error;
    }
    $stmt->close();
}

//Добавляем данные документов
function insertAttachment($title, $link, $procedure_id)
{
    global $conn;

    $sql = 'INSERT INTO attachment (title, link, procedure_id) VALUES (?, ?, ?);';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $title, $link, $procedure_id);
    if($stmt->execute()){
        // echo "Данные успешно добавлены attachment";
    } else{
        echo "Ошибка: " . $conn->error;
    }
    $stmt->close();
}

//Получение id процедуры
function getId($procedure_number)
{
    global $conn;

    $sql = 'SELECT id FROM procedures WHERE procedure_number=' . $procedure_number . ';';

    $result = $conn->query($sql);

    foreach($result as $row){
        return $row['id'];
    }
}

//Очистить базы
function deleteAll()
{
    global $conn;

    $sql_1 = 'DELETE FROM attachment;';
    $sql_2 = 'DELETE FROM procedures;'; 

    $conn->query($sql_1);
    $conn->query($sql_2);
}

//Достаем данные из Процедур
function selectProcedures()
{
    global $conn;

    $sql = 'SELECT * FROM procedures;';

    $rows = $conn->query($sql);

    $result = [];
    foreach($rows as $row)
    {   
        $result[] = $row;
    }

    return $result;
}

//Достаем данные из Документов
function selectAttachment($procedure_id)
{
    global $conn;

    $sql = 'SELECT * FROM attachment WHERE procedure_id=' . $procedure_id . ';';

    $rows = $conn->query($sql);

    $result = [];
    foreach($rows as $row)
    {   
        $result[] = $row; 
    }

    return $result;
}
