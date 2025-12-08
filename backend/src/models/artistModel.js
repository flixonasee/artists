import db from '../db/index.js';

const parseJson = (value) => {
  try {
    return JSON.parse(value || '[]');
  } catch (e) {
    return [];
  }
};

export const listArtists = () => {
  const stmt = db.prepare('SELECT * FROM artists ORDER BY id DESC');
  return stmt.all().map((a) => ({ ...a, tags: parseJson(a.tags), links: parseJson(a.links) }));
};

export const createArtist = (data) => {
  const stmt = db.prepare(`INSERT INTO artists(name, birth_year, bio, featured_image_url, tags, links)
    VALUES (@name, @birth_year, @bio, @featured_image_url, @tags, @links)`);
  const info = stmt.run({
    name: data.name,
    birth_year: data.birth_year,
    bio: data.bio,
    featured_image_url: data.featured_image_url,
    tags: JSON.stringify(data.tags || []),
    links: JSON.stringify(data.links || []),
  });
  return getArtist(info.lastInsertRowid);
};

export const getArtist = (id) => {
  const stmt = db.prepare('SELECT * FROM artists WHERE id = ?');
  const artist = stmt.get(id);
  if (!artist) return null;
  return { ...artist, tags: parseJson(artist.tags), links: parseJson(artist.links) };
};

export const updateArtist = (id, data) => {
  const existing = getArtist(id);
  if (!existing) return null;
  const updated = {
    ...existing,
    ...data,
    tags: data.tags !== undefined ? data.tags : existing.tags,
    links: data.links !== undefined ? data.links : existing.links,
  };
  const stmt = db.prepare(`UPDATE artists SET name=@name, birth_year=@birth_year, bio=@bio, featured_image_url=@featured_image_url,
    tags=@tags, links=@links WHERE id=@id`);
  stmt.run({
    ...updated,
    tags: JSON.stringify(updated.tags || []),
    links: JSON.stringify(updated.links || []),
    id,
  });
  return getArtist(id);
};

export const deleteArtist = (id) => {
  const stmt = db.prepare('DELETE FROM artists WHERE id = ?');
  return stmt.run(id);
};
