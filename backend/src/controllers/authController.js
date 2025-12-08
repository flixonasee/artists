import bcrypt from 'bcrypt';
import jwt from 'jsonwebtoken';
import { findUserByEmail, seedUsers } from '../models/userModel.js';
import { config } from '../config/index.js';

seedUsers();

export const login = (req, res) => {
  const { email, password } = req.body;
  const user = findUserByEmail(email);
  if (!user) return res.status(401).json({ error: 'Invalid credentials' });
  if (!bcrypt.compareSync(password, user.password)) return res.status(401).json({ error: 'Invalid credentials' });
  const token = jwt.sign({ id: user.id, name: user.name, email: user.email }, config.jwtSecret, { expiresIn: '7d' });
  res.json({ token, user: { id: user.id, name: user.name, email: user.email } });
};
