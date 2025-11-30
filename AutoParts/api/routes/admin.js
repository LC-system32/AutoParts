// path: api/routes/adminRouter.js
import express from 'express';
import bcrypt from 'bcrypt';

import { authenticate, requireAdmin } from '../middlewares/auth.js';

import {
    // USERS / SESSIONS / ROLES
    getAllUsersWithRoles,
    getUserSessions,
    deleteUserSessions,

    // DASHBOARD
    getDashboardStats,

    // ORDERS
    getAdminOrders,
    getAdminOrderDetails,
    updateAdminOrderStatus,

    // REVIEWS
    getPendingReviews,
    approveReview,
    deleteReview,

    // SUPPORT
    getSupportTickets,
    getSupportTicketThread,
    updateSupportTicketStatus,
    replyToSupportTicket,

    // CATALOG
    getAdminProducts,
    getAdminProductDetails,
    updateAdminProduct,

    // BRANDS
    getAdminBrands,
    getAdminBrand,
    createAdminBrand,
    updateAdminBrand,
    deleteAdminBrand,

    // CATEGORIES
    getAdminCategories,
    getAdminCategoryById,
    createAdminCategory,
    updateAdminCategory,
    deleteAdminCategory,

    // DISCOUNTS
    getAdminDiscounts,
    getAdminDiscountById,
    createAdminDiscount,
    updateAdminDiscount,
    deleteAdminDiscount,

    // STOCK / WAREHOUSES
    getAdminStock,
    getAdminWarehouses,
} from '../models/adminModel.js';

import { dbQuery, pool } from '../db/index.js';

export const adminRouter = express.Router();

// всі адмін-роути захищені
adminRouter.use(authenticate, requireAdmin);

// ---------------------------------------------------------------------
// DASHBOARD
// ---------------------------------------------------------------------

// GET /api/admin/stats?period=today|week|month|all
adminRouter.get('/stats', async (req, res) => {
    try {
        const period = (req.query.period || 'today').toString();
        const data = await getDashboardStats(period);
        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN /stats ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load stats' });
    }
});

// ---------------------------------------------------------------------
// USERS / ROLES
// ---------------------------------------------------------------------

// GET /api/admin/roles
adminRouter.get('/roles', async (req, res) => {
    try {
        const { rows } = await dbQuery(
            `SELECT id, code, name, description
       FROM roles
       ORDER BY code ASC`
        );
        return res.json({ success: true, data: rows });
    } catch (err) {
        console.error('ADMIN GET /roles ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load roles' });
    }
});

// POST /api/admin/users/:id/roles
adminRouter.post('/users/:id/roles', async (req, res) => {
    const userId = Number(req.params.id);
    const roles = Array.isArray(req.body.roles) ? req.body.roles : [];
    if (!userId) {
        return res.status(400).json({ success: false, error: 'Invalid user id' });
    }

    const client = await pool.connect();
    try {
        await client.query('BEGIN');
        await client.query('DELETE FROM user_roles WHERE user_id = $1', [userId]);

        const roleIds = roles.map(Number).filter(x => x > 0);
        if (roleIds.length > 0) {
            const values = roleIds.map((_, idx) => `($1,$${idx + 2})`).join(', ');
            await client.query(
                `INSERT INTO user_roles (user_id, role_id) VALUES ${values}`,
                [userId, ...roleIds]
            );
        }

        await client.query('COMMIT');
        return res.json({ success: true, updated: true });
    } catch (err) {
        await client.query('ROLLBACK');
        console.error('ADMIN POST /users/:id/roles ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to update roles' });
    } finally {
        client.release();
    }
});

// GET /api/admin/users?q=&role=&status=
adminRouter.get('/users', async (req, res) => {
    try {
        const users = await getAllUsersWithRoles({
            q: req.query.q || '',
            role: req.query.role || '',
            status: req.query.status || '',
        });
        return res.json({ success: true, data: users });
    } catch (err) {
        console.error('ADMIN /users ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load users' });
    }
});

