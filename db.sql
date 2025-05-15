-- Create the database
CREATE DATABASE IF NOT EXISTS print_shop_management;
USE print_shop_management;

-- Customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    preferences JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    reference VARCHAR(50) UNIQUE NOT NULL,
    delivery_date DATE,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('received', 'in_preparation', 'in_printing', 'in_finishing', 'ready_for_delivery', 'delivered', 'canceled') DEFAULT 'received',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Production steps table
CREATE TABLE production_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    step ENUM('prepress', 'printing', 'finishing', 'quality_check', 'packaging', 'shipping') NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'on_hold', 'failed') DEFAULT 'pending',
    assigned_to VARCHAR(100),
    start_time DATETIME,
    end_time DATETIME,
    comments TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Files table
CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Materials table (additional)
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('paper', 'ink', 'plate', 'chemical', 'other') NOT NULL,
    stock_quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    min_stock_level DECIMAL(10,2),
    cost_per_unit DECIMAL(10,2)
) ENGINE=InnoDB;

-- Order materials (junction table)
CREATE TABLE order_materials (
    order_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity_used DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_id, material_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Create indexes for better performance
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_priority ON orders(priority);
CREATE INDEX idx_production_steps_order ON production_steps(order_id);
CREATE INDEX idx_production_steps_status ON production_steps(status);