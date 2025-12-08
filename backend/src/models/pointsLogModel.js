import db from '../db/index.js';

export const logPoints = (userId, artistId, action, points) => {
  const stmt = db.prepare('INSERT INTO points_logs(user_id, artist_id, action, points, timestamp) VALUES (?, ?, ?, ?, ?)');
  stmt.run(userId, artistId || null, action, points, Date.now());
};

export const countActions = (userId, action) => {
  const stmt = db.prepare('SELECT COUNT(*) as count FROM points_logs WHERE user_id = ? AND action = ?');
  return stmt.get(userId, action).count;
};

export const getRecentActivity = (limit = 20) => {
  const stmt = db.prepare('SELECT * FROM points_logs ORDER BY timestamp DESC LIMIT ?');
  return stmt.all(limit);
};

export const getTotals = (userId) => {
  const stmt = db.prepare('SELECT SUM(points) as total FROM points_logs WHERE user_id = ?');
  return stmt.get(userId).total || 0;
};
