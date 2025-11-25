ALTER TABLE codegen
  ADD COLUMN student_status ENUM('active','deactivated') NOT NULL DEFAULT 'active' AFTER code;
