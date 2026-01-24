-- Database Optimization for Product Search (23K Products)
-- Run these commands in your MySQL database to improve search performance

-- 1. Add indexes on products table
ALTER TABLE products ADD INDEX idx_business_product_name (business_id, productName);
ALTER TABLE products ADD INDEX idx_business_product_code (business_id, productCode);
ALTER TABLE products ADD INDEX idx_business_created (business_id, created_at);

-- 2. Add indexes on related tables
ALTER TABLE categories ADD INDEX idx_category_name (categoryName);
ALTER TABLE units ADD INDEX idx_unit_name (unitName);

-- 3. Add indexes on stocks table for price searches
ALTER TABLE stocks ADD INDEX idx_product_prices (product_id, sales_price, dealer_price);
ALTER TABLE stocks ADD INDEX idx_product_stock (product_id, productStock);

-- 4. Optional: Full-text search (for even better performance)
-- Uncomment if you want to implement full-text search later
-- ALTER TABLE products ADD FULLTEXT idx_fulltext_search (productName, productCode);

-- 5. Check existing indexes
SHOW INDEX FROM products;
SHOW INDEX FROM categories;
SHOW INDEX FROM units;
SHOW INDEX FROM stocks;

-- 6. Analyze tables to update statistics
ANALYZE TABLE products;
ANALYZE TABLE categories;
ANALYZE TABLE units;
ANALYZE TABLE stocks;
