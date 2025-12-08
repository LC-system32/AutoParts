
import { dbQuery } from "../db/index.js";

/**
 * Побудова WHERE-частини та масиву параметрів для фільтрів продуктів.
 */
function buildProductFilters(filters = {}) {
  const where = ['p.is_active = TRUE'];
  const params = [];
  let idx = 1;

  const {
    search,
    brand,
    category,
    in_stock,
    
    search_type,
    make_id,
    model_id,
    generation_id,
    modification_id,
  } = filters;

  
  if (search) {
    where.push(`(
      p.name ILIKE $${idx}
      OR p.sku ILIKE $${idx}
      OR p.oem_number ILIKE $${idx}
    )`);
    params.push(`%${search}%`);
    idx++;
  }

  
  if (brand) {
    where.push(`b.slug = $${idx}`);
    params.push(brand);
    idx++;
  }

  
  if (category) {
    where.push(`(
      c.slug = $${idx}
      OR parent.slug = $${idx}
    )`);
    params.push(category);
    idx++;
  }

  
  if (in_stock === '1' || in_stock === true) {
    where.push(`EXISTS (
      SELECT 1
      FROM product_offers po2
      WHERE po2.product_id = p.id
        AND po2.is_active = TRUE
        AND po2.quantity > 0
    )`);
  }

  
  if (search_type === 'car') {
    
    if (modification_id) {
      where.push(`EXISTS (
        SELECT 1
        FROM product_fitments pf
        WHERE pf.product_id = p.id
          AND pf.modification_id = $${idx}
      )`);
      params.push(modification_id);
      idx++;
    }
    
    else if (generation_id) {
      where.push(`EXISTS (
        SELECT 1
        FROM product_fitments pf
        JOIN car_modifications cm ON cm.id = pf.modification_id
        WHERE pf.product_id = p.id
          AND cm.generation_id = $${idx}
      )`);
      params.push(generation_id);
      idx++;
    }
    
    else if (model_id) {
      where.push(`EXISTS (
        SELECT 1
        FROM product_fitments pf
        JOIN car_modifications cm   ON cm.id = pf.modification_id
        JOIN car_generations  cg    ON cg.id = cm.generation_id
        WHERE pf.product_id = p.id
          AND cg.model_id = $${idx}
      )`);
      params.push(model_id);
      idx++;
    }
    
    else if (make_id) {
      where.push(`EXISTS (
        SELECT 1
        FROM product_fitments pf
        JOIN car_modifications cm   ON cm.id = pf.modification_id
        JOIN car_generations  cg    ON cg.id = cm.generation_id
        JOIN car_models       cmo   ON cmo.id = cg.model_id
        WHERE pf.product_id = p.id
          AND cmo.make_id = $${idx}
      )`);
      params.push(make_id);
      idx++;
    }
  }

  const whereSql = where.length ? 'WHERE ' + where.join(' AND ') : '';
  return { whereSql, params };
}
/**
 * Побудова ORDER BY для сортування.
 */
function buildSort(sort) {
  switch (sort) {
    case "price_asc":
      return "ORDER BY p.price ASC NULLS LAST";
    case "price_desc":
      return "ORDER BY p.price DESC NULLS LAST";
    case "popular":
      return "ORDER BY p.is_popular DESC, p.created_at DESC";
    case "newest":
    default:
      return "ORDER BY p.created_at DESC";
  }
}

/**
 * Список товарів з фільтрами та пагінацією.
 */
