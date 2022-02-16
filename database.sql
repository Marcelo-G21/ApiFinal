CREATE DATABASE IF NOT EXISTS apirest;
USE apirest;

CREATE TABLE users(
id int auto_increment not null,
email varchar(255),
role	varchar(20),
name	varchar(255),
surname	varchar(255),
password varchar(255),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
remember_token varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE curso(
id int auto_increment not null,
user_id int not null,
nombre varchar(255),
horas	int,
status  varchar(30),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
CONSTRAINT pk_curso PRIMARY KEY(id),
CONSTRAINT fk_curso_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;