import db from '../db/index.js';

export const createWork = (artistId, data) => {
  const stmt = db.prepare('INSERT INTO works(artist_id, title, year, image_url, notes) VALUES (@artist_id, @title, @year, @image_url, @notes)');
  const info = stmt.run({
    artist_id: artistId,
    title: data.title,
    year: data.year,
    image_url: data.image_url,
    notes: data.notes || '',
  });
  return getWorks(artistId).find((w) => w.id === info.lastInsertRowid);
};

export const getWorks = (artistId) => {
  const stmt = db.prepare('SELECT * FROM works WHERE artist_id = ? ORDER BY id DESC');
  return stmt.all(artistId);
};
