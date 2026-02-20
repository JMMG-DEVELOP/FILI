-- ================= PRODUCTS =================
ALTER TABLE products
ADD INDEX idx_products_code (code),
ADD INDEX idx_products_description (description),
ADD INDEX idx_products_code_description (code, description),
ADD INDEX idx_products_section (section),
ADD INDEX idx_products_brand (brand),
ADD INDEX idx_products_sales (sales),
ADD INDEX idx_products_iva (iva);

-- ================= PRICES =================
ALTER TABLE products_prices
ADD INDEX idx_prices_product (product);

-- ================= COST =================
ALTER TABLE products_cost
ADD INDEX idx_cost_product (product);

-- ================= STOCK =================
ALTER TABLE products_stock
ADD INDEX idx_stock_product (product),
ADD INDEX idx_stock_product_sucursal (product, sucursal);

-- ================= USER_SUCURSALS =================
ALTER TABLE users_sucursals
ADD INDEX idx_users_sucursal (user, sucursal);

