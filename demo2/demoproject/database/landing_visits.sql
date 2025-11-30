CREATE TABLE IF NOT EXISTS landing_visits (
  id INT PRIMARY KEY AUTO_INCREMENT,
  visit_count INT NOT NULL DEFAULT 0,
  last_visited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ip_address VARCHAR(45) DEFAULT NULL
);

-- Insert initial visits row if not exists
INSERT INTO landing_visits (id, visit_count, last_visited) 
VALUES (1, 0, NOW())
ON DUPLICATE KEY UPDATE id=id;
