-- Tabla products
ALTER TABLE products
ADD INDEX idx_products_code (code),
ADD INDEX idx_products_description (description),
ADD INDEX idx_products_section (section),
ADD INDEX idx_products_id (id);
/*
-- Tabla products_stock
ALTER TABLE products_stock
ADD INDEX idx_products_stock_product_sucursal (product, sucursal);

-- Tabla products_prices
ALTER TABLE products_prices
ADD INDEX idx_products_prices_product (product);

-- Tabla products_cost
ALTER TABLE products_cost
ADD INDEX idx_products_cost_product (product);
*/