CREATE TABLE IF NOT EXISTS landing_visits (
  id INT PRIMARY KEY AUTO_INCREMENT,
  visit_count INT NOT NULL DEFAULT 0,
  last_visited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ip_address VARCHAR(45) DEFAULT NULL
);

-- Insert initial visits row if not exists
INSERT INTO landing_visits (visit_count) SELECT 0 WHERE NOT EXISTS (SELECT 1 FROM landing_visits WHERE id = 1);
