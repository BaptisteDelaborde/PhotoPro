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
    false, false, 'grid',
    'CLMARC26', 'http://localhost:8082/galeries/code/CLMARC26',
    'Claire et Marc', 'claire.marc@example.com',
    '2026-04-01 09:00:00', NULL
);

INSERT INTO photos (id, photographer_id, galerie_id, title, file_name, mime_type, file_size, storage_url) VALUES
('cccc3333-cccc-3333-cccc-333333333331', '11111111-1111-1111-1111-111111111111', 'aaaa1111-aaaa-1111-aaaa-111111111111', 'Sommet enneigé', 'sommet.jpg', 'image/jpeg', 4.2, 'https://picsum.photos/id/1018/800/600'),
('cccc3333-cccc-3333-cccc-333333333332', '11111111-1111-1111-1111-111111111111', 'aaaa1111-aaaa-1111-aaaa-111111111111', 'Lac d''altitude', 'lac.jpg', 'image/jpeg', 3.8, 'https://picsum.photos/id/1015/800/600'),
('cccc3333-cccc-3333-cccc-333333333333', '11111111-1111-1111-1111-111111111111', 'aaaa1111-aaaa-1111-aaaa-111111111111', 'Forêt de pins', 'foret.jpg', 'image/jpeg', 5.1, 'https://picsum.photos/id/1019/800/600'),

('dddd4444-dddd-4444-dddd-444444444441', '11111111-1111-1111-1111-111111111111', 'bbbb2222-bbbb-2222-bbbb-222222222222', 'Préparatifs de la mariée', 'preparatifs.jpg', 'image/jpeg', 3.5, 'https://picsum.photos/id/1062/800/600'),
('dddd4444-dddd-4444-dddd-444444444442', '11111111-1111-1111-1111-111111111111', 'bbbb2222-bbbb-2222-bbbb-222222222222', 'Échange des vœux', 'voeux.jpg', 'image/jpeg', 4.1, 'https://picsum.photos/id/1060/800/600'),
('dddd4444-dddd-4444-dddd-444444444443', '11111111-1111-1111-1111-111111111111', 'bbbb2222-bbbb-2222-bbbb-222222222222', 'Sortie d''église', 'sortie.jpg', 'image/jpeg', 3.9, 'https://picsum.photos/id/1063/800/600'),
('dddd4444-dddd-4444-dddd-444444444444', '11111111-1111-1111-1111-111111111111', 'bbbb2222-bbbb-2222-bbbb-222222222222', 'Première danse', 'danse.jpg', 'image/jpeg', 4.5, 'https://picsum.photos/id/1069/800/600');