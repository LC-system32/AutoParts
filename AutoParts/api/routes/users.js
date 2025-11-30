// routes/users.js
import express from 'express';
import { requireAuth, requireAdmin } from '../middlewares/auth.js';
import { getUserById, updateUser } from '../models/userModel.js';
import { dbQuery } from '../db/index.js';

export const usersRouter = express.Router();

// Отримати профіль користувача
usersRouter.get('/:id', requireAuth, async (req, res) => {
    const id = parseInt(req.params.id, 10);
    // Only allow the authenticated user to fetch their own profile or admins
    if (!req.user || (Number(req.user.id) !== id && !req.user.roles?.includes('admin'))) {
        return res.status(403).json({ success: false, error: 'Forbidden' });
    }
    try {
        const profile = await getUserById(id);
        if (!profile) {
            return res.status(404).json({ success: false, error: 'User not found' });
        }
        return res.json({ success: true, data: profile });
    } catch (err) {
        console.error('GET /api/users/:id error', err);
        return res.status(500).json({ success: false, error: 'Failed to fetch profile' });
    }
});

// List all users (admin only)
usersRouter.get('/', requireAuth, requireAdmin, async (req, res) => {
    try {
        const { rows } = await dbQuery(
            `SELECT u.id, u.email, u.login, u.first_name, u.last_name, u.phone, u.is_active,
                    array_remove(array_agg(r.code), NULL) AS roles
             FROM users u
             LEFT JOIN user_roles ur ON ur.user_id = u.id
             LEFT JOIN roles r       ON r.id = ur.role_id
             GROUP BY u.id, u.email, u.login, u.first_name, u.last_name, u.phone, u.is_active
             ORDER BY u.id`);
        return res.json({ success: true, data: rows });
    } catch (err) {
        console.error('GET /api/users error', err);
        return res.status(500).json({ success: false, error: 'Failed to list users' });
    }
});

// Get all active sessions for a user
usersRouter.get('/:id/sessions', requireAuth, async (req, res) => {
    const id = parseInt(req.params.id, 10);
    // only allow the user themselves or admin/manager to view
    const roles = Array.isArray(req.user.roles) ? req.user.roles.map((r) => String(r).toLowerCase()) : [];
    const isAdmin = roles.includes('admin') || roles.includes('manager');
    if (!req.user || (Number(req.user.id) !== id && !isAdmin)) {
        return res.status(403).json({ success: false, error: 'Forbidden' });
    }
    try {
        const { rows } = await dbQuery(
            `SELECT id, token, ip_address, user_agent, created_at, expires_at
             FROM user_sessions
             WHERE user_id = $1
             ORDER BY created_at DESC`,
            [id]
        );
        return res.json({ success: true, data: rows });
    } catch (err) {
        console.error('GET /api/users/:id/sessions error', err);
        return res.status(500).json({ success: false, error: 'Failed to fetch sessions' });
    }
});

// Terminate all other sessions for a user (keep current)
usersRouter.delete('/:id/sessions', requireAuth, async (req, res) => {
    const id = parseInt(req.params.id, 10);
    const roles = Array.isArray(req.user.roles) ? req.user.roles.map((r) => String(r).toLowerCase()) : [];
    const isAdmin = roles.includes('admin') || roles.includes('manager');
    if (!req.user || (Number(req.user.id) !== id && !isAdmin)) {
        return res.status(403).json({ success: false, error: 'Forbidden' });
    }
    // We expect authenticate middleware to put the current token on req.token
    const currentToken = req.token;
    try {
        if (currentToken) {
            await dbQuery(
                `DELETE FROM user_sessions
                 WHERE user_id = $1
                   AND token <> $2`,
                [id, currentToken]
            );
        } else {
            // If we don't know the current token, delete all sessions
            await dbQuery(
                `DELETE FROM user_sessions
                 WHERE user_id = $1`,
                [id]
            );
        }
        return res.json({ success: true });
    } catch (err) {
        console.error('DELETE /api/users/:id/sessions error', err);
        return res.status(500).json({ success: false, error: 'Failed to terminate sessions' });
    }
});

// Оновити профіль користувача
usersRouter.patch('/:id', requireAuth, async (req, res) => {
    const id = parseInt(req.params.id, 10);

    if (!req.user || (Number(req.user.id) !== id && !req.user.roles.includes('admin'))) {
        return res.status(403).json({ success: false, error: 'Forbidden' });
    }

    try {
        const updated = await updateUser(id, req.body);
        if (!updated) {
            return res.status(404).json({ success: false, error: 'User not found' });
        }
        res.json({ success: true, data: updated });
    } catch (err) {
        console.error(err);
        res.status(500).json({ success: false, error: 'Internal server error' });
    }
});
usersRouter.get('/by-email', async (req, res) => {
    const email = String(req.query.email || '').trim().toLowerCase();
    if (!email) {
        return res.status(400).json({ success: false, error: 'Email is required' });
    }

    try {
        const user = await findUserByEmail(email);
        if (!user) {
            return res.status(404).json({ success: false, error: 'User not found' });
        }
        return res.json({ success: true, data: user });
    } catch (err) {
        console.error('GET /api/users/by-email error', err);
        return res.status(500).json({ success: false, error: 'Failed to fetch user by email' });
    }
});
