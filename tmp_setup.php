<?php
$db = new SQLite3('writable/database/mobile_money.sqlite');
$hash = password_hash('1234', PASSWORD_DEFAULT);
$sql1 = "INSERT INTO operators (username, password_hash) VALUES ('Arnaut', '$hash') ON CONFLICT(username) DO UPDATE SET password_hash=excluded.password_hash";
$sql2 = "INSERT OR IGNORE INTO clients (phone_number, balance, status) VALUES ('0385608876', 0.00, 'active')";
$db->exec($sql1);
$db->exec($sql2);
$res = $db->query('SELECT username, password_hash FROM operators WHERE username="Arnaut"');
echo "operator=";
print_r($res->fetchArray(SQLITE3_ASSOC));
$res2 = $db->query('SELECT phone_number, balance, status FROM clients WHERE phone_number="0385608876"');
echo "\nclient=";
print_r($res2->fetchArray(SQLITE3_ASSOC));
