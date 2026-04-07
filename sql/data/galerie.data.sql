INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES
(
    'aaaa1111-aaaa-1111-aaaa-111111111111',
    '11111111-1111-1111-1111-111111111111', -- ID d'Alice
    'Échappée en Montagne',
    'Une série de clichés capturant la beauté brute des Alpes au printemps.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2025-10-01 10:00:00', '2025-10-05 14:30:00'
),
(
    'bbbb2222-bbbb-2222-bbbb-222222222222',
    '11111111-1111-1111-1111-111111111111', -- ID d'Alice
    'Mariage de Claire & Marc',
    'Reportage complet du mariage au domaine des Chênes, une journée inoubliable.',
    false, true, 'grid',
    'CLMARC26', 'http://localhost:8082/galeries/code/CLMARC26',
    'Claire et Marc', 'claire.marc@example.com',
    '2026-04-01 09:00:00', '2026-04-02 10:00:00'
)
,
(
    '11111111-aaaa-bbbb-cccc-111111111111',
    '99999999-9999-9999-9999-999999999999',
    'Voyage en Islande',
    'Des paysages à couper le souffle, entre glace et feu.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2026-01-10 08:00:00', '2026-01-15 10:00:00'
),
(
    '22222222-aaaa-bbbb-cccc-222222222222',
    '99999999-9999-9999-9999-999999999999',
    'Séance Naissance - Petit Léo',
    'Les premiers jours de Léo en famille.',
    false, true, 'grid',
    'LEO2026', '/p/LEO2026',
    'Famille Martin', 'famille.martin@example.com',
    '2026-02-01 14:00:00', '2026-02-05 09:30:00'
),
(
    '33333333-aaaa-bbbb-cccc-333333333333',
    '88888888-8888-8888-8888-888888888888',
    'Architecture de Paris',
    'Exploration des façades haussmanniennes.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2026-03-10 11:00:00', '2026-03-12 16:45:00'
);

INSERT INTO photos (id, photographer_id, title, file_name, mime_type, file_size, storage_url) VALUES
('cccc3333-cccc-3333-cccc-333333333331', '11111111-1111-1111-1111-111111111111', 'Sommet enneigé', 'sommet.jpg', 'image/jpeg', 4.2, 'https://picsum.photos/id/1018/800/600'),
('cccc3333-cccc-3333-cccc-333333333332', '11111111-1111-1111-1111-111111111111', 'Lac d''altitude', 'lac.jpg', 'image/jpeg', 3.8, 'https://picsum.photos/id/1015/800/600'),
('cccc3333-cccc-3333-cccc-333333333333', '11111111-1111-1111-1111-111111111111', 'Forêt de pins', 'foret.jpg', 'image/jpeg', 5.1, 'https://picsum.photos/id/1019/800/600'),
('dddd4444-dddd-4444-dddd-444444444441', '11111111-1111-1111-1111-111111111111', 'Préparatifs de la mariée', 'preparatifs.jpg', 'image/jpeg', 3.5, 'https://picsum.photos/id/1062/800/600'),
('dddd4444-dddd-4444-dddd-444444444442', '11111111-1111-1111-1111-111111111111', 'Échange des vœux', 'voeux.jpg', 'image/jpeg', 4.1, 'https://picsum.photos/id/1060/800/600'),
('dddd4444-dddd-4444-dddd-444444444443', '11111111-1111-1111-1111-111111111111', 'Sortie d''église', 'sortie.jpg', 'image/jpeg', 3.9, 'https://picsum.photos/id/1063/800/600'),
('dddd4444-dddd-4444-dddd-444444444444', '11111111-1111-1111-1111-111111111111', 'Première danse', 'danse.jpg', 'image/jpeg', 4.5, 'https://picsum.photos/id/1069/800/600'),
('a1a1a1a1-1111-1111-1111-a1a1a1a1a1a1', '99999999-9999-9999-9999-999999999999', 'Glacier', 'glacier.jpg', 'image/jpeg', 5.2, 'https://picsum.photos/id/1018/800/600'),
('a2a2a2a2-1111-1111-1111-a2a2a2a2a2a2', '99999999-9999-9999-9999-999999999999', 'Cascade', 'cascade.jpg', 'image/jpeg', 4.8, 'https://picsum.photos/id/1015/800/600'),
('b1b1b1b1-2222-2222-2222-b1b1b1b1b1b1', '99999999-9999-9999-9999-999999999999', 'Léo dort', 'leo1.jpg', 'image/jpeg', 3.1, 'https://picsum.photos/id/1062/800/600'),
('b2b2b2b2-2222-2222-2222-b2b2b2b2b2b2', '99999999-9999-9999-9999-999999999999', 'Petits pieds', 'leo2.jpg', 'image/jpeg', 2.9, 'https://picsum.photos/id/1060/800/600'),
('c1c1c1c1-3333-3333-3333-c1c1c1c1c1c1', '88888888-8888-8888-8888-888888888888', 'Façade 1', 'facade1.jpg', 'image/jpeg', 6.0, 'https://picsum.photos/id/1031/800/600');

INSERT INTO gallery_photos (gallery_id, photo_id) VALUES
-- Échappée en Montagne
('aaaa1111-aaaa-1111-aaaa-111111111111', 'cccc3333-cccc-3333-cccc-333333333331'),
('aaaa1111-aaaa-1111-aaaa-111111111111', 'cccc3333-cccc-3333-cccc-333333333332'),
('aaaa1111-aaaa-1111-aaaa-111111111111', 'cccc3333-cccc-3333-cccc-333333333333'),
-- Mariage Claire & Marc
('bbbb2222-bbbb-2222-bbbb-222222222222', 'dddd4444-dddd-4444-dddd-444444444441'),
('bbbb2222-bbbb-2222-bbbb-222222222222', 'dddd4444-dddd-4444-dddd-444444444442'),
('bbbb2222-bbbb-2222-bbbb-222222222222', 'dddd4444-dddd-4444-dddd-444444444443'),
('bbbb2222-bbbb-2222-bbbb-222222222222', 'dddd4444-dddd-4444-dddd-444444444444'),
-- Voyage en Islande
('11111111-aaaa-bbbb-cccc-111111111111', 'a1a1a1a1-1111-1111-1111-a1a1a1a1a1a1'),
('11111111-aaaa-bbbb-cccc-111111111111', 'a2a2a2a2-1111-1111-1111-a2a2a2a2a2a2'),
-- Séance Naissance Léo
('22222222-aaaa-bbbb-cccc-222222222222', 'b1b1b1b1-2222-2222-2222-b1b1b1b1b1b1'),
('22222222-aaaa-bbbb-cccc-222222222222', 'b2b2b2b2-2222-2222-2222-b2b2b2b2b2b2'),
-- Architecture de Paris
('33333333-aaaa-bbbb-cccc-333333333333', 'c1c1c1c1-3333-3333-3333-c1c1c1c1c1c1');
