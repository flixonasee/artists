import db from '../db/index.js';
import { listBadges } from '../models/badgeModel.js';
import { getRecentActivity } from '../models/pointsLogModel.js';

export const getDashboardData = () => {
  const artistCount = db.prepare('SELECT COUNT(*) as count FROM artists').get().count;
  const workCount = db.prepare('SELECT COUNT(*) as count FROM works').get().count;
  const photoCount = db.prepare('SELECT COUNT(*) as count FROM photos').get().count;
  const pointsByUser = db.prepare('SELECT user_id, SUM(points) as points FROM points_logs GROUP BY user_id').all();
  return {
    stats: { artistCount, workCount, photoCount },
    badges: listBadges(),
    activity: getRecentActivity(30),
    ranking: pointsByUser,
  };
};
