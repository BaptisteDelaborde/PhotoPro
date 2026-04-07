-- MOT DE PASSE pour tous les comptes : password
INSERT INTO users (id, email, password, role)
VALUES
('11111111-1111-1111-1111-111111111111', 'alice.dubois@mail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('99999999-9999-9999-9999-999999999999', 'thomas.moreau@mail.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('88888888-8888-8888-8888-888888888888', 'sophie.laurent@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0);

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url)
VALUES
('11111111-1111-1111-1111-111111111111', '11111111-1111-1111-1111-111111111111',
 'alice_photo', 'Alice', 'Dubois', 'alice.dubois@mail.com', '06 11 22 33 44',
 'Photographe passionnée, spécialisée dans les paysages naturels et les reportages de mariage.',
 'https://picsum.photos/seed/alice/200'),
('99999999-9999-9999-9999-999999999999', '99999999-9999-9999-9999-999999999999',
 'thomas_photo', 'Thomas', 'Moreau', 'thomas.moreau@mail.com', '06 22 33 44 55',
 'Photographe spécialisé dans les voyages et la nature sauvage.',
 'https://picsum.photos/seed/thomas/200'),
('88888888-8888-8888-8888-888888888888', '88888888-8888-8888-8888-888888888888',
 'sophie_photo', 'Sophie', 'Laurent', 'sophie.laurent@mail.com', '06 33 44 55 66',
 'Architecte de formation reconvertie dans la photographie d''architecture et d''urbanisme.',
 'https://picsum.photos/seed/sophie/200');