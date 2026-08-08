<?php

function saveAttendance($conn, $employee_id, $date, $status)
{
    $sql = "SELECT id FROM attendance
            WHERE employee_id=? AND attendance_date=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $employee_id, $date);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $sql = "UPDATE attendance SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $row["id"]);

        return $stmt->execute();
    }

    $sql = "INSERT INTO attendance
            (employee_id, attendance_date, status)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $employee_id, $date, $status);

    return $stmt->execute();
}

function getAttendanceByDate($conn, $date)
{
    $sql = "SELECT e.id, e.employee_code, e.name,
                   d.department_name, a.status
            FROM employees e
            LEFT JOIN departments d ON e.department_id=d.id
            LEFT JOIN attendance a
                ON a.employee_id=e.id
                AND a.attendance_date=?
            ORDER BY e.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();

    return $stmt->get_result();
}

function getMonthlyAttendance($conn, $year, $month)
{
    $sql = "SELECT e.employee_code, e.name,
                   SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS present_days,
                   SUM(CASE WHEN a.status='Absent' THEN 1 ELSE 0 END) AS absent_days
            FROM employees e
            LEFT JOIN attendance a
                ON a.employee_id=e.id
                AND YEAR(a.attendance_date)=?
                AND MONTH(a.attendance_date)=?
            GROUP BY e.id
            ORDER BY e.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();

    return $stmt->get_result();
}

function getEmployeeAttendance($conn, $employee_id, $month)
{
    $year = date("Y", strtotime($month . "-01"));
    $month_number = date("m", strtotime($month . "-01"));

    $sql = "SELECT attendance_date, status
            FROM attendance
            WHERE employee_id=?
            AND YEAR(attendance_date)=?
            AND MONTH(attendance_date)=?
            ORDER BY attendance_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $employee_id, $year, $month_number);
    $stmt->execute();

    return $stmt->get_result();
}
