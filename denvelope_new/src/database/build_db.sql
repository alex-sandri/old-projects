CREATE TABLE files (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id TINYTEXT NOT NULL,
    name TINYTEXT NOT NULL,
    parent_folder_id TINYTEXT NOT NULL,
    id_path LONGTEXT NOT NULL
);

CREATE TABLE folders (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id TINYTEXT NOT NULL,
    name TINYTEXT NOT NULL,
    created TINYTEXT NOT NULL,
    parent_folder_id TINYTEXT NOT NULL,
    id_path LONGTEXT NOT NULL
);

CREATE TABLE password_resets (
    token VARCHAR(255) NOT NULL PRIMARY KEY,
	user_id TINYTEXT NOT NULL,
    expires TINYTEXT NOT NULL
);

CREATE TABLE sessions (
	token VARCHAR(255) NOT NULL PRIMARY KEY,
    data LONGTEXT NOT NULL,
    expires TINYTEXT NOT NULL
);

CREATE TABLE users(
    user_id VARCHAR(255) NOT NULL PRIMARY KEY,
    username TINYTEXT NOT NULL,
    email TINYTEXT NOT NULL,
    password TINYTEXT NOT NULL,
    created TINYTEXT NOT NULL,
    activated TINYTEXT NOT NULL,
    preferred_language TINYTEXT NOT NULL
);

CREATE TABLE user_sessions (
	token VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id TINYTEXT NOT NULL
);