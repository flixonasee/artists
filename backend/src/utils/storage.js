import fs from 'fs';
import path from 'path';
import { config } from '../config/index.js';
import { v4 as uuid } from 'uuid';

export const ensureUploadDir = () => {
  fs.mkdirSync(config.uploadDir, { recursive: true });
};

export const saveBuffer = (buffer, originalName) => {
  ensureUploadDir();
  const ext = path.extname(originalName) || '.jpg';
  const filename = `${uuid()}${ext}`;
  const filepath = path.join(config.uploadDir, filename);
  fs.writeFileSync(filepath, buffer);
  return `/uploads/${filename}`;
};
