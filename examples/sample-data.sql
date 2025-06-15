-- Sample Data for Aeris Dropship Management System
-- This file contains sample data to help you get started with the application
-- Run this after setting up your database with the schema.sql file

-- Insert sample users
INSERT INTO users (username, email, password, name, security_question_1, security_answer_1, security_question_2, security_answer_2) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'What is your favorite color?', 'blue', 'What city were you born in?', 'newyork'),
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', 'What is your pet''s name?', 'fluffy', 'What is your mother''s maiden name?', 'smith');

-- Insert sample products
INSERT INTO products (name, description, purchase_price, selling_price, stock_quantity, supplier_id, sku, category) VALUES
('Wireless Bluetooth Headphones', 'High-quality wireless headphones with noise cancellation', 45.00, 89.99, 25, 1, 'WBH-001', 'Electronics'),
('Smart Fitness Tracker', 'Advanced fitness tracker with heart rate monitoring', 35.00, 79.99, 15, 2, 'SFT-002', 'Fitness'),
('Portable Phone Charger', '10000mAh portable battery pack with fast charging', 12.00, 29.99, 50, 1, 'PPC-003', 'Electronics'),
('Yoga Mat Premium', 'Non-slip yoga mat with carrying strap', 18.00, 39.99, 20, 3, 'YMP-004', 'Fitness'),
('Bluetooth Speaker', 'Waterproof portable speaker with 12-hour battery', 25.00, 59.99, 30, 1, 'BTS-005', 'Electronics');

-- Insert sample suppliers
INSERT INTO suppliers (name, contact_person, email, phone, address, notes) VALUES
('TechSupply Co', 'John Smith', 'john@techsupply.com', '+1-555-0123', '123 Tech Street, Silicon Valley, CA 94000', 'Reliable electronics supplier'),
('FitGear Wholesale', 'Sarah Johnson', 'sarah@fitgear.com', '+1-555-0456', '456 Fitness Ave, Austin, TX 78701', 'Specializes in fitness equipment'),
('Global Imports Ltd', 'Mike Chen', 'mike@globalimports.com', '+1-555-0789', '789 Import Blvd, Miami, FL 33101', 'International supplier for various categories');

-- Insert sample orders
INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, product_id, quantity, total_amount, status, order_date, notes) VALUES
('Alice Johnson', 'alice@email.com', '+1-555-1001', '123 Main St, Anytown, USA 12345', 1, 2, 179.98, 'delivered', '2024-01-15 10:30:00', 'Customer requested expedited shipping'),
('Bob Wilson', 'bob@email.com', '+1-555-1002', '456 Oak Ave, Somewhere, USA 67890', 3, 1, 29.99, 'shipped', '2024-01-18 14:20:00', 'Standard shipping'),
('Carol Davis', 'carol@email.com', '+1-555-1003', '789 Pine Rd, Elsewhere, USA 54321', 2, 1, 79.99, 'processing', '2024-01-20 09:15:00', 'Waiting for stock confirmation'),
('David Brown', 'david@email.com', '+1-555-1004', '321 Elm St, Anywhere, USA 98765', 4, 2, 79.98, 'pending', '2024-01-22 16:45:00', 'Payment pending verification'),
('Emma Martinez', 'emma@email.com', '+1-555-1005', '654 Maple Dr, Someplace, USA 13579', 5, 3, 179.97, 'delivered', '2024-01-25 11:00:00', 'Customer very satisfied');

-- Insert sample supplier orders
INSERT INTO supplier_orders (supplier_id, product_id, quantity, unit_cost, total_cost, status, order_date, expected_delivery, notes) VALUES
(1, 1, 50, 45.00, 2250.00, 'delivered', '2024-01-10 09:00:00', '2024-01-15', 'Bulk order for headphones'),
(2, 2, 30, 35.00, 1050.00, 'shipped', '2024-01-12 11:30:00', '2024-01-20', 'Fitness tracker restock'),
(1, 3, 100, 12.00, 1200.00, 'delivered', '2024-01-14 15:20:00', '2024-01-18', 'Phone charger bulk order'),
(3, 4, 40, 18.00, 720.00, 'processing', '2024-01-16 13:45:00', '2024-01-25', 'Yoga mat order processing'),
(1, 5, 60, 25.00, 1500.00, 'pending', '2024-01-20 10:15:00', '2024-01-28', 'Bluetooth speaker order placed');

-- Note: Default password for sample users is 'password'
-- Please change these passwords in production!
