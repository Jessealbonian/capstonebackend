-- Database tables for routines functionality

-- Classes table
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    coach_username VARCHAR(100) NOT NULL,
    sport VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Class enrollments table
CREATE TABLE IF NOT EXISTS class_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    student_username VARCHAR(100) NOT NULL,
    enrollment_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive', 'Completed') DEFAULT 'Active',
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (class_id, student_username)
);

-- Class routines table
CREATE TABLE IF NOT EXISTS class_routines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    task_title VARCHAR(255) NOT NULL,
    task_description TEXT,
    due_date DATE NOT NULL,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Routine history table
CREATE TABLE IF NOT EXISTS routine_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    routine_id INT NOT NULL,
    user_id INT NOT NULL,
    completion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_image VARCHAR(255),
    status ENUM('Completed', 'Pending') DEFAULT 'Completed',
    FOREIGN KEY (routine_id) REFERENCES class_routines(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_daily_routine (routine_id, user_id, DATE(completion_date))
);

-- Update codegen table to include student_redeemer and redemption_time
ALTER TABLE codegen 
ADD COLUMN IF NOT EXISTS student_redeemer VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS redemption_time TIMESTAMP NULL;

-- Insert sample data for testing
INSERT INTO classes (title, coach_username, sport, description) VALUES
('Basketball Fundamentals', 'coach_john', 'Basketball', 'Learn the basics of basketball including dribbling, shooting, and passing'),
('Soccer Training', 'coach_sarah', 'Soccer', 'Improve your soccer skills with drills and practice sessions'),
('Swimming Lessons', 'coach_mike', 'Swimming', 'Master swimming techniques and build endurance');

-- Insert sample routines
INSERT INTO class_routines (class_id, task_title, task_description, due_date) VALUES
(1, 'Dribbling Practice', 'Practice dribbling with both hands for 30 minutes', '2024-01-15'),
(1, 'Shooting Drills', 'Complete 50 free throws and 50 three-point shots', '2024-01-16'),
(1, 'Passing Practice', 'Practice chest passes and bounce passes with a partner', '2024-01-17'),
(2, 'Ball Control', 'Practice ball control exercises for 45 minutes', '2024-01-15'),
(2, 'Shooting Practice', 'Practice shooting on goal from different angles', '2024-01-16'),
(3, 'Freestyle Stroke', 'Practice freestyle stroke technique for 30 minutes', '2024-01-15'),
(3, 'Breathing Exercise', 'Practice breathing rhythm while swimming', '2024-01-16');