// GET /api/admin/users/:id
adminRouter.get('/users/:id', async (req, res) => {
    const userId = Number(req.params.id);
    if (!userId) {
        return res.status(400).json({ success: false, error: 'Invalid user id' });
    }

    try {
        const { rows } = await dbQuery(
            `
      SELECT
        u.id,
        u.email,
        u.login,
        u.first_name,
        u.last_name,
        u.phone,
        u.is_active,
        u.created_at,
        u.updated_at,
        COALESCE(
          json_agg(
            json_build_object(
              'id', r.id,
              'code', r.code,
              'name', r.name
            )
          ) FILTER (WHERE r.id IS NOT NULL),
          '[]'
        ) AS roles,
        array_remove(array_agg(r.id), NULL) AS role_ids
      FROM users u
      LEFT JOIN user_roles ur ON ur.user_id = u.id
      LEFT JOIN roles r ON r.id = ur.role_id
      WHERE u.id = $1
      GROUP BY u.id
    `,
            [userId],
        );

        if (!rows[0]) {
            return res.status(404).json({ success: false, error: 'User not found' });
        }

        return res.json({ success: true, data: rows[0] });
    } catch (err) {
        console.error('ADMIN GET /users/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load user' });
    }
});

// POST /api/admin/users  (create)
adminRouter.post('/users', async (req, res) => {
    const {
        email,
        login,
        password,
        password_confirm,
        first_name,
        last_name,
        phone,
        is_active,
        roles,
    } = req.body || {};

    if (!email || !login || !password) {
        return res.status(400).json({
            success: false,
            error: 'email, login та password є обовʼязковими',
        });
    }

    if (password !== password_confirm) {
        return res.status(400).json({ success: false, error: 'Паролі не співпадають' });
    }

    const rolesArr = Array.isArray(roles) ? roles : [];

    const client = await pool.connect();
    try {
        await client.query('BEGIN');

        const hash = await bcrypt.hash(String(password), 10);

        const userInsert = await client.query(
            `
      INSERT INTO users (email, login, password_hash, first_name, last_name, phone, is_active)
      VALUES ($1, $2, $3, $4, $5, $6, COALESCE($7, TRUE))
      RETURNING id
    `,
            [
                String(email).trim(),
                String(login).trim(),
                hash,
                first_name || null,
                last_name || null,
                phone || null,
                typeof is_active === 'boolean' ? is_active : true,
            ],
        );

        const userId = userInsert.rows[0]?.id;
        if (!userId) {
            throw new Error('User not created');
        }

        if (rolesArr.length > 0) {
            const roleIds = rolesArr.map((r) => Number(r)).filter((r) => r > 0);
            if (roleIds.length > 0) {
                const values = roleIds.map((_, idx) => `($1, $${idx + 2})`).join(', ');
                await client.query(
                    `INSERT INTO user_roles (user_id, role_id) VALUES ${values}`,
                    [userId, ...roleIds],
                );
            }
        }

        await client.query('COMMIT');
        return res.json({ success: true, id: userId });
    } catch (err) {
        await client.query('ROLLBACK');
        if (err?.code === '23505') {
            const msg = String(err?.detail || '').toLowerCase();
            return res.status(409).json({
                success: false,
                error: msg.includes('email') ? 'Email вже використовується'
                    : msg.includes('login') ? 'Логін вже використовується'
                        : 'Порушено унікальність (email або логін)'
            });
        }
        console.error('ADMIN POST /users ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to create user' });
    } finally {
        client.release();
    }
});