export async function listProducts(filters = {}) {
  const { sort } = filters;

  
  const rawPage = filters.page ?? 1;
  const rawPerPage = filters.perPage ?? filters.per_page ?? 12;

  const page = Number(rawPage) > 0 ? Number(rawPage) : 1;
  const limit = Number(rawPerPage) > 0 ? Number(rawPerPage) : 12;
  const offset = (page - 1) * limit;

  const { whereSql, params } = buildProductFilters(filters);
  const orderBy = buildSort(sort);

  const sql = `
    WITH product_with_price AS (
      SELECT
        p.id,
        p.sku,
        p.oem_number,
        p.name,
        p.slug,
        p.short_desc,
        p.description,
        p.weight_kg,
        p.is_active,
        p.is_popular,
        p.created_at,
        p.updated_at,
        b.id   AS brand_id,
        b.name AS brand_name,
        b.slug AS brand_slug,
        c.id   AS category_id,
        c.name AS category_name,
        c.slug AS category_slug,
        MIN(po.sale_price) AS price,
        MIN(po.base_price) AS base_price,
        COALESCE(
          MAX(CASE WHEN pi.is_main THEN pi.image_url END),
          MAX(pi.image_url)
        ) AS image
      FROM products p
      LEFT JOIN brands      b      ON b.id = p.brand_id
      LEFT JOIN categories  c      ON c.id = p.category_id
      LEFT JOIN categories  parent ON parent.id = c.parent_id
      LEFT JOIN product_offers po  ON po.product_id = p.id AND po.is_active = TRUE
      LEFT JOIN product_images pi  ON pi.product_id = p.id
      ${whereSql}
      GROUP BY
        p.id, b.id, c.id, parent.id
    )
    SELECT
      *
    FROM product_with_price p
    ${orderBy}
    LIMIT $${params.length + 1} OFFSET $${params.length + 2};
  `;

  const sqlParams = [...params, limit, offset];

  const countSql = `
    SELECT COUNT(*) AS total
    FROM products p
    LEFT JOIN brands      b      ON b.id = p.brand_id
    LEFT JOIN categories  c      ON c.id = p.category_id
    LEFT JOIN categories  parent ON parent.id = c.parent_id
    ${whereSql};
  `;

  const [rowsRes, countRes] = await Promise.all([
    dbQuery(sql, sqlParams),
    dbQuery(countSql, params),
  ]);

  const items = rowsRes.rows;
  const total = parseInt(countRes.rows[0]?.total ?? '0', 10) || 0;
  const pages = Math.max(Math.ceil(total / limit), 1);

  return {
    items,
    page,
    perPage: limit,
    total,
    pages,
  };
}

/**
 * Отримати товар по slug.
 */
export async function getProductBySlug(slug) {
  const sql = `
    SELECT
      p.id,
      p.sku,
      p.slug,
      p.name,
      p.short_desc,
      p.description,
      p.brand_id,
      b.name  AS brand_name,
      p.category_id,
      c.name  AS category_name,
      MIN(po.sale_price) AS price,
      MIN(po.base_price) AS base_price,
      CASE
        WHEN COALESCE(SUM(CASE WHEN po.quantity > 0 THEN 1 ELSE 0 END), 0) > 0
          THEN TRUE
        ELSE FALSE
      END AS in_stock,
      COALESCE(
        MAX(CASE WHEN pi.is_main THEN pi.image_url END),
        MAX(pi.image_url)
      ) AS image,
      COALESCE(
        json_agg(
          json_build_object(
            'url',        pi.image_url,
            'is_main',    pi.is_main,
            'sort_order', pi.sort_order
          )
        ) FILTER (WHERE pi.id IS NOT NULL),
        '[]'::json
      ) AS images
    FROM products p
    LEFT JOIN brands b
      ON b.id = p.brand_id
    LEFT JOIN categories c
      ON c.id = p.category_id
    LEFT JOIN product_offers po
      ON po.product_id = p.id
     AND po.is_active = TRUE
    LEFT JOIN product_images pi
      ON pi.product_id = p.id
    WHERE p.slug = $1
    GROUP BY
      p.id,
      p.sku,
      p.slug,
      p.name,
      p.short_desc,
      p.description,
      p.brand_id,
      b.name,
      p.category_id,
      c.name
    LIMIT 1
  `;

  const { rows } = await dbQuery(sql, [slug]);
  return rows[0] || null;
}

/**
 * Пропозиції (offers) для товару.
 */
export async function getProductOffers(productId, sort = "cheapest") {
  let orderBy = "";
  switch (sort) {
    case "fastest":
      orderBy = "ORDER BY po.delivery_days ASC NULLS LAST, po.sale_price ASC";
      break;
    case "city":
      orderBy = "ORDER BY w.city ASC NULLS LAST, po.sale_price ASC";
      break;
    case "cheapest":
    default:
      orderBy = "ORDER BY po.sale_price ASC, po.delivery_days ASC NULLS LAST";
      break;
  }

  const sql = `
    SELECT
      po.id     AS offer_id,
      w.name    AS warehouse,
      w.city    AS city,
      s.name    AS supplier,
      po.quantity::TEXT AS quantity,
      po.sale_price::TEXT,
      po.base_price::TEXT,
      po.delivery_days
    FROM product_offers po
    JOIN warehouses w ON w.id = po.warehouse_id
    LEFT JOIN suppliers s ON s.id = w.supplier_id
    WHERE po.product_id = $1
      AND po.is_active = TRUE
    ${orderBy}
  `;
  const { rows } = await dbQuery(sql, [productId]);
  return rows;
}

/**
 * Сумісності товару з авто.
 */
