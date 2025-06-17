<?php
session_start();
$pdo = require_once 'data/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: inquiries.php");
    exit;
}

$inquiry_id = (int)$_GET['id'];

// Fetch inquiry
$stmt = $pdo->prepare("
    SELECT i.*, u.username AS user_name 
    FROM inquiries i
    JOIN users u ON i.user_id = u.user_id 
    WHERE i.inquiry_id = ?
");
$stmt->execute([$inquiry_id]);
$inquiry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inquiry) {
    die("Inquiry not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = trim($_POST['answer']);
    $admin_id = $_SESSION['admin_id'] ?? 1; // use session value if available

    if ($answer !== '') {
        $update = $pdo->prepare("
            UPDATE inquiries 
            SET answer = ?, status = 'answered', 
                answered_by = ?, answered_at = CURRENT_TIMESTAMP 
            WHERE inquiry_id = ?
        ");
        $update->execute([$answer, $admin_id, $inquiry_id]);
        header("Location: inquiries.php?reply=success");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reply to Inquiry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .reply-container {
            width: 70%;
            margin: 3rem auto;
            background: white;
            padding: 2rem 3rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2 {
            color: #ff8a00;
            margin-bottom: 1rem;
        }

        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #ff8a00;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
        }

        .label {
            font-weight: bold;
            color: #333;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 12px;
            margin-top: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
        }

        .submit-btn {
            background: #ff8a00;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            margin-top: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #e07000;
        }

        .back-btn {
            display: inline-block;
            margin-top: 1.5rem;
            text-decoration: none;
            color: #555;
            font-size: 14px;
        }

        .back-btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="reply-container">
    <h2>Reply to Inquiry</h2>

    <div class="info-box"><span class="label">User:</span> <?= htmlspecialchars($inquiry['user_name']) ?></div>
    <div class="info-box"><span class="label">Subject:</span> <?= htmlspecialchars($inquiry['subject']) ?></div>
    <div class="info-box"><span class="label">Message:</span><br><?= nl2br(htmlspecialchars($inquiry['message'])) ?></div>

    <?php if ($inquiry['status'] !== 'answered' && $inquiry['status'] !== 'closed'): ?>
        <form method="post" onsubmit="return validateForm()">
            <label for="answer" class="label">Your Answer:</label>
            <textarea name="answer" id="answer" required></textarea>
            <button class="submit-btn" type="submit">Submit Reply</button>
        </form>
    <?php else: ?>
        <div class="info-box"><span class="label">Already Answered:</span><br><?= nl2br(htmlspecialchars($inquiry['answer'])) ?></div>
    <?php endif; ?>

    <a href="inquiries.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Inquiries</a>
</div>

<script>
function validateForm() {
    const answer = document.getElementById('answer').value.trim();
    if (answer === '') {
        alert("Please enter a reply before submitting.");
        return false;
    }
    return true;
}
</script>

</body>
</html>