// POST /api/admin/users/:id (update)
adminRouter.post('/users/:id', async (req, res) => {
    const userId = Number(req.params.id);
    if (!userId) {
        return res.status(400).json({ success: false, error: 'Invalid user id' });
    }

    const {
        email,
        login,
        password,
        password_confirm,
        first_name,
        last_name,
        phone,
        is_active,
        roles,
    } = req.body || {};

    const rolesArr = Array.isArray(roles) ? roles : [];

    const client = await pool.connect();
    try {
        await client.query('BEGIN');

        // update basic fields
        const baseUpdate = await client.query(
            `
      UPDATE users
      SET
        email      = COALESCE($2, email),
        login      = COALESCE($3, login),
        first_name = $4,
        last_name  = $5,
        phone      = $6,
        is_active  = COALESCE($7, is_active),
        updated_at = NOW()
      WHERE id = $1
      RETURNING id
    `,
            [
                userId,
                email ? String(email).trim() : null,
                login ? String(login).trim() : null,
                first_name || null,
                last_name || null,
                phone || null,
                typeof is_active === 'boolean' ? is_active : null,
            ],
        );

        if (!baseUpdate.rows[0]) {
            throw new Error('User not found');
        }

        // update password if provided
        if (password || password_confirm) {
            if (!password || password !== password_confirm) {
                throw new Error('Паролі не співпадають');
            }
            const hash = await bcrypt.hash(String(password), 10);
            await client.query(
                `
        UPDATE users
        SET password_hash = $2,
            updated_at    = NOW()
        WHERE id = $1
      `,
                [userId, hash],
            );
        }

        // update roles
        await client.query('DELETE FROM user_roles WHERE user_id = $1', [userId]);
        const roleIds = rolesArr.map((r) => Number(r)).filter((r) => r > 0);
        if (roleIds.length > 0) {
            const values = roleIds
                .map((_, idx) => `($1, $${idx + 2})`)
                .join(', ');
            await client.query(
                `INSERT INTO user_roles (user_id, role_id) VALUES ${values}`,
                [userId, ...roleIds],
            );
        }

        await client.query('COMMIT');
        return res.json({ success: true, updated: true });
    } catch (err) {
        await client.query('ROLLBACK');
        if (err?.code === '23505') {
            const msg = String(err?.detail || '').toLowerCase();
            return res.status(409).json({
                success: false,
                error: msg.includes('email') ? 'Email вже використовується'
                    : msg.includes('login') ? 'Логін вже використовується'
                        : 'Порушено унікальність (email або логін)'
            });
        }
        console.error('ADMIN POST /users/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to update user' });
    } finally {
        client.release();
    }
});

// Сесії користувача для адмінки
// GET /api/admin/users/:id/sessions
adminRouter.get('/users/:id/sessions', async (req, res) => {
    const userId = Number(req.params.id);
    if (!userId) {
        return res.status(400).json({ success: false, error: 'Invalid user id' });
    }

    try {
        const sessions = await getUserSessions(userId); 
        console.log('DBG user sessions', userId, sessions); // ← побачите в консолі Node

        return res.json({ success: true, data: sessions });
    } catch (err) {
        console.error('ADMIN GET /users/:id/sessions ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load sessions' });
    }
});

// POST /api/admin/users/:id/sessions/terminate
adminRouter.post('/users/:id/sessions/terminate', async (req, res) => {
    const userId = Number(req.params.id);
    if (!userId) {
        return res.status(400).json({ success: false, error: 'Invalid user id' });
    }

    try {
        await deleteUserSessions(userId);
        return res.json({ success: true, data: true });
    } catch (err) {
        console.error('ADMIN POST /users/:id/sessions/terminate ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to delete sessions' });
    }
});

// Сумісний старий DELETE /users/:id/sessions
adminRouter.delete('/users/:id/sessions', async (req, res) => {
    const userId = Number(req.params.id);
    if (!userId) {
        return res.status(400).json({ success: false, error: 'Invalid user id' });
    }

    try {
        await deleteUserSessions(userId);
        return res.json({ success: true, data: true });
    } catch (err) {
        console.error('ADMIN DELETE /users/:id/sessions ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to delete sessions' });
    }
});

// ---------------------------------------------------------------------
// ORDERS
// ---------------------------------------------------------------------

// GET /api/admin/orders
adminRouter.get('/orders', async (req, res) => {
    try {
        const data = await getAdminOrders({
            status: req.query.status || undefined,
            from_date: req.query.from_date || req.query.date_from || undefined,
            to_date: req.query.to_date || req.query.date_to || undefined,
            q: req.query.q || undefined,
            limit: req.query.limit || undefined,
        });
        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN GET /orders ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load orders' });
    }
});

// GET /api/admin/orders/:id
adminRouter.get('/orders/:id', async (req, res) => {
    const id = Number(req.params.id);
    if (!id) {
        return res.status(400).json({ success: false, error: 'Invalid order id' });
    }

    try {
        const details = await getAdminOrderDetails(id);
        if (!details) {
            return res.status(404).json({ success: false, error: 'Order not found' });
        }
        return res.json({ success: true, data: details });
    } catch (err) {
        console.error('ADMIN GET /orders/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load order' });
    }
});