export async function getFitmentsForProduct(productId) {
  const sql = `
    SELECT
      mk.name  AS make,
      md.name  AS model,
      gen.name AS generation,
      gen.year_from AS generation_year_from,
      gen.year_to   AS generation_year_to,
      mod.engine_code AS engine,
      mod.year_from   AS modification_year_from,
      mod.year_to     AS modification_year_to,
      mod.fuel_type   AS fuel_type,
      mod.transmission AS transmission,
      mod.id AS modification_id
    FROM product_fitments pf
    JOIN car_modifications mod   ON mod.id = pf.modification_id
    JOIN car_generations gen     ON gen.id = mod.generation_id
    JOIN car_models md           ON md.id = gen.model_id
    JOIN car_makes mk            ON mk.id = md.make_id
    WHERE pf.product_id = $1
    ORDER BY mk.name, md.name, gen.name, mod.id
  `;
  const { rows } = await dbQuery(sql, [productId]);
  return rows.map((r) => {
    let years = "";
    if (r.modification_year_from && r.modification_year_to) {
      years = `${r.modification_year_from}-${r.modification_year_to}`;
    } else if (r.modification_year_from) {
      years = `${r.modification_year_from}-`;
    } else if (r.modification_year_to) {
      years = `-${r.modification_year_to}`;
    }
    return {
      make: r.make,
      model: r.model,
      generation: r.generation,
      generation_year_from: r.generation_year_from,
      generation_year_to: r.generation_year_to,
      modification: null,
      modification_year_from: r.modification_year_from,
      modification_year_to: r.modification_year_to,
      years,
      engine: r.engine,
      fuel_type: r.fuel_type,
      transmission: r.transmission,
      modification_id: r.modification_id,
    };
  });
}

/**
 * Отримати відгуки товару (тільки схвалені).
 */
export async function getProductReviews(productId) {
  const pid = Number(productId);
  if (!Number.isInteger(pid) || pid <= 0) {
    throw new Error("Invalid product id");
  }

  const { rows } = await dbQuery(
    `SELECT
       pr.id,
       pr.product_id,
       pr.user_id,
       pr.rating,
       pr.title,
       pr.body,
       pr.is_approved,
       pr.created_at,
       COALESCE(
         NULLIF(TRIM(CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,''))), ''),
         u.email,
         u.login
       ) AS user_name
     FROM product_reviews pr
     LEFT JOIN users u ON u.id = pr.user_id
     WHERE pr.product_id = $1
       AND pr.is_approved = TRUE
     ORDER BY pr.created_at DESC`,
    [pid]
  );

  return rows;
}

/**
 * Alias для сумісності зі старим кодом (якщо десь використовується listReviews).
 */
export async function listReviews(productId) {
  return getProductReviews(productId);
}

/**
 * Створити відгук (нова функція).
 */
export async function createProductReview(productId, { userId, rating, title, body }) {
  const pid = Number(productId);
  console.log("createProductReview() INPUT:", {
    productId: pid,
    userId,
    rating,
    title,
    body,
  });

  if (!Number.isInteger(pid) || pid <= 0) {
    console.warn("createProductReview: invalid product id:", productId);
    throw new Error("Invalid product id");
  }

  const r = Number(rating) || 0;
  if (!Number.isFinite(r) || r < 1 || r > 5) {
    console.warn("createProductReview: invalid rating:", rating);
    throw new Error("Rating must be between 1 and 5");
  }

  const text = String(body || "").trim();
  if (!text) {
    console.warn("createProductReview: empty body");
    throw new Error("Review body is required");
  }

  const ttl = String(title || "").trim() || null;

  try {
    const { rows } = await dbQuery(
      `INSERT INTO product_reviews (product_id, user_id, rating, title, body, is_approved)
       VALUES ($1, $2, $3, $4, $5, FALSE)
       RETURNING id, product_id, user_id, rating, title, body, is_approved, created_at`,
      [pid, userId || null, r, ttl, text]
    );

    console.log("createProductReview: inserted row:", rows[0]);
    return rows[0];
  } catch (err) {
    console.error("createProductReview: SQL ERROR:", err);
    throw err;
  }
}

/**
 * createReview – обгортка для старого коду (якщо десь ще імпортується createReview).
 * Підтримує 2 варіанти виклику:
 *   createReview(productId, userId, { rating, title, body })
 *   createReview(productId, userId, rating, comment)
 */
export async function createReview(productId, userId, ratingOrPayload, maybeComment) {
  
  if (ratingOrPayload && typeof ratingOrPayload === "object") {
    const { rating, title, body } = ratingOrPayload;
    return createProductReview(productId, {
      userId,
      rating,
      title,
      body,
    });
  }

  
  const rating = ratingOrPayload;
  const body = maybeComment;
  return createProductReview(productId, {
    userId,
    rating,
    title: null,
    body,
  });
}
