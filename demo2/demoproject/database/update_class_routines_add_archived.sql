ALTER TABLE class_routines
  ADD COLUMN archived TINYINT(1) NOT NULL DEFAULT 0 AFTER expiration_date;

-- Set archived=1 for any class with expiration_date < NOW()
UPDATE class_routines SET archived=1 WHERE expiration_date IS NOT NULL AND expiration_date < NOW();
