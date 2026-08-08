<?php

function getTotalEmployees($conn)
{
    $sql = "SELECT COUNT(*) AS total FROM employees";
    $result = $conn->query($sql);
    return $result->fetch_assoc()["total"];
}

function getTotalDepartments($conn)
{
    $sql = "SELECT COUNT(*) AS total FROM departments";
    $result = $conn->query($sql);
    return $result->fetch_assoc()["total"];
}

function getPresentToday($conn)
{
    $today = date("Y-m-d");

    $sql = "SELECT COUNT(*) AS total
            FROM attendance
            WHERE attendance_date=? AND status='Present'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc()["total"];
}

function getPendingLeaves($conn)
{
    $sql = "SELECT COUNT(*) AS total
            FROM leaves
            WHERE status='Pending'";

    $result = $conn->query($sql);
    return $result->fetch_assoc()["total"];
}
