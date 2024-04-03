CREATE TABLE IF NOT EXISTS users
(
    id         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    email      varchar(255)        NOT NULL,
    password   varchar(255)        NOT NULL,
    created_at datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    updated_at datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    UNIQUE KEY (email)
);

CREATE TABLE IF NOT EXISTS formats
(
    id   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    name varchar(255)        NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS movies
(
    id           bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    name         varchar(255)        NOT NULL,
    release_date YEAR                NOT NULL,
    format_id    bigint(20) UNSIGNED NOT NULL,
    user_id      bigint(20) UNSIGNED NOT NULL,
    created_at   datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    updated_at   datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (format_id) REFERENCES formats (id)
);

CREATE TABLE IF NOT EXISTS actors
(
    id      bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    name    varchar(255)        NOT NULL,
    surname varchar(255)        NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS movie_actors
(
    id       bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    movie_id bigint(20) UNSIGNED NOT NULL,
    actor_id bigint(20) UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors (id) ON DELETE CASCADE
);