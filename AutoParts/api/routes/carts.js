// API/routes/carts.js
import express from "express";
import {
    createCart,
    getCart,
    addItem,
    updateItem,
    removeItem,
    clearCart,
    verifyCouponForTotal,applyCouponToCart,
    verifyCouponForCartId,
} from "../models/cartModel.js";

export const cartsRouter = express.Router();

/**
 * POST /api/carts/discounts/verify
 * Публічний (пропускається authenticate-ом через allowlist).
 * Якщо передано cart_id → бекенд сам рахує суму;
 * інакше приймає cart_total.
 */
// Публічний ендпоінт перевірки купона
cartsRouter.post("/discounts/verify", async (req, res) => {
  try {
    const { code, cart_total, cart_id } = req.body || {};
    const cid = Number.parseInt(cart_id, 10);

    const result =
      Number.isFinite(cid) && cid > 0
        ? await verifyCouponForCartId(code, cid)
        : await verifyCouponForTotal(code, cart_total);

    if (!result.valid) {
      console.log(
        "Invalid coupon verification attempt:",
        code,
        cart_total,
        cart_id,
        "reason:",
        result.error
      );
      return res.json({
        valid: false,
        error: result.error || "Купон недійсний",
      });
    }

    return res.json({ valid: true, data: result.data });
  } catch (e) {
    console.error("SHOP POST /api/carts/discounts/verify ERROR", e);
    return res.status(500).json({
      valid: false,
      error: "Failed to verify coupon",
    });
  }
});


/**
 * Далі — захищені маршрути кошика (глобальний authenticate уже застосований)
 */

// POST /api/carts
cartsRouter.post("/", async (req, res) => {
    try {
        const userId = req.user?.id ?? null;
        const sessionToken =
            req.body.sessionToken ??
            req.body.session_id ??
            req.cookies?.sessionToken ??
            null;

        const cart = await createCart({ userId, sessionToken });
        res.status(201).json({ success: true, data: cart });
    } catch (err) {
        console.error('POST /api/carts ERROR', err);
        res.status(500).json({ success: false, error: "Failed to create cart" });
    }
});

// GET /api/carts/:id
cartsRouter.get("/:id", async (req, res) => {
    const cartId = Number.parseInt(req.params.id, 10);
    if (!Number.isFinite(cartId) || cartId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid cart id" });
    }

    try {
        const cart = await getCart(cartId);
        if (!cart) {
            return res.status(404).json({ success: false, error: "Cart not found" });
        }
        res.json({ success: true, data: cart });
    } catch (err) {
        console.error('GET /api/carts/:id ERROR', err);
        res.status(500).json({ success: false, error: "Failed to fetch cart" });
    }
});

// POST /api/carts/:id/items
cartsRouter.post("/:id/items", async (req, res) => {
    const cartId = Number.parseInt(req.params.id, 10);
    const { product_id, productId, quantity } = req.body;

    const pid = Number.parseInt(product_id ?? productId, 10);
    const qty = Number.parseInt(quantity, 10);

    if (!Number.isFinite(cartId) || cartId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid cart id" });
    }
    if (!Number.isFinite(pid) || pid <= 0 || !Number.isFinite(qty) || qty <= 0) {
        return res.status(400).json({
            success: false,
            error: "Missing or invalid product_id / quantity",
        });
    }

    try {
        const cart = await getCart(cartId);
        if (!cart) {
            return res.status(404).json({ success: false, error: "Cart not found" });
        }

        const item = await addItem(cartId, pid, qty);
        const updatedCart = await getCart(cartId);

        res.status(201).json({
            success: true,
            data: { cart: updatedCart, item },
        });
    } catch (err) {
        console.error('POST /api/carts/:id/items ERROR', err);
        res.status(500).json({
            success: false,
            error: err.message || "Failed to add item",
        });
    }
});

// PATCH /api/carts/:id/items/:itemId
cartsRouter.patch("/:id/items/:itemId", async (req, res) => {
    const cartId = Number.parseInt(req.params.id, 10);
    const itemId = Number.parseInt(req.params.itemId, 10);
    const qty = Number.parseInt(req.body.quantity, 10);

    if (!Number.isFinite(cartId) || cartId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid cart id" });
    }
    if (!Number.isFinite(itemId) || itemId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid item id" });
    }
    if (!Number.isFinite(qty) || qty <= 0) {
        return res.status(400).json({ success: false, error: "Missing or invalid quantity" });
    }

    try {
        const item = await updateItem(cartId, itemId, qty);
        res.json({ success: true, data: item });
    } catch (err) {
        console.error('PATCH /api/carts/:id/items/:itemId ERROR', err);
        res.status(500).json({ success: false, error: "Failed to update item" });
    }
});

// DELETE /api/carts/:id/items/:itemId
cartsRouter.delete("/:id/items/:itemId", async (req, res) => {
    const cartId = Number.parseInt(req.params.id, 10);
    const itemId = Number.parseInt(req.params.itemId, 10);

    if (!Number.isFinite(cartId) || cartId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid cart id" });
    }
    if (!Number.isFinite(itemId) || itemId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid item id" });
    }

    try {
        await removeItem(cartId, itemId);
        res.json({ success: true, data: { id: itemId } });
    } catch (err) {
        console.error('DELETE /api/carts/:id/items/:itemId ERROR', err);
        res.status(500).json({ success: false, error: "Failed to remove item" });
    }
});

// DELETE /api/carts/:id/items
cartsRouter.delete("/:id/items", async (req, res) => {
    const cartId = Number.parseInt(req.params.id, 10);

    if (!Number.isFinite(cartId) || cartId <= 0) {
        return res.status(400).json({ success: false, error: "Invalid cart id" });
    }

    try {
        await clearCart(cartId);
        res.json({ success: true, data: { id: cartId } });
    } catch (err) {
        console.error('DELETE /api/carts/:id/items ERROR', err);
        res.status(500).json({ success: false, error: "Failed to clear cart" });
    }
});

/**
 * (Опційно) POST /api/carts/:id/discounts/verify
 * Перевірка купона для конкретного кошика (бекенд сам рахує суму)
 */
cartsRouter.post("/:id/discounts/verify", async (req, res) => {
    const cartId = Number.parseInt(req.params.id, 10);
    const code = (req.body?.code ?? "").toString();

    if (!Number.isFinite(cartId) || cartId <= 0) {
        return res.status(400).json({ valid: false, error: "Invalid cart id" });
    }

    try {
        const result = await verifyCouponForCartId(code, cartId);
        if (!result.valid) {
            return res.status(400).json({ valid: false, error: result.error || "Invalid coupon" });
        }
        console.log("Valid coupon verification:", result.data);
        return res.json({ valid: true, data: result.data });
    } catch (err) {
        console.error("CART POST /:id/discounts/verify ERROR", err);
        res.status(500).json({ valid: false, error: "Failed to verify coupon for cart" });
    }
});

cartsRouter.post("/:id/discounts/apply", async (req, res) => {
  const cartId = Number.parseInt(req.params.id, 10);
  const { code } = req.body || {};

  if (!Number.isFinite(cartId) || cartId <= 0) {
    return res
      .status(400)
      .json({ success: false, error: "Invalid cart id" });
  }

  try {
    const cart = await applyCouponToCart(cartId, code);
    return res.json({ success: true, data: cart });
  } catch (err) {
    console.error("POST /api/carts/:id/discounts/apply ERROR", err);
    return res.status(400).json({
      success: false,
      error: err.message || "Failed to apply coupon",
    });
  }
});