// POST /api/admin/orders/:id/status
adminRouter.post('/orders/:id/status', async (req, res) => {
    const id = Number(req.params.id);
    if (!id) {
        return res.status(400).json({ success: false, error: 'Invalid order id' });
    }

    const status = (req.body.status || '').toString().trim();
    const isPaid = Boolean(req.body.is_paid);

    if (!status) {
        return res.status(400).json({ success: false, error: 'Status is required' });
    }

    try {
        const adminUserId = req.user?.id || null;

        const updated = await updateAdminOrderStatus(id, {
            status,
            is_paid: isPaid,
            adminUserId,
        });

        if (!updated) {
            return res.status(404).json({ success: false, error: 'Order not found' });
        }

        return res.json({ success: true, updated: true, data: updated });
    } catch (err) {
        console.error('ADMIN POST /orders/:id/status ERROR', err);
        return res.status(500).json({
            success: false,
            error: 'Failed to update order status',
        });
    }
});

// ---------------------------------------------------------------------
// REVIEWS
// ---------------------------------------------------------------------

// GET /api/admin/reviews/pending
adminRouter.get('/reviews/pending', async (req, res) => {
    try {
        const reviews = await getPendingReviews();
        return res.json({ success: true, data: reviews });
    } catch (err) {
        console.error('ADMIN GET /reviews/pending ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load reviews' });
    }
});

// POST /api/admin/reviews/:id/approve
adminRouter.post('/reviews/:id/approve', async (req, res) => {
    const id = Number(req.params.id);
    if (!id) {
        return res.status(400).json({ success: false, error: 'Invalid review id' });
    }

    try {
        const ok = await approveReview(id);
        if (!ok) {
            return res.status(404).json({ success: false, error: 'Review not found' });
        }
        return res.json({ success: true, data: true });
    } catch (err) {
        console.error('ADMIN POST /reviews/:id/approve ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to approve review' });
    }
});

// POST /api/admin/reviews/:id/delete
adminRouter.post('/reviews/:id/delete', async (req, res) => {
    const id = Number(req.params.id);
    if (!id) {
        return res.status(400).json({ success: false, error: 'Invalid review id' });
    }

    try {
        const ok = await deleteReview(id);
        if (!ok) {
            return res.status(404).json({ success: false, error: 'Review not found' });
        }
        return res.json({ success: true, data: true });
    } catch (err) {
        console.error('ADMIN POST /reviews/:id/delete ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to delete review' });
    }
});

// ---------------------------------------------------------------------
// SUPPORT
// ---------------------------------------------------------------------

// GET /api/admin/support-tickets?status=&q=
adminRouter.get('/support-tickets', async (req, res) => {
    try {
        const data = await getSupportTickets({
            status: req.query.status,
            q: req.query.q,
        });
        res.json({ success: true, data });
    } catch (e) {
        console.error('ADMIN GET /support-tickets ERROR', e);
        res.status(500).json({ success: false, error: 'Failed to get tickets' });
    }
});

// GET /api/admin/support-tickets/:id
adminRouter.get('/support-tickets/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        const thread = await getSupportTicketThread(id);
        if (!thread) return res.status(404).json({ success: false, error: 'Not found' });
        res.json({ success: true, data: thread });
    } catch (e) {
        console.error('ADMIN GET /support-tickets/:id ERROR', e);
        res.status(500).json({ success: false, error: 'Failed to get ticket' });
    }
});

// POST /api/admin/support-tickets/:id/status
adminRouter.post('/support-tickets/:id/status', async (req, res) => {
    try {
        const id = Number(req.params.id);
        const { status } = req.body || {};
        if (!status) return res.status(400).json({ success: false, error: 'Missing status' });
        const updated = await updateSupportTicketStatus(id, status);
        res.json({ success: true, data: updated });
    } catch (e) {
        console.error('ADMIN POST /support-tickets/:id/status ERROR', e);
        res.status(500).json({ success: false, error: 'Failed to update status' });
    }
});

// POST /api/admin/support-tickets/:id/reply
adminRouter.post('/support-tickets/:id/reply', async (req, res) => {
    try {
        const id = Number(req.params.id);
        const { body, close_ticket } = req.body || {};
        if (!body || !body.trim()) {
            return res.status(400).json({ success: false, error: 'Message body is required' });
        }
        const msg = await replyToSupportTicket(id, {
            staffUserId: req.user.id,
            body: body.trim(),
            closeTicket: !!close_ticket,
        });
        res.status(201).json({ success: true, data: msg });
    } catch (e) {
        console.error('ADMIN POST /support-tickets/:id/reply ERROR', e);
        res.status(500).json({ success: false, error: 'Failed to reply' });
    }
});

