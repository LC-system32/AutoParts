// file: api/middlewares/auth.js
import { getUserByToken } from '../models/userModel.js';

/**
 * Допоміжна функція: нормалізує масив ролей у користувача
 * в нижній регістр (['admin', 'manager', 'customer', ...]).
 */
function normalizeRoles(user) {
  const roles = Array.isArray(user.roles) ? user.roles : [];
  return roles
    .filter((r) => r !== null && r !== undefined)
    .map((r) => String(r).toLowerCase());
}

/**
 * authenticate — глобальний middleware, який:
 *  - читає заголовок Authorization: Bearer <token> або x-auth-token
 *  - знаходить користувача в БД через user_sessions + roles
 *  - кладе об'єкт користувача в req.user
 *
 * Якщо токен не валідний / немає користувача — req.user = null
 */
export async function authenticate(req, res, next) {
  try {
    // Основний заголовок
    const authHeader =
      req.headers['authorization'] || req.headers['Authorization'];

    // Альтернативний заголовок, якщо по дорозі зрізали Authorization
    const xAuthToken =
      req.headers['x-auth-token'] || req.headers['X-Auth-Token'];

    // DEBUG (можна потім прибрати)

    // За замовчуванням вважаємо, що користувача немає
    req.user = null;

    let token = null;

    // 1) Першою пробуємо розпарсити Authorization: Bearer <token>
    if (authHeader) {
      const parts = String(authHeader).split(' ');
      if (parts.length === 2 && parts[0] === 'Bearer') {
        token = parts[1].trim();
      }
    }

    // 2) Якщо Authorization немає або формат кривий — пробуємо x-auth-token
    if (!token && xAuthToken) {
      token = String(xAuthToken).trim();
    }

    // Якщо взагалі не знайшли токен — йдемо далі як гість
    if (!token) {
      return next();
    }

    // Шукаємо користувача по токену в БД
    const user = await getUserByToken(token);

    if (user) {
      user.roles = normalizeRoles(user);
      req.user = user;
      // expose the current token on the request for downstream handlers
      req.token = token;
    }

    return next();
  } catch (err) {
    // Не валимо API, просто вважаємо що користувач неавторизований
    req.user = null;
    return next();
  }
}

/**
 * requireAuth — пропускає лише авторизованих користувачів.
 */
export function requireAuth(req, res, next) {
  if (!req.user) {
    return res
      .status(401)
      .json({ success: false, error: 'Unauthorized' });
  }
  return next();
}

/**
 * requireAdmin — пропускає тільки admin/manager.
 */
export function requireAdmin(req, res, next) {
  if (!req.user) {
    return res
      .status(401)
      .json({ success: false, error: 'Unauthorized' });
  }

  const roles = Array.isArray(req.user.roles)
    ? req.user.roles.map((r) => String(r).toLowerCase())
    : [];

  const isAdmin = roles.includes('admin') || roles.includes('manager');

  if (!isAdmin) {
    return res
      .status(403)
      .json({ success: false, error: 'Forbidden: admin only' });
  }

  return next();
}
