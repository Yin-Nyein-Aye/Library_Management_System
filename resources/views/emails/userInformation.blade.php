<!DOCTYPE html>
<html>
<head>
    <title>Reminder: Return Book</title>
</head>
<body>
    <p>Dear {{ $book->user->first_name }}{{ $book->user->middle_name}}{{ $book->user->last_name}},</p>
    <p>This is a reminder to return the book "{{ $book->title }}" as its return date has exceeded.You need to pay fine</p>
    <p>Thank you.</p>
</body>
</html>
