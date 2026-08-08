<?php

function applyLeave($conn, $employee_id, $leave_type, $start_date, $end_date, $reason)
{
    $sql = "INSERT INTO leaves
            (employee_id, leave_type, start_date, end_date, reason, status)
            VALUES (?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issss",
        $employee_id,
        $leave_type,
        $start_date,
        $end_date,
        $reason
    );

    return $stmt->execute();
}

function getEmployeeLeaves($conn, $employee_id)
{
    $sql = "SELECT * FROM leaves
            WHERE employee_id=?
            ORDER BY applied_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();

    return $stmt->get_result();
}

function updateLeaveStatus($conn, $id, $status)
{
    $sql = "UPDATE leaves SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    return $stmt->execute();
}

function getAllLeaves($conn, $status = "")
{
    if ($status != "") {
        $sql = "SELECT l.*, e.name, e.employee_code
                FROM leaves l
                INNER JOIN employees e ON e.id=l.employee_id
                WHERE l.status=?
                ORDER BY l.applied_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();

        return $stmt->get_result();
    }

    $sql = "SELECT l.*, e.name, e.employee_code
            FROM leaves l
            INNER JOIN employees e ON e.id=l.employee_id
            ORDER BY l.applied_at DESC";

    return $conn->query($sql);
}

function getRecentLeaves($conn)
{
    $sql = "SELECT l.*, e.name, e.employee_code
            FROM leaves l
            INNER JOIN employees e ON e.id=l.employee_id
            ORDER BY l.applied_at DESC
            LIMIT 5";

    return $conn->query($sql);
}
