<?php
$sql = file_get_contents('c:/laragon/www/administrador-licencia-sistemamoda/bk_basededatos.sql');
$sql = str_replace('`licencias_db`', '`licencias_moda_db`', $sql);
file_put_contents('c:/laragon/www/sistema_moda/temp_bk.sql', $sql);

$conn = new mysqli('localhost', 'root', '');
$conn->query("CREATE DATABASE IF NOT EXISTS licencias_moda_db");
$conn->select_db("licencias_moda_db");

if ($conn->multi_query($sql)) {
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "DB Imported correctly.";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();

$c = new mysqli('localhost', 'root', '', 'licencias_moda_db');
$res = $c->query("SELECT email, password_hash FROM admin_licencias LIMIT 1");
if ($res) {
    while($r = $res->fetch_assoc()) { print_r($r); }
}
