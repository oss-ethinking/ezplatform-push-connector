create table push_delivery_main_settings
(
 settings_id INTEGER NOT NULL DEFAULT 1,
 domain VARCHAR(50) NOT NULL,
 client_id VARCHAR(50) NOT NULL,
 username VARCHAR(50) NOT NULL,
 password VARCHAR(50) NOT NULL,
 created_on DATETIME NOT NULL,
 updated_on DATETIME NOT NULL,
 CONSTRAINT PK_PUSH_DELIVERY_MAIN_SETTINGS PRIMARY KEY(settings_id)
);