import db from '../db/index.js';
import bcrypt from 'bcrypt';

export const seedUsers = () => {
  const existing = db.prepare('SELECT COUNT(*) as count FROM users').get();
  if (existing.count === 0) {
    const users = [
      { name: 'Gianmaria', email: 'gianmaria@example.com', password: 'password123' },
      { name: 'Giulio', email: 'giulio@example.com', password: 'password123' },
    ];
    const stmt = db.prepare('INSERT INTO users(name, email, password) VALUES (@name, @email, @password)');
    const insertMany = db.transaction((rows) => {
      for (const user of rows) {
        stmt.run({ ...user, password: bcrypt.hashSync(user.password, 10) });
      }
    });
    insertMany(users);
  }
};

export const findUserByEmail = (email) => db.prepare('SELECT * FROM users WHERE email = ?').get(email);
