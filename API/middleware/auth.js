import { query } from '../database/db.js';

export async function verifyToken(token) {
  const result = await query('SELECT * FROM users WHERE remember_token = ?', [token]);
  return result.length > 0 ? result[0] : null;
}