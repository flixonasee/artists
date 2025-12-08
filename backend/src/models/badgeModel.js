import db from '../db/index.js';

export const ensureBadges = (definitions) => {
  const existing = db.prepare('SELECT COUNT(*) as count FROM badges').get();
  if (existing.count === 0) {
    const stmt = db.prepare('INSERT INTO badges(name, description, icon_url) VALUES (@name, @description, @icon_url)');
    const insertMany = db.transaction((rows) => {
      rows.forEach((row) => stmt.run(row));
    });
    insertMany(definitions);
  }
};

export const listBadges = () => db.prepare('SELECT * FROM badges').all();
export const getBadgeByName = (name) => db.prepare('SELECT * FROM badges WHERE name = ?').get(name);