// ---------------------------------------------------------------------
// CATALOG & MARKETING
// ---------------------------------------------------------------------

// GET /api/admin/products  – список з фільтрами/пагінацією
adminRouter.get('/products', async (req, res) => {
    try {
        const data = await getAdminProducts({
            q: req.query.q || '',
            status: req.query.status || '',
            category_id: req.query.category_id || '',
            brand_id: req.query.brand_id || '',
            stock: req.query.stock || '',
            page: Number(req.query.page || 1),
            perPage: Number(req.query.perPage || 20),
        });

        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN GET /products ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load products' });
    }
});

// GET /api/admin/products/:id – один маршрут для отримання даних товару (view + edit)
adminRouter.get('/products/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid product id' });
        }

        const details = await getAdminProductDetails(id);
        if (!details) {
            return res.status(404).json({ success: false, error: 'Product not found' });
        }

        // details: { product: {...}, offers: [...] }
        return res.json({ success: true, data: details });
    } catch (err) {
        console.error('ADMIN GET /products/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load product' });
    }
});

// PUT /api/admin/products/:id – оновлення товару (API для редагування)
adminRouter.put('/products/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid product id' });
        }

        const payload = {
            name: req.body.name,
            slug: req.body.slug,
            sku: req.body.sku,
            brand_id:
                req.body.brand_id !== undefined && req.body.brand_id !== null
                    ? Number(req.body.brand_id)
                    : undefined,
            category_id:
                req.body.category_id !== undefined && req.body.category_id !== null
                    ? Number(req.body.category_id)
                    : undefined,
            short_description: req.body.short_description,
            description: req.body.description,
            is_active: req.body.is_active,
        };

        const updated = await updateAdminProduct(id, payload);

        if (!updated) {
            return res.status(404).json({ success: false, error: 'Product not found' });
        }

        return res.json({ success: true, data: updated });
    } catch (err) {
        console.error('ADMIN PUT /products/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to update product' });
    }
});

// GET /api/admin/brands
adminRouter.get('/brands', async (req, res) => {
    try {
        const data = await getAdminBrands({
            q: req.query.q || '',
            with_products: req.query.with_products || '',
            popular: req.query.popular || '',
        });
        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN GET /brands ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load brands' });
    }
});

// GET /api/admin/brands/:id
adminRouter.get('/brands/:id', async (req, res) => {
    const id = Number(req.params.id);
    if (!id) {
        return res.status(400).json({ success: false, error: 'Invalid brand id' });
    }

    try {
        const brand = await getAdminBrand(id);
        if (!brand) {
            return res.status(404).json({ success: false, error: 'Brand not found' });
        }
        return res.json({ success: true, data: brand });
    } catch (err) {
        console.error('ADMIN GET /brands/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load brand' });
    }
});

// POST /api/admin/brands  (create)
adminRouter.post('/brands', async (req, res) => {
    try {
        const payload = {
            name: req.body.name,
            slug: req.body.slug,
            is_active:
                req.body.is_active === true ||
                req.body.is_active === '1' ||
                req.body.is_active === 'on',
        };

        const brand = await createAdminBrand(payload);
        return res.json({ success: true, data: brand });
    } catch (err) {
        console.error('ADMIN POST /brands ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to create brand' });
    }
});

// POST /api/admin/brands/:id  (update)
adminRouter.post('/brands/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!id) {
            return res.status(400).json({ success: false, error: 'Invalid brand id' });
        }

        const payload = {
            name: req.body.name,
            slug: req.body.slug,
            is_active:
                req.body.is_active === true ||
                req.body.is_active === '1' ||
                req.body.is_active === 'on',
        };

        const brand = await updateAdminBrand(id, payload);

        if (!brand) {
            return res.status(404).json({ success: false, error: 'Brand not found' });
        }

        return res.json({ success: true, data: brand });
    } catch (err) {
        console.error('ADMIN POST /brands/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to update brand' });
    }
});

