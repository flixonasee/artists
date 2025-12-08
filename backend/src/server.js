import express from 'express';
import cors from 'cors';
import path from 'path';
import { fileURLToPath } from 'url';
import { config } from './config/index.js';
import router from './routes/index.js';
import { ensureUploadDir } from './utils/storage.js';
import './models/userModel.js';
import { ensureBadges } from './models/badgeModel.js';
import { gamificationRules } from './config/index.js';

ensureUploadDir();
ensureBadges(gamificationRules.badges);

const app = express();
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

app.use(cors({ origin: config.origins.length ? config.origins : '*', credentials: true }));
app.use(express.json({ limit: '10mb' }));
app.use('/uploads', express.static(path.join(__dirname, '../uploads')));
app.use('/api', router);

app.get('/', (_, res) => res.json({ status: 'Artist Index API' }));

app.listen(config.port, () => console.log(`API listening on ${config.port}`));
