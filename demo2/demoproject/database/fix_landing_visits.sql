-- Quick fix: Ensure landing_visits table has the initial row
-- Run this if the table is empty

INSERT INTO landing_visits (id, visit_count, last_visited) 
VALUES (1, 0, NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Verify the row exists
SELECT * FROM landing_visits WHERE id = 1;