// POST /api/admin/brands/:id/delete
adminRouter.post('/brands/:id/delete', async (req, res) => {
    const id = Number(req.params.id);
    if (!id) {
        return res.status(400).json({ success: false, error: 'Invalid brand id' });
    }

    try {
        const ok = await deleteAdminBrand(id);
        if (!ok) {
            return res.status(404).json({ success: false, error: 'Brand not found' });
        }
        return res.json({ success: true, deleted: true });
    } catch (err) {
        console.error('ADMIN POST /brands/:id/delete ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to delete brand' });
    }
});

// GET /api/admin/categories
adminRouter.get('/categories', async (req, res) => {
    try {
        const data = await getAdminCategories({
            q: req.query.q || '',
            parent: req.query.parent || '',
        });
        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN GET /categories ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load categories' });
    }
});

// GET /api/admin/categories/:id
adminRouter.get('/categories/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid category id' });
        }

        const category = await getAdminCategoryById(id);
        if (!category) {
            return res.status(404).json({ success: false, error: 'Category not found' });
        }

        return res.json({ success: true, data: category });
    } catch (err) {
        console.error('ADMIN GET /categories/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load category' });
    }
});

// POST /api/admin/categories  (create)
adminRouter.post('/categories', async (req, res) => {
    try {
        const name = (req.body.name || '').toString().trim();
        const slug = (req.body.slug || '').toString().trim();
        const parentRaw = req.body.parent_id;
        const isActive =
            req.body.is_active === true ||
            req.body.is_active === '1' ||
            req.body.is_active === 'on';

        let parent_id = null;
        if (
            parentRaw !== undefined &&
            parentRaw !== null &&
            parentRaw !== '' &&
            parentRaw !== 'null'
        ) {
            const n = Number(parentRaw);
            parent_id = Number.isInteger(n) && n > 0 ? n : null;
        }

        if (!name) {
            return res.status(400).json({ success: false, error: 'Category name is required' });
        }

        const created = await createAdminCategory({
            name,
            slug,
            parent_id,
            is_active: isActive,
        });

        return res.json({ success: true, data: created });
    } catch (err) {
        console.error('ADMIN POST /categories ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to create category' });
    }
});

// POST /api/admin/categories/:id  (update)
adminRouter.post('/categories/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid category id' });
        }

        const name = (req.body.name || '').toString().trim();
        const slug = (req.body.slug || '').toString().trim();
        const parentRaw = req.body.parent_id;
        const isActive =
            req.body.is_active === true ||
            req.body.is_active === '1' ||
            req.body.is_active === 'on';

        let parent_id;
        if (parentRaw !== undefined) {
            if (parentRaw === '' || parentRaw === null || parentRaw === 'null') {
                parent_id = null;
            } else {
                const n = Number(parentRaw);
                parent_id = Number.isInteger(n) && n > 0 ? n : null;
            }
        }

        const updated = await updateAdminCategory(id, {
            name,
            slug,
            parent_id,
            is_active: isActive,
        });

        if (!updated) {
            return res.status(404).json({ success: false, error: 'Category not found' });
        }

        return res.json({ success: true, data: updated });
    } catch (err) {
        console.error('ADMIN POST /categories/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to update category' });
    }
});

// POST /api/admin/categories/:id/delete
adminRouter.post('/categories/:id/delete', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid category id' });
        }

        const ok = await deleteAdminCategory(id);
        if (!ok) {
            return res.status(404).json({ success: false, error: 'Category not found' });
        }

        return res.json({ success: true, data: true });
    } catch (err) {
        console.error('ADMIN POST /categories/:id/delete ERROR', err);
        return res.status(500).json({ success: false, error: 'Internal server error' });
    }
});
// GET /api/admin/warehouses
adminRouter.get('/warehouses', async (req, res) => {
    try {
        const data = await getAdminWarehouses();
        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN GET /warehouses ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load warehouses' });
    }
});
// GET /api/admin/discounts
adminRouter.get('/discounts', async (req, res) => {
    try {
        const data = await getAdminDiscounts({
            q: req.query.q || '',
            status: req.query.status || '',
            page: Number(req.query.page || 1),
            perPage: Number(req.query.perPage || 20),
        });
        return res.json({ success: true, data });
    } catch (err) {
        console.error('ADMIN GET /discounts ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load discounts' });
    }
});

