import { gamificationRules } from '../config/index.js';
import { logPoints, countActions } from '../models/pointsLogModel.js';
import { getBadgeByName } from '../models/badgeModel.js';
import db from '../db/index.js';

export const awardForAction = (userId, artistId, action) => {
  const rule = gamificationRules[action];
  if (rule) {
    logPoints(userId, artistId, action, rule.points);
    checkBadges(userId, action);
  }
};

const checkBadges = (userId, action) => {
  gamificationRules.badges.forEach((badgeRule) => {
    if (badgeRule.condition.action === action) {
      const count = countActions(userId, action);
      if (count >= badgeRule.condition.count) {
        const existing = getBadgeByName(badgeRule.name);
        const alreadyEarned = db
          .prepare('SELECT 1 FROM points_logs WHERE user_id = ? AND action = ? LIMIT 1')
          .get(userId, `badge:${badgeRule.name}`);
        if (!alreadyEarned) {
          logPoints(userId, null, `badge:${badgeRule.name}`, 0);
          if (!existing) {
            db.prepare('INSERT INTO badges(name, description, icon_url) VALUES (?, ?, ?)').run(
              badgeRule.name,
              badgeRule.description,
              badgeRule.icon_url
            );
          }
        }
      }
    }
  });
};

export const getUserSummary = (userId) => {
  const points = db.prepare('SELECT SUM(points) as total FROM points_logs WHERE user_id = ?').get(userId).total || 0;
  const badges = db.prepare('SELECT * FROM points_logs WHERE user_id = ? AND action LIKE ?').all(userId, 'badge:%');
  return { points, badges: badges.map((b) => b.action.replace('badge:', '')) };
};
