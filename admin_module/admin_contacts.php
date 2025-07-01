<?php
include('./middleware.php');
middleware();
include('database_connect.php');

$success_message = $error_message = '';

// Handle delete and reply actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            $id = intval($_POST['id']);
            $delete_query = "DELETE FROM newsletter_subscription WHERE id = ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = 'Message deleted successfully!';
            } else {
                $error_message = 'Error deleting message.';
            }
            mysqli_stmt_close($stmt);
        } elseif ($_POST['action'] === 'reply') {
            $to = $_POST['email'];
            $subject = 'Reply from Transport Manager';
            $message = $_POST['reply_message'];
            $headers = 'From: admin@transport.com' . "\r\n" .
                       'Reply-To: admin@transport.com' . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();
            if (mail($to, $subject, $message, $headers)) {
                $success_message = 'Reply sent successfully!';
            } else {
                $error_message = 'Error sending reply. (Mail may not be configured on localhost)';
            }
        }
    }
}

// Get all contacts
$contacts_query = "SELECT * FROM newsletter_subscription ORDER BY created_at DESC";
$contacts_result = mysqli_query($conn, $contacts_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
    <link rel="stylesheet" href="admin_modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-message {
            background: var(--surface-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .contact-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .contact-header h4 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        .contact-actions {
            display: flex;
            gap: 0.5rem;
        }
        .reply-form {
            margin-top: 1rem;
            background: #f1f5f9;
            border-radius: 10px;
            padding: 1rem;
        }
        .reply-form textarea {
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .reply-form button {
            min-width: 120px;
        }
    </style>
    <script>
        function toggleReply(id) {
            var form = document.getElementById('reply-form-' + id);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
<div class="admin-container">
    <?php include('sidebar.php'); ?>
    <main class="main-content">
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <h1>Contact Messages</h1>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if (mysqli_num_rows($contacts_result) > 0): ?>
                <?php while ($contact = mysqli_fetch_assoc($contacts_result)): ?>
                    <div class="contact-message">
                        <div class="contact-header">
                            <h4><i class="fas fa-user"></i> <?php echo htmlspecialchars($contact['name']); ?></h4>
                            <div class="contact-actions">
                                <button class="btn btn-secondary btn-sm" onclick="toggleReply(<?php echo $contact['id']; ?>)"><i class="fas fa-reply"></i> Reply</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                        <div><strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?></div>
                        <div><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($contact['message'])); ?></div>
                        <div style="color: var(--text-light); font-size: 0.95rem; margin-top: 0.5rem;"><i class="fas fa-clock"></i> <?php echo date('M d, Y H:i', strtotime($contact['created_at'])); ?></div>
                        <form method="POST" class="reply-form" id="reply-form-<?php echo $contact['id']; ?>" style="display:none;">
                            <input type="hidden" name="action" value="reply">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>">
                            <textarea name="reply_message" rows="4" placeholder="Type your reply here..." required></textarea>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Reply</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-warning">No contact messages found.</div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html> 