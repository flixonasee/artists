import db from '../db/index.js';

export const createPhoto = (artistId, data) => {
  const stmt = db.prepare('INSERT INTO photos(artist_id, image_url, caption) VALUES (?, ?, ?)');
  const info = stmt.run(artistId, data.image_url, data.caption || '');
  return getPhotos(artistId).find((p) => p.id === info.lastInsertRowid);
};

export const getPhotos = (artistId) => {
  const stmt = db.prepare('SELECT * FROM photos WHERE artist_id = ? ORDER BY id DESC');
  return stmt.all(artistId);
};
