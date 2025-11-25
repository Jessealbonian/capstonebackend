-- 1. Landing Visits Table
CREATE TABLE IF NOT EXISTS landing_visits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  visit_count INT DEFAULT 0,
  last_visited DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Class Expiration/Archiving
ALTER TABLE class_routines 
  ADD COLUMN IF NOT EXISTS expiration_date DATETIME DEFAULT NULL, 
  ADD COLUMN IF NOT EXISTS archived TINYINT(1) DEFAULT 0;

-- 3. codegen student_status
ALTER TABLE codegen 
  ADD COLUMN IF NOT EXISTS student_status ENUM('active','deactivated') DEFAULT 'active';

-- 4. kickhistory Table
CREATE TABLE IF NOT EXISTS kickhistory (
  idkickhistory INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  class_id INT NOT NULL,
  user_id INT NOT NULL,
  reason VARCHAR(255),
  kicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (class_id) REFERENCES class_routines(class_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES hoa_users(user_id) ON DELETE CASCADE
);

-- 5. routine_history Reflection/Coach Response
ALTER TABLE routine_history 
  ADD COLUMN IF NOT EXISTS student_reflection TEXT,
  ADD COLUMN IF NOT EXISTS coach_response TEXT,
  ADD COLUMN IF NOT EXISTS time_of_submission TIME AFTER date_of_submission;

-- Index/archive utility
UPDATE class_routines SET archived=1 WHERE expiration_date IS NOT NULL AND expiration_date < NOW();
