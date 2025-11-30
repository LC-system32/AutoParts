// file: API/routes/products.js
import express from "express";
import {
  listProducts,
  getProductBySlug,
  getProductReviews,
  createProductReview,
  getFitmentsForProduct,
  getProductOffers,
} from "../models/productModel.js";
import { authenticate, requireAuth } from "../middlewares/auth.js";

export const productsRouter = express.Router();

// підключаємо authenticate до всіх роутів продуктів
productsRouter.use(authenticate);

// Глобальний логер для дебагу
productsRouter.use((req, res, next) => {
  console.log("=== [PRODUCTS API]", req.method, req.originalUrl, "===");
  console.log("  user:", req.user ? { id: req.user.id, email: req.user.email } : null);
  console.log("  body:", req.body);
  next();
});

// GET /api/products
productsRouter.get('/', async (req, res) => {
  try {
    const {
      q,
      brand,
      category,
      sort,
      in_stock,
      page = '1',
      per_page = '12',

      // 🔧 додаємо car-параметри
      search_type,
      make_id,
      model_id,
      generation_id,
      modification_id,
    } = req.query;

    const filters = {
      search: q || '',
      brand: brand || '',
      category: category || '',
      sort: sort || '',
      in_stock,
      page: Number(page) || 1,
      perPage: Number(per_page) || 12,
    };

    // 🔧 якщо це підбір по авто — додаємо спец. фільтри
    if (search_type === 'car') {
      filters.search_type = 'car';

      if (make_id)         filters.make_id         = Number(make_id);
      if (model_id)        filters.model_id        = Number(model_id);
      if (generation_id)   filters.generation_id   = Number(generation_id);
      if (modification_id) filters.modification_id = Number(modification_id);
    }

    const data = await listProducts(filters);
    res.json(data);
  } catch (err) {
    console.error('GET /api/products error:', err);
    res.status(500).json({ error: 'Failed to load products' });
  }
});

// GET /api/products/:id/reviews
productsRouter.get("/:id/reviews", async (req, res) => {
  const productId = Number(req.params.id);
  console.log("GET /api/products/:id/reviews -> productId =", productId);

  if (!Number.isFinite(productId) || productId <= 0) {
    console.warn("GET /:id/reviews invalid id:", req.params.id);
    return res.status(400).json({ success: false, error: "Invalid product id" });
  }

  try {
    const reviews = await getProductReviews(productId);
    return res.json({ success: true, data: reviews });
  } catch (err) {
    console.error("GET /api/products/:id/reviews ERROR:", err);
    return res.status(500).json({ success: false, error: "Failed to fetch reviews" });
  }
});

// POST /api/products/:id/reviews (auth required)
productsRouter.post("/:id/reviews", requireAuth, async (req, res) => {
  const productId = Number(req.params.id);
  console.log("POST /api/products/:id/reviews -> productId =", productId);
  console.log("POST body =", req.body);

  if (!Number.isFinite(productId) || productId <= 0) {
    console.warn("POST /:id/reviews invalid id:", req.params.id);
    return res.status(400).json({ success: false, error: "Invalid product id" });
  }

  const userId = req.user?.id || null;
  // Приймаємо як body, так і comment – щоб було сумісно з різними клієнтами
  const {
    rating,
    title,
    body,
    comment,
  } = req.body || {};

  const text = (body ?? comment ?? "").toString().trim();
  const r = Number(rating) || 0;

  if (!Number.isFinite(r) || r < 1 || r > 5) {
    console.warn("POST /:id/reviews invalid rating:", rating);
    return res
      .status(400)
      .json({ success: false, error: "Rating must be between 1 and 5" });
  }

  if (!text) {
    console.warn("POST /:id/reviews empty body/comment");
    return res
      .status(400)
      .json({ success: false, error: "Review body is required" });
  }

  try {
    const review = await createProductReview(productId, {
      userId,
      rating: r,
      title: title ? String(title).trim() : null,
      body: text,
    });

    console.log("POST /:id/reviews created review id =", review?.id);
    return res.status(201).json({ success: true, data: review });
  } catch (err) {
    console.error("POST /api/products/:id/reviews SQL ERROR:", err);
    return res
      .status(500)
      .json({ success: false, error: "Failed to create review" });
  }
});

// GET /api/products/:id/fitments
productsRouter.get("/:id/fitments", async (req, res) => {
  const productId = Number(req.params.id);
  console.log("GET /api/products/:id/fitments -> productId =", productId);

  if (!Number.isFinite(productId) || productId <= 0) {
    return res.status(400).json({ success: false, error: "Invalid product id" });
  }

  try {
    const fitments = await getFitmentsForProduct(productId);
    return res.json({ success: true, data: fitments });
  } catch (err) {
    console.error("GET /api/products/:id/fitments ERROR:", err);
    return res
      .status(500)
      .json({ success: false, error: "Failed to fetch fitments" });
  }
});

// GET /api/products/:id/offers
productsRouter.get("/:id/offers", async (req, res) => {
  const productId = Number(req.params.id);
  console.log("GET /api/products/:id/offers -> productId =", productId);

  if (!Number.isFinite(productId) || productId <= 0) {
    return res.status(400).json({ success: false, error: "Invalid product id" });
  }

  const sort = (req.query.sort || "").toString().toLowerCase();

  try {
    const offers = await getProductOffers(productId, sort);
    return res.json({ success: true, data: offers });
  } catch (err) {
    console.error("GET /api/products/:id/offers ERROR:", err);
    return res
      .status(500)
      .json({ success: false, error: "Failed to fetch offers" });
  }
});

// ВАЖЛИВО: останній маршрут – за slug
// GET /api/products/:slug
productsRouter.get("/:slug", async (req, res) => {
  const { slug } = req.params;
  console.log("GET /api/products/:slug -> slug =", slug);

  try {
    const product = await getProductBySlug(slug);

    if (!product) {
      return res.status(404).json({ success: false, error: "Product not found" });
    }
    return res.json({ success: true, data: product });
  } catch (err) {
    console.error("GET /api/products/:slug ERROR:", err);
    return res.status(500).json({ success: false, error: "Failed to fetch product" });
  }
});
