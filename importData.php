<?php
include_once 'dbConfig.php';
if(isset($_POST['importSubmit'])){
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            fgetcsv($csvFile);
            while(($line = fgetcsv($csvFile)) !== FALSE){
                $name   = $line[0];
                $email  = $line[1];
                $phone  = $line[2];
                $status = $line[3];
                $prevQuery = "SELECT id FROM members WHERE email = '".$line[1]."'";
                $prevResult = $db->query($prevQuery);
                
                if($prevResult->num_rows > 0){
                    $db->query("UPDATE members SET name = '".$name."', phone = '".$phone."', status = '".$status."', modified = NOW() WHERE email = '".$email."'");
                }else{
                    $db->query("INSERT INTO members (name, email, phone, created, modified, status) VALUES ('".$name."', '".$email."', '".$phone."', NOW(), NOW(), '".$status."')");
                }
            }
            
            fclose($csvFile);
            
            $qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
}
header("Location: index.php".$qstring);