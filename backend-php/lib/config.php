<?php
// Basic configuration constants for Artist Index PHP backend
// Adjust FRONTEND_ORIGIN to your deployed frontend domain
const DB_PATH = __DIR__ . '/../data.sqlite';
const JWT_SECRET = 'change_this_secret_in_production';
const UPLOAD_DIR = __DIR__ . '/../uploads';
const FRONTEND_ORIGIN = '*'; // e.g., 'https://index.housita.art'
const TOKEN_EXPIRY_DAYS = 7;
