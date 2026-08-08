<?php

function storeDepartment($conn, $name)
{
    $sql = "INSERT INTO departments (department_name) VALUES (?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    return $stmt->execute();
}

function updateDepartment($conn, $id, $name)
{
    $sql = "UPDATE departments SET department_name=? WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);

    return $stmt->execute();
}

function deleteDepartment($conn, $id)
{
    $sql = "DELETE FROM departments WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

function getAllDepartments($conn)
{
    $sql = "SELECT d.id, d.department_name, COUNT(e.id) AS employee_count
            FROM departments d
            LEFT JOIN employees e ON e.department_id=d.id
            GROUP BY d.id
            ORDER BY d.department_name";

    return $conn->query($sql);
}
