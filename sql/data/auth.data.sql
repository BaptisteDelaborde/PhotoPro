INSERT INTO users (id, email, password, role) VALUES (
    '22222222-2222-2222-2222-222222222222',
    'photographer1@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    0
);

INSERT INTO users (id, email, password, role) VALUES (
    '77777777-7777-7777-7777-777777777777',
    'photographer2@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    0
);

INSERT INTO users (id, email, password, role) VALUES (
    '88888888-8888-8888-8888-888888888888',
    'photographer3@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    0
);

INSERT INTO users (id, email, password, role) VALUES (
    'cccccccc-cccc-cccc-cccc-cccccccccccc',
    'photographer4@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    0
);

INSERT INTO users (id, email, password, role) VALUES (
    'dddddddd-dddd-dddd-dddd-dddddddddddd',
    'photographer5@mail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    0
);

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url) VALUES (
    '22222222-2222-2222-2222-222222222222',
    '22222222-2222-2222-2222-222222222222',
    'jean_dupont', 'Jean', 'Dupont',
    'photographer1@mail.com',
    '06 12 34 56 78',
    'Photographe portraitiste basé à Paris, spécialisé en portrait urbain et reportage mariage.',
    'https://picsum.photos/seed/jean/200'
);

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url) VALUES (
    '77777777-7777-7777-7777-777777777777',
    '77777777-7777-7777-7777-777777777777',
    'marie_martin', 'Marie', 'Martin',
    'photographer2@mail.com',
    '06 98 76 54 32',
    'Passionnée de paysages naturels et de photographie animalière, basée en Bretagne.',
    'https://picsum.photos/seed/marie/200'
);

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url) VALUES (
    '88888888-8888-8888-8888-888888888888',
    '88888888-8888-8888-8888-888888888888',
    'thomas_leroy', 'Thomas', 'Leroy',
    'photographer3@mail.com',
    '07 11 22 33 44',
    'Architecte photographe, capturant le béton et la lumière des métropoles européennes.',
    'https://picsum.photos/seed/thomas/200'
);

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url) VALUES (
    'cccccccc-cccc-cccc-cccc-cccccccccccc',
    'cccccccc-cccc-cccc-cccc-cccccccccccc',
    'lucie_bernard', 'Lucie', 'Bernard',
    'photographer4@mail.com',
    '06 55 44 33 22',
    'Spécialisée en photographie de naissance, maternité et famille. Lumière naturelle avant tout.',
    'https://picsum.photos/seed/lucie/200'
);

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url) VALUES (
    'dddddddd-dddd-dddd-dddd-dddddddddddd',
    'dddddddd-dddd-dddd-dddd-dddddddddddd',
    'antoine_moreau', 'Antoine', 'Moreau',
    'photographer5@mail.com',
    '07 66 77 88 99',
    'Photographe de concert et de festival depuis 10 ans. Capturez l''énergie de la scène.',
    'https://picsum.photos/seed/antoine/200'
);
