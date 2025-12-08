import fs from 'fs';
import path from 'path';
import Database from 'better-sqlite3';
import dotenv from 'dotenv';

dotenv.config();
const dbPath = process.env.DATABASE_PATH || './data/artist-index.db';
fs.mkdirSync(path.dirname(dbPath), { recursive: true });
const db = new Database(dbPath);

const migrationsDir = path.resolve('db/migrations');
const files = fs.readdirSync(migrationsDir).sort();

files.forEach((file) => {
  const sql = fs.readFileSync(path.join(migrationsDir, file), 'utf-8');
  db.exec(sql);
  console.log(`Applied migration ${file}`);
});

db.close();
