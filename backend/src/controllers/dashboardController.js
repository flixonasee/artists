import { getDashboardData } from '../services/dashboardService.js';

export const getDashboard = (req, res) => {
  res.json(getDashboardData());
};
