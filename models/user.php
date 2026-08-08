<?php

function getUserByEmail($conn, $email)
{
    $sql = "SELECT * FROM users WHERE email=? AND status=1 LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    return $stmt->get_result();
}

function getEmployeeUser($conn, $employee_id)
{
    $sql = "SELECT id FROM users WHERE employee_id=? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();

    return $stmt->get_result();
}

function createUser($conn, $name, $email, $password, $role, $employee_id)
{
    $hashedPassword  = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users
            (name, email, password, role, employee_id)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $hashedPassword, $role, $employee_id);

    return $stmt->execute();
}

function emailExists($conn, $email)
{
    $sql = "SELECT id FROM users WHERE email=? LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return true;
    }

    return false;
}