// GET /api/admin/discounts/:id
adminRouter.get('/discounts/:id', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid discount id' });
        }
        const discount = await getAdminDiscountById(id);
        if (!discount) {
            return res.status(404).json({ success: false, error: 'Discount not found' });
        }
        return res.json({ success: true, data: discount });
    } catch (err) {
        console.error('ADMIN GET /discounts/:id ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to load discount' });
    }
});

// POST /api/admin/discounts/:id  (update)
// POST /api/admin/discounts/:id  (update)
adminRouter.post('/discounts/:id', async (req, res) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id) || id <= 0) {
      return res.status(400).json({ success: false, error: 'Invalid discount id' });
    }

    const discount = await updateAdminDiscount(id, {
      name: req.body.name,
      description: req.body.description,
      discount_type: req.body.discount_type,
      value: req.body.value,
      date_from: req.body.date_from,
      date_to: req.body.date_to,
      is_active:
        req.body.is_active === true ||
        req.body.is_active === '1' ||
        req.body.is_active === 'on',
      min_order_sum: req.body.min_order_sum,
      code: req.body.code,
    });

    if (!discount) {
      return res.status(404).json({ success: false, error: 'Discount not found' });
    }

    return res.json({ success: true, data: discount });
  } catch (err) {
    console.error('ADMIN POST /discounts/:id ERROR', err);

    if (err.code === '23505') {
      const detail = String(err.detail || '');
      let msg = 'Порушено унікальність даних';

      if (detail.includes('(code)=')) {
        msg = 'Купон з таким кодом вже існує';
      }

      return res.status(409).json({ success: false, error: msg });
    }

    return res.status(500).json({ success: false, error: 'Failed to update discount' });
  }
});

// POST /api/admin/discounts/:id/delete
adminRouter.post('/discounts/:id/delete', async (req, res) => {
    try {
        const id = Number(req.params.id);
        if (!Number.isInteger(id) || id <= 0) {
            return res.status(400).json({ success: false, error: 'Invalid discount id' });
        }
        const ok = await deleteAdminDiscount(id);
        if (!ok) {
            return res.status(404).json({ success: false, error: 'Discount not found' });
        }
        return res.json({ success: true, deleted: true });
    } catch (err) {
        console.error('ADMIN POST /discounts/:id/delete ERROR', err);
        return res.status(500).json({ success: false, error: 'Failed to delete discount' });
    }
});
// Сумісність з PHP формою: POST /api/admin/products/:id
adminRouter.post('/products/:id', async (req, res) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id) || id <= 0) {
      return res.status(400).json({ success: false, error: 'Invalid product id' });
    }

    const payload = {
      name: req.body.name,
      slug: req.body.slug,
      sku: req.body.sku,
      brand_id: req.body.brand_id != null ? Number(req.body.brand_id) : undefined,
      category_id: req.body.category_id != null ? Number(req.body.category_id) : undefined,
      short_description: req.body.short_description,
      description: req.body.description,
      is_active: req.body.is_active,
    };

    const updated = await updateAdminProduct(id, payload);
    if (!updated) return res.status(404).json({ success: false, error: 'Product not found' });
    return res.json({ success: true, data: updated });
  } catch (err) {
    console.error('ADMIN POST /products/:id ERROR', err);
    return res.status(500).json({ success: false, error: 'Failed to update product' });
  }
});

// POST /api/admin/discounts  (create)
adminRouter.post('/discounts', async (req, res) => {
  try {
    const discount = await createAdminDiscount({
      name: req.body.name,
      description: req.body.description,
      discount_type: req.body.discount_type,
      value: req.body.value,
      date_from: req.body.date_from,
      date_to: req.body.date_to,
      is_active:
        req.body.is_active === true ||
        req.body.is_active === '1' ||
        req.body.is_active === 'on',
      min_order_sum: req.body.min_order_sum,
      code: req.body.code,
    });

    return res.json({ success: true, data: discount });
  } catch (err) {
    console.error('ADMIN POST /discounts ERROR', err);

    // Унікальність (23505)
    if (err.code === '23505') {
      const detail = String(err.detail || '');
      let msg = 'Порушено унікальність даних';

      if (detail.includes('(code)=')) {
        msg = 'Купон з таким кодом вже існує';
      }

      return res.status(409).json({ success: false, error: msg });
    }

    return res.status(500).json({ success: false, error: 'Failed to create discount' });
  }
});
