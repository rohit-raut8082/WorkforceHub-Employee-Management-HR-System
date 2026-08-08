<?php

function getNextEmployeeCode($conn)
{
    $sql = "SELECT employee_code FROM employees ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        return "EMP001";
    }

    $row = $result->fetch_assoc();
    $number = (int) substr($row["employee_code"], 3);

    $number = $number + 1;

    return "EMP" . str_pad($number, 3, "0", STR_PAD_LEFT);
}

function storeEmployee($conn, $data)
{
    $sql = "INSERT INTO employees
            (employee_code, name, email, phone, gender, department_id, salary, joining_date, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssidss",
        $data["employee_code"],
        $data["name"],
        $data["email"],
        $data["phone"],
        $data["gender"],
        $data["department_id"],
        $data["salary"],
        $data["joining_date"],
        $data["photo"]
    );

    return $stmt->execute();
}

function getAllEmployees($conn)
{
    $sql = "SELECT e.*, d.department_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            ORDER BY e.name";

    return $conn->query($sql);
}

function getEmployeeById($conn, $id)
{
    $sql = "SELECT e.*, d.department_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE e.id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result();
}

function updateEmployee($conn, $data)
{
    $sql = "UPDATE employees
            SET name=?, email=?, phone=?, gender=?, department_id=?,
                salary=?, joining_date=?, photo=?
            WHERE id=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssidssi",
        $data["name"],
        $data["email"],
        $data["phone"],
        $data["gender"],
        $data["department_id"],
        $data["salary"],
        $data["joining_date"],
        $data["photo"],
        $data["id"]
    );

    return $stmt->execute();
}

function deleteEmployee($conn, $id)
{
    $sql = "DELETE FROM employees WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

function getEmployeePhoto($conn, $id)
{
    $sql = "SELECT photo FROM employees WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result();
}

function getEmployeeByCodeAndEmail($conn, $employee_code, $email)
{
    $sql = "SELECT * FROM employees
            WHERE employee_code=? AND email=?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $employee_code, $email);
    $stmt->execute();

    return $stmt->get_result();
}

function getEmployeeByUserId($conn, $user_id)
{
    $sql = "SELECT e.*, d.department_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id=d.id
            INNER JOIN users u ON u.employee_id=e.id
            WHERE u.id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    return $stmt->get_result();
}
