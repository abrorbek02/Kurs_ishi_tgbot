<?php
require_once 'db.php';
class User
{
    private $chat_id;
    private $name;

    public function __construct($chat_id, $name)
    {
        $this->chat_id = $chat_id;
        $this->name = $name;

        if (!$this->is_registered()) {
            $this->register();
        }
    }

    public function is_registered(): bool
    {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM users WHERE chat_id = ?");
        $stmt->bind_param("s", $this->chat_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function register(): void
    {
        global $conn;

        $stmt = $conn->prepare("INSERT INTO users (chat_id, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $this->chat_id, $this->name);
        $stmt->execute();
    }
    public function get_name(): string
    {
        return $this->name;
    }
    public function get_chat_id(): string
    {
        return $this->chat_id;
    }
    public function set($key, $value): void
    {
        global $conn;

        $stmt = $conn->prepare("UPDATE users SET $key = ? WHERE chat_id = ?");
        $stmt->bind_param("ss", $value, $this->chat_id);
        $stmt->execute();
    }
    public function get($key)
    {
        global $conn;

        $stmt = $conn->prepare("SELECT $key FROM users WHERE chat_id = ?");
        $stmt->bind_param("s", $this->chat_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc()[$key];
    }
}