-- Game Plaza database schema and sample data

CREATE DATABASE IF NOT EXISTS game_plaza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE game_plaza;

DROP TABLE IF EXISTS compatibility_scores;
DROP TABLE IF EXISTS likes_follows;
DROP TABLE IF EXISTS match_participants;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS user_preferences;
DROP TABLE IF EXISTS user_games;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  bio TEXT,
  skill_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Pro') DEFAULT 'Beginner',
  availability VARCHAR(100),
  profile_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE games (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL UNIQUE,
  genre VARCHAR(50),
  developer VARCHAR(100),
  release_year INT,
  is_multiplayer BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE user_games (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  game_id INT NOT NULL,
  platform ENUM('Steam', 'Epic Games', 'Blizzard', 'Riot Games', 'Other') DEFAULT 'Steam',
  playtime_hours INT DEFAULT 0,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_game (user_id, game_id)
) ENGINE=InnoDB;

CREATE TABLE user_preferences (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL UNIQUE,
  preferred_genres VARCHAR(200),
  preferred_playstyle VARCHAR(100),
  competitive_preference ENUM('Casual', 'Competitive', 'Mixed') DEFAULT 'Mixed',
  team_size_preference ENUM('Solo', 'Duo', 'Squad', 'Large') DEFAULT 'Squad',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE matches (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  game_id INT NOT NULL,
  creator_id INT NOT NULL,
  match_type ENUM('Casual', 'Tournament', 'Ranked') DEFAULT 'Casual',
  max_participants INT DEFAULT 4,
  status ENUM('Pending', 'Active', 'Completed', 'Cancelled') DEFAULT 'Pending',
  scheduled_time DATETIME,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (game_id) REFERENCES games(id),
  FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE match_participants (
  id INT PRIMARY KEY AUTO_INCREMENT,
  match_id INT NOT NULL,
  user_id INT NOT NULL,
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('Pending', 'Confirmed', 'Declined', 'Left') DEFAULT 'Pending',
  FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_match_user (match_id, user_id)
) ENGINE=InnoDB;

CREATE TABLE likes_follows (
  id INT PRIMARY KEY AUTO_INCREMENT,
  follower_id INT NOT NULL,
  followee_id INT NOT NULL,
  action_type ENUM('Like', 'Follow') DEFAULT 'Follow',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (followee_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_follow (follower_id, followee_id)
) ENGINE=InnoDB;

CREATE TABLE compatibility_scores (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_a_id INT NOT NULL,
  user_b_id INT NOT NULL,
  score FLOAT DEFAULT 0,
  shared_games INT DEFAULT 0,
  calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_a_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (user_b_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_compatibility (user_a_id, user_b_id)
) ENGINE=InnoDB;

-- Sample users using the same placeholder hash for password123
INSERT INTO users (username, email, password_hash, bio, skill_level, availability) VALUES
('playerone', 'playerone@example.com', '$2y$10$e0NR3QYqLKWLkta.lU2SJeUg2AQQgL7omMt74O5bGCYihs/1F/KEW', 'Competitive FPS player who loves organizing squads.', 'Advanced', 'Evenings'),
('arcadequeen', 'arcadequeen@example.com', '$2y$10$e0NR3QYqLKWLkta.lU2SJeUg2AQQgL7omMt74O5bGCYihs/1F/KEW', 'Casual gamer who enjoys co-op adventures.', 'Intermediate', 'Weekends'),
('riotchamp', 'riotchamp@example.com', '$2y$10$e0NR3QYqLKWLkta.lU2SJeUg2AQQgL7omMt74O5bGCYihs/1F/KEW', 'MOBA specialist focused on strategy and teamplay.', 'Pro', 'Afternoons'),
('epicgamer', 'epicgamer@example.com', '$2y$10$e0NR3QYqLKWLkta.lU2SJeUg2AQQgL7omMt74O5bGCYihs/1F/KEW', 'Looking for new squad members and fun tournaments.', 'Intermediate', 'Mornings'),
('blizzardhero', 'blizzardhero@example.com', '$2y$10$e0NR3QYqLKWLkta.lU2SJeUg2AQQgL7omMt74O5bGCYihs/1F/KEW', 'Team-focused player who loves strategy RPGs.', 'Advanced', 'Evenings');

INSERT INTO games (name, genre, developer, release_year, is_multiplayer) VALUES
('League of Legends', 'MOBA', 'Riot Games', 2009, TRUE),
('Valorant', 'FPS', 'Riot Games', 2020, TRUE),
('Counter-Strike 2', 'FPS', 'Valve', 2023, TRUE),
('Dota 2', 'MOBA', 'Valve', 2013, TRUE),
('Overwatch 2', 'Shooter', 'Blizzard', 2022, TRUE),
('Minecraft', 'Sandbox', 'Mojang', 2011, TRUE),
('Hearthstone', 'Card Game', 'Blizzard', 2014, TRUE),
('Apex Legends', 'Battle Royale', 'Respawn', 2019, TRUE);

INSERT INTO user_preferences (user_id, preferred_genres, preferred_playstyle, competitive_preference, team_size_preference) VALUES
(1, 'FPS, Battle Royale', 'Team-oriented', 'Competitive', 'Squad'),
(2, 'Adventure, RPG', 'Casual', 'Casual', 'Solo'),
(3, 'MOBA, Strategy', 'Tactical', 'Competitive', 'Duo'),
(4, 'FPS, Shooter', 'Aggressive', 'Mixed', 'Squad'),
(5, 'Strategy, Card Game', 'Support', 'Casual', 'Duo');

INSERT INTO user_games (user_id, game_id, platform, playtime_hours) VALUES
(1, 3, 'Steam', 1200),
(1, 8, 'Epic Games', 340),
(2, 6, 'Other', 560),
(2, 7, 'Blizzard', 240),
(3, 1, 'Riot Games', 1800),
(3, 4, 'Steam', 950),
(4, 2, 'Riot Games', 600),
(4, 3, 'Steam', 430),
(5, 5, 'Blizzard', 720),
(5, 7, 'Blizzard', 515);

INSERT INTO matches (name, game_id, creator_id, match_type, max_participants, status, scheduled_time, description) VALUES
('Night Siege', 3, 1, 'Ranked', 5, 'Pending', '2026-05-05 20:00:00', 'Competitive CS2 match for evening players.'),
('Casual Adventure', 6, 2, 'Casual', 4, 'Active', '2026-05-06 18:00:00', 'Minecraft co-op session for casual builders.'),
('MOBA Strategy Squad', 1, 3, 'Tournament', 5, 'Pending', '2026-05-07 19:30:00', 'League of Legends tournament practice match.');

INSERT INTO match_participants (match_id, user_id, status) VALUES
(1, 1, 'Confirmed'),
(1, 4, 'Pending'),
(2, 2, 'Confirmed'),
(2, 5, 'Pending'),
(3, 3, 'Confirmed'),
(3, 1, 'Pending');

INSERT INTO likes_follows (follower_id, followee_id, action_type) VALUES
(1, 3, 'Follow'),
(2, 5, 'Like'),
(3, 1, 'Follow'),
(4, 1, 'Follow'),
(5, 2, 'Like');
