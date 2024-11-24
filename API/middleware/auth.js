import { query } from '../database/db.js';

export async function verifyToken(token) {
  const result = await query('SELECT * FROM sessions WHERE id = ?', [token]);
  return result.length > 0 ? result[0] : null;
}