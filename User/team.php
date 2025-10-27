<?php
// team_table_index_colors.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Team & Guide - Lapcart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #e6e6e6;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
            min-height: 100vh;
            padding-top: 70px;
        }
        h2 {
            color: #00cc88;
            text-align: center;
            margin-bottom: 15px;
        }
        .team-table {
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 10px;
            margin: 20px auto 30px auto;
            width: 90%;
        }
        .team-table th, .team-table td {
            text-align: center;
            padding: 12px;
            border-bottom: 1px solid #333;
        }
        .team-table th {
            background-color: #14532d;
            color: #fff;
            font-weight: bold;
        }        
        .team-table td img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .team-table tbody tr:nth-child(even) {
            background-color: #2a2a2a;
        }
        .back-home {
            position: fixed;
            top: 20px;
            right: 80px;
            z-index: 1000;
        }

        /* Guide table adjustments to match member table width and padding */
        .guide-table {
            table-layout: fixed;
        }
        .guide-table th, .guide-table td {
            width: 16.66%; /* equalize columns to match 6-column member table */
            padding: 12px;
        }
        .guide-table td:nth-child(1) {
            display: none; /* hide the first column (photo) for guide */
        }
    </style>
</head>
<body>

<a href="index.php" class="btn btn-success back-home">⬅ Back to Home</a>

<h2>Meet Our Team</h2>

<table class="team-table">
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Role</th>
            <th>Description</th>
            <th>Enrollment No</th>
            <th>Class</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><img src="A.png" alt="Arman Khorajiya"></td>
            <td>Arman Khorajiya</td>
            <td>Full Stack Developer</td>
            <td>Handles both frontend and backend of our e-commerce project "Lapcart". Expert in PHP and UI design.</td>
            <td>23020201091</td>
            <td>5B Diploma CE</td>
        </tr>
        <tr>
            <td><img src="N.jpg" alt="Nazil Mathakiya"></td>
            <td>Nazil Mathakiya</td>
            <td>SRS Specialist</td>
            <td>Manages all documentation and requirement analysis, ensuring the project is well-structured and organized.</td>
            <td>23020201106</td>
            <td>5B Diploma CE</td>
        </tr>
    </tbody>
</table>

<h2>Guided By</h2>

<table class="team-table guide-table">
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Role</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td><img src="Aa.jpg"></td>
            <td>Aadhyashree Pandya (ABP)</td>
            <td>Assistant Professor</td>
            <td>Provides guidance and mentorship for the team, ensuring project quality and adherence to standards.</td>
        </tr>
    </tbody>
</table>

</body>
</html>
