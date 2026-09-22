<?php
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    points INTEGER DEFAULT 20,
    free_used_today INTEGER DEFAULT 0,
    last_free_reset TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS chat_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    model TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('user','assistant')),
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    amount INTEGER NOT NULL,
    type TEXT NOT NULL CHECK(type IN ('redeem','purchase','free_daily')),
    voucher_link TEXT DEFAULT NULL,
    status TEXT DEFAULT 'pending' CHECK(status IN ('pending','success','failed')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

echo "Database initialized successfully.\n";
