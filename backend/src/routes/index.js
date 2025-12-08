import express from 'express';
import multer from 'multer';
import {
  getAllArtists,
  postArtist,
  patchArtist,
  deleteArtistHandler,
  postPhoto,
  postWork,
  getArtistDetail,
} from '../controllers/artistController.js';
import { login } from '../controllers/authController.js';
import { getDashboard } from '../controllers/dashboardController.js';
import { authMiddleware } from '../utils/auth.js';
import { saveBuffer } from '../utils/storage.js';

const router = express.Router();
const upload = multer();

router.post('/login', login);

router.use(authMiddleware);
router.get('/artists', getAllArtists);
router.post('/artists', postArtist);
router.get('/artists/:id', getArtistDetail);
router.patch('/artists/:id', patchArtist);
router.delete('/artists/:id', deleteArtistHandler);
router.post('/artists/:id/photos', postPhoto);
router.post('/artists/:id/works', postWork);
router.get('/dashboard', getDashboard);

router.post('/upload', upload.single('file'), (req, res) => {
  if (!req.file) return res.status(400).json({ error: 'File required' });
  const url = saveBuffer(req.file.buffer, req.file.originalname);
  res.json({ url });
});

export default router;
