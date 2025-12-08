import dotenv from 'dotenv';
import fs from 'fs';
import path from 'path';

dotenv.config();

export const config = {
  port: process.env.PORT || 4000,
  jwtSecret: process.env.JWT_SECRET || 'secret',
  databasePath: process.env.DATABASE_PATH || './data/artist-index.db',
  uploadDir: process.env.UPLOAD_DIR || './uploads',
  origins: (process.env.ALLOWED_ORIGINS || '').split(',').filter(Boolean),
};

export const gamificationRules = JSON.parse(
  fs.readFileSync(path.resolve('src/config/gamification.json'), 'utf-8')
);
