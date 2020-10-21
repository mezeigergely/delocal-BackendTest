<?php

// db connection
$connection = new mysqli('localhost','root', '', 'contacts');


// return contacts
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{

    // return a contact by id (for example: http://localhost/contacts-API/contacts/4)
    if (isset($_GET['id']))
    {
        $id = $connection->real_escape_string($_GET['id']);
        $query = $connection->query("SELECT * FROM users WHERE id='$id'");
        $data = $query->fetch_assoc();
    }

    // return all contacts (for example: http://localhost/contacts-API/contacts)
    else
    {
        $data = array();
        $query = $connection->query("SELECT * FROM users");
        while ($d = $query->fetch_assoc())
        {
            $data[] = $d;
        }       
    }

    exit(json_encode($data));
}

// create a contact
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['name']) &&
        isset($_POST['email']) &&
        isset($_POST['phone']) &&
        isset($_POST['address']))
    {
        $name = $connection->real_escape_string($_POST['name']);
        $email = $connection->real_escape_string($_POST['email']);
        $phone = $connection->real_escape_string($_POST['phone']);
        $address = $connection->real_escape_string($_POST['address']);
		
		if(strlen($name) == 0 || strlen($email) == 0 || strlen($phone) == 0 || strlen($address) == 0)
		{
			exit(json_encode(array("status" => 'failed', 'details' => 'wrong inputs')));
		}

        $connection->query("INSERT INTO users (name,email,phone,address) VALUES ('$name', '$email', '$phone', '$address') ");
        exit(json_encode(array("status" => 'contact created')));
    }
    else
    {
        exit(json_encode(array("status" => 'failed', 'details' => 'wrong inputs')));
    }     
}

//modify a contact
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    if (!isset($_GET['id']))
        exit(json_encode(array("status" => 'failed', 'details' => 'wrong inputs')));

    $userID = $connection->real_escape_string($_GET['id']);
    $data = urldecode(file_get_contents('php://input'));

    if (strpos($data, '=') !== false)
    {
        $allPairs = array();
        $data = explode('&', $data);
        foreach($data as $pair)
        {
            $pair = explode('=', $pair);
            $allPairs[$pair[0]] = $pair[1];
        }

        if (isset($allPairs['name']) &&
            isset($allPairs['email']) &&
            isset($allPairs['phone']) &&
            isset($allPairs['address']))
        {
            $connection->query("UPDATE users SET name='".$allPairs['name']."', email='".$allPairs['email']."', phone='".$allPairs['phone']."', address='".$allPairs['address']."' WHERE id='$userID'");
        }
        else if (isset($allPairs['name']))
        {
            $connection->query("UPDATE users SET name='".$allPairs['name']."' WHERE id='$userID'");
        }
        else if (isset($allPairs['email']))
        {
            $connection->query("UPDATE users SET email='".$allPairs['email']."' WHERE id='$userID'");
        }
        else if (isset($allPairs['phone']))
        {
            $connection->query("UPDATE users SET phone='".$allPairs['phone']."' WHERE id='$userID'");
        }
        else if (isset($allPairs['address']))
        {
            $connection->query("UPDATE users SET address='".$allPairs['address']."' WHERE id='$userID'");
        }
        else
        {
            exit(json_encode(array("status" => 'failed', 'details' => 'wrong inputs')));
        }
        exit(json_encode(array("status" => 'modified')));
    }
    else
    {
        exit(json_encode(array("status" => 'failed', 'details' => 'wrong inputs')));
    }       
}
?>