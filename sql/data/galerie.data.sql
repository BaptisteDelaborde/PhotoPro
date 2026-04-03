INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    '55555555-5555-5555-5555-555555555555',
    '22222222-2222-2222-2222-222222222222',
    'Portraits urbains',
    'Une série de portraits réalisés dans les rues de Paris, capturant l''âme de la ville.',
    true, true, 'grid',
    NULL, NULL, NULL, NULL,
    '2025-10-01 10:00:00', '2025-11-10 14:30:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    '66666666-6666-6666-6666-666666666666',
    '22222222-2222-2222-2222-222222222222',
    'Mariage Martin-Leroy',
    'Reportage photo du mariage de Sophie Martin et Julien Leroy au château de Vaux-le-Vicomte.',
    false, false, 'masonry',
    'ABC123', 'http://localhost:8082/galeries/code/ABC123',
    'Sophie Martin', 'client@test.com',
    '2025-11-20 09:00:00', NULL
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'a1a1a1a1-a1a1-a1a1-a1a1-a1a1a1a1a1a1',
    '22222222-2222-2222-2222-222222222222',
    'Paris by night',
    'La capitale illuminée — ponts, monuments et reflets sur la Seine après le coucher du soleil.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2025-08-15 21:00:00', '2025-09-01 08:00:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'e0e0e0e0-e0e0-e0e0-e0e0-e0e0e0e0e0e0',
    '77777777-7777-7777-7777-777777777777',
    'Paysages de Bretagne',
    'Côtes sauvages, landes et phares : une immersion dans la lumière bretonne.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2025-09-10 07:00:00', '2025-10-05 09:00:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'f0f0f0f0-f0f0-f0f0-f0f0-f0f0f0f0f0f0',
    '77777777-7777-7777-7777-777777777777',
    'Séance famille Rousseau',
    'Séance photo en extérieur pour la famille Rousseau — parc de Saint-Cloud.',
    false, false, 'grid',
    'XYZ789', 'http://localhost:8082/galeries/code/XYZ789',
    'Paul Rousseau', 'paul.rousseau@example.com',
    '2025-11-05 14:00:00', NULL
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'a2a2a2a2-a2a2-a2a2-a2a2-a2a2a2a2a2a2',
    '77777777-7777-7777-7777-777777777777',
    'Forêt de Brocéliande',
    'Entre brume et légende, les sous-bois de Brocéliande à l''automne.',
    true, true, 'grid',
    NULL, NULL, NULL, NULL,
    '2025-10-20 08:30:00', '2025-11-01 10:00:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    '9a9a9a9a-9a9a-9a9a-9a9a-9a9a9a9a9a9a',
    '88888888-8888-8888-8888-888888888888',
    'Architecture parisienne',
    'Façades haussmanniennes, verrières et passages couverts de Paris.',
    true, true, 'grid',
    NULL, NULL, NULL, NULL,
    '2025-08-01 11:00:00', '2025-09-20 16:45:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'b1b1b1b1-b1b1-b1b1-b1b1-b1b1b1b1b1b1',
    '88888888-8888-8888-8888-888888888888',
    'Inauguration Galerie Lumière',
    'Reportage de l''inauguration de la galerie d''art contemporain Lumière, Lyon.',
    false, false, 'masonry',
    'DEF456', 'http://localhost:8082/galeries/code/DEF456',
    'Galerie Lumière', 'contact@galerie-lumiere.fr',
    '2025-10-15 18:00:00', NULL
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'a3a3a3a3-a3a3-a3a3-a3a3-a3a3a3a3a3a3',
    'cccccccc-cccc-cccc-cccc-cccccccccccc',
    'Nouveau-nés & maternité',
    'Des instants de douceur à la naissance — séances en lumière naturelle à domicile.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2025-07-01 09:00:00', '2025-07-20 11:00:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'a4a4a4a4-a4a4-a4a4-a4a4-a4a4a4a4a4a4',
    'cccccccc-cccc-cccc-cccc-cccccccccccc',
    'Portraits corporate Nexus',
    'Shooting portrait professionnel pour l''équipe dirigeante de Nexus Consulting.',
    false, false, 'grid',
    'GHI012', 'http://localhost:8082/galeries/code/GHI012',
    'Nexus Consulting', 'rh@nexus-consulting.fr',
    '2025-11-25 13:00:00', NULL
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'a5a5a5a5-a5a5-a5a5-a5a5-a5a5a5a5a5a5',
    'dddddddd-dddd-dddd-dddd-dddddddddddd',
    'Festival Les Vieilles Charrues 2025',
    'Backstage et scènes du festival breton — trois jours d''immersion dans la fosse photo.',
    true, true, 'masonry',
    NULL, NULL, NULL, NULL,
    '2025-07-18 20:00:00', '2025-08-05 14:00:00'
);

INSERT INTO galleries (id, photographer_id, title, description, is_public, is_published, layout, access_code, access_url, client_name, client_email, created_at, published_at) VALUES (
    'a6a6a6a6-a6a6-a6a6-a6a6-a6a6a6a6a6a6',
    'dddddddd-dddd-dddd-dddd-dddddddddddd',
    'Shooting mode printemps — Label Noé',
    'Collection printemps-été 2026 du label parisien Noé — shooting en studio et en extérieur.',
    false, false, 'masonry',
    'JKL345', 'http://localhost:8082/galeries/code/JKL345',
    'Label Noé', 'direction@label-noe.com',
    '2025-12-01 10:00:00', NULL
);

INSERT INTO photos (id, photographer_id, galerie_id, title, file_name, mime_type, file_size, storage_url) VALUES
('33333333-3333-3333-3333-333333333333', '22222222-2222-2222-2222-222222222222', '55555555-5555-5555-5555-555555555555', 'Place de la République', 'republique.jpg', 'image/jpeg', 3.2, 'https://picsum.photos/id/1018/800/600'),
('44444444-4444-4444-4444-444444444444', '22222222-2222-2222-2222-222222222222', '55555555-5555-5555-5555-555555555555', 'Montmartre at dawn', 'montmartre.jpg', 'image/jpeg', 2.8, 'https://picsum.photos/id/1023/800/600'),
('c0000001-0000-0000-0000-000000000001', '22222222-2222-2222-2222-222222222222', '55555555-5555-5555-5555-555555555555', 'Le Marais', 'marais.jpg', 'image/jpeg', 4.1, 'https://picsum.photos/id/1040/800/600'),
('c0000002-0000-0000-0000-000000000002', '22222222-2222-2222-2222-222222222222', '55555555-5555-5555-5555-555555555555', 'Canal Saint-Martin', 'canal.jpg', 'image/jpeg', 3.5, 'https://picsum.photos/id/1044/800/600'),
('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', '22222222-2222-2222-2222-222222222222', '66666666-6666-6666-6666-666666666666', 'Cérémonie', 'ceremonie.jpg', 'image/jpeg', 5.1, 'https://picsum.photos/id/1060/800/600'),
('c0000003-0000-0000-0000-000000000003', '22222222-2222-2222-2222-222222222222', '66666666-6666-6666-6666-666666666666', 'Les mariés', 'maries.jpg', 'image/jpeg', 3.7, 'https://picsum.photos/id/1062/800/600'),
('c0000004-0000-0000-0000-000000000004', '22222222-2222-2222-2222-222222222222', '66666666-6666-6666-6666-666666666666', 'Vin d''honneur', 'vin_honneur.jpg', 'image/jpeg', 2.9, 'https://picsum.photos/id/1063/800/600'),
('c0000005-0000-0000-0000-000000000005', '22222222-2222-2222-2222-222222222222', '66666666-6666-6666-6666-666666666666', 'La pièce montée', 'gateau.jpg', 'image/jpeg', 2.1, 'https://picsum.photos/id/1069/800/600'),
('c0000006-0000-0000-0000-000000000006', '22222222-2222-2222-2222-222222222222', 'a1a1a1a1-a1a1-a1a1-a1a1-a1a1a1a1a1a1', 'Tour Eiffel illuminée', 'eiffel.jpg', 'image/jpeg', 6.3, 'https://picsum.photos/id/1014/800/600'),
('c0000007-0000-0000-0000-000000000007', '22222222-2222-2222-2222-222222222222', 'a1a1a1a1-a1a1-a1a1-a1a1-a1a1a1a1a1a1', 'Pont Alexandre III', 'pontalex.jpg', 'image/jpeg', 4.8, 'https://picsum.photos/id/1021/800/600'),
('c0000008-0000-0000-0000-000000000008', '22222222-2222-2222-2222-222222222222', 'a1a1a1a1-a1a1-a1a1-a1a1-a1a1a1a1a1a1', 'Reflets sur la Seine', 'seine.jpg', 'image/jpeg', 3.9, 'https://picsum.photos/id/1022/800/600'),
('c0000009-0000-0000-0000-000000000009', '77777777-7777-7777-7777-777777777777', 'e0e0e0e0-e0e0-e0e0-e0e0-e0e0e0e0e0e0', 'Pointe du Raz', 'raz.jpg', 'image/jpeg', 6.2, 'https://picsum.photos/id/1015/800/600'),
('c000000a-0000-0000-0000-00000000000a', '77777777-7777-7777-7777-777777777777', 'e0e0e0e0-e0e0-e0e0-e0e0-e0e0e0e0e0e0', 'Phare du Créac''h', 'phare.jpg', 'image/jpeg', 4.5, 'https://picsum.photos/id/1016/800/600'),
('c000000b-0000-0000-0000-00000000000b', '77777777-7777-7777-7777-777777777777', 'e0e0e0e0-e0e0-e0e0-e0e0-e0e0e0e0e0e0', 'Lande en automne', 'lande.jpg', 'image/jpeg', 3.8, 'https://picsum.photos/id/1019/800/600'),
('c000000c-0000-0000-0000-00000000000c', '77777777-7777-7777-7777-777777777777', 'e0e0e0e0-e0e0-e0e0-e0e0-e0e0e0e0e0e0', 'Côte sauvage', 'cote.jpg', 'image/jpeg', 5.0, 'https://picsum.photos/id/1038/800/600'),
('c000000d-0000-0000-0000-00000000000d', '77777777-7777-7777-7777-777777777777', 'f0f0f0f0-f0f0-f0f0-f0f0-f0f0f0f0f0f0', 'Portrait familial', 'famille.jpg', 'image/jpeg', 2.6, 'https://picsum.photos/id/1027/800/600'),
('c000000e-0000-0000-0000-00000000000e', '77777777-7777-7777-7777-777777777777', 'f0f0f0f0-f0f0-f0f0-f0f0-f0f0f0f0f0f0', 'Les enfants', 'enfants.jpg', 'image/jpeg', 1.9, 'https://picsum.photos/id/1028/800/600'),
('c000000f-0000-0000-0000-00000000000f', '77777777-7777-7777-7777-777777777777', 'f0f0f0f0-f0f0-f0f0-f0f0-f0f0f0f0f0f0', 'Jeux dans le parc', 'jeux.jpg', 'image/jpeg', 2.3, 'https://picsum.photos/id/1036/800/600'),
('c0000010-0000-0000-0000-000000000010', '77777777-7777-7777-7777-777777777777', 'a2a2a2a2-a2a2-a2a2-a2a2-a2a2a2a2a2a2', 'Brume matinale', 'brume.jpg', 'image/jpeg', 4.4, 'https://picsum.photos/id/1043/800/600'),
('c0000011-0000-0000-0000-000000000011', '77777777-7777-7777-7777-777777777777', 'a2a2a2a2-a2a2-a2a2-a2a2-a2a2a2a2a2a2', 'Sous-bois dorés', 'sousbois.jpg', 'image/jpeg', 3.7, 'https://picsum.photos/id/1049/800/600'),
('c0000012-0000-0000-0000-000000000012', '77777777-7777-7777-7777-777777777777', 'a2a2a2a2-a2a2-a2a2-a2a2-a2a2a2a2a2a2', 'L''étang de Comper', 'etang.jpg', 'image/jpeg', 5.2, 'https://picsum.photos/id/1055/800/600'),
('c0000013-0000-0000-0000-000000000013', '88888888-8888-8888-8888-888888888888', '9a9a9a9a-9a9a-9a9a-9a9a-9a9a9a9a9a9a', 'Opéra Garnier', 'opera.jpg', 'image/jpeg', 7.1, 'https://picsum.photos/id/1029/800/600'),
('c0000014-0000-0000-0000-000000000014', '88888888-8888-8888-8888-888888888888', '9a9a9a9a-9a9a-9a9a-9a9a-9a9a9a9a9a9a', 'Passage des Panoramas', 'passage.jpg', 'image/jpeg', 3.3, 'https://picsum.photos/id/1031/800/600'),
('c0000015-0000-0000-0000-000000000015', '88888888-8888-8888-8888-888888888888', '9a9a9a9a-9a9a-9a9a-9a9a-9a9a9a9a9a9a', 'Grande halle de la Villette', 'villette.jpg', 'image/jpeg', 5.5, 'https://picsum.photos/id/1034/800/600'),
('c0000016-0000-0000-0000-000000000016', '88888888-8888-8888-8888-888888888888', '9a9a9a9a-9a9a-9a9a-9a9a-9a9a9a9a9a9a', 'Centre Pompidou', 'pompidou.jpg', 'image/jpeg', 4.2, 'https://picsum.photos/id/1037/800/600'),
('c0000017-0000-0000-0000-000000000017', '88888888-8888-8888-8888-888888888888', 'b1b1b1b1-b1b1-b1b1-b1b1-b1b1b1b1b1b1', 'Vernissage', 'vernissage.jpg', 'image/jpeg', 4.0, 'https://picsum.photos/id/1048/800/600'),
('c0000018-0000-0000-0000-000000000018', '88888888-8888-8888-8888-888888888888', 'b1b1b1b1-b1b1-b1b1-b1b1-b1b1b1b1b1b1', 'Les œuvres', 'oeuvres.jpg', 'image/jpeg', 3.6, 'https://picsum.photos/id/1050/800/600'),
('c0000019-0000-0000-0000-000000000019', '88888888-8888-8888-8888-888888888888', 'b1b1b1b1-b1b1-b1b1-b1b1-b1b1b1b1b1b1', 'Les invités', 'invites.jpg', 'image/jpeg', 2.8, 'https://picsum.photos/id/1052/800/600'),
('c000001a-0000-0000-0000-00000000001a', 'cccccccc-cccc-cccc-cccc-cccccccccccc', 'a3a3a3a3-a3a3-a3a3-a3a3-a3a3a3a3a3a3', 'Premiers instants', 'naissance1.jpg', 'image/jpeg', 2.2, 'https://picsum.photos/id/1074/800/600'),
('c000001b-0000-0000-0000-00000000001b', 'cccccccc-cccc-cccc-cccc-cccccccccccc', 'a3a3a3a3-a3a3-a3a3-a3a3-a3a3a3a3a3a3', 'Les petites mains', 'naissance2.jpg', 'image/jpeg', 1.8, 'https://picsum.photos/id/1076/800/600'),
('c000001c-0000-0000-0000-00000000001c', 'cccccccc-cccc-cccc-cccc-cccccccccccc', 'a3a3a3a3-a3a3-a3a3-a3a3-a3a3a3a3a3a3', 'Séance maternité', 'maternite.jpg', 'image/jpeg', 3.1, 'https://picsum.photos/id/1077/800/600'),
('c000001d-0000-0000-0000-00000000001d', 'cccccccc-cccc-cccc-cccc-cccccccccccc', 'a4a4a4a4-a4a4-a4a4-a4a4-a4a4a4a4a4a4', 'Portrait DG', 'dg.jpg', 'image/jpeg', 2.9, 'https://picsum.photos/id/1080/800/600'),
('c000001e-0000-0000-0000-00000000001e', 'cccccccc-cccc-cccc-cccc-cccccccccccc', 'a4a4a4a4-a4a4-a4a4-a4a4-a4a4a4a4a4a4', 'Équipe dirigeante', 'equipe.jpg', 'image/jpeg', 3.4, 'https://picsum.photos/id/1081/800/600'),
('c000001f-0000-0000-0000-00000000001f', 'cccccccc-cccc-cccc-cccc-cccccccccccc', 'a4a4a4a4-a4a4-a4a4-a4a4-a4a4a4a4a4a4', 'Open space', 'openspace.jpg', 'image/jpeg', 4.0, 'https://picsum.photos/id/1082/800/600'),
('c0000020-0000-0000-0000-000000000020', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a5a5a5a5-a5a5-a5a5-a5a5-a5a5a5a5a5a5', 'Grande scène', 'scene.jpg', 'image/jpeg', 5.8, 'https://picsum.photos/id/1084/800/600'),
('c0000021-0000-0000-0000-000000000021', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a5a5a5a5-a5a5-a5a5-a5a5-a5a5a5a5a5a5', 'Foule en délire', 'foule.jpg', 'image/jpeg', 4.7, 'https://picsum.photos/id/1086/800/600'),
('c0000022-0000-0000-0000-000000000022', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a5a5a5a5-a5a5-a5a5-a5a5-a5a5a5a5a5a5', 'Backstage', 'backstage.jpg', 'image/jpeg', 3.3, 'https://picsum.photos/id/1090/800/600'),
('c0000023-0000-0000-0000-000000000023', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a5a5a5a5-a5a5-a5a5-a5a5-a5a5a5a5a5a5', 'Coucher de soleil sur le site', 'sunset_festival.jpg', 'image/jpeg', 6.1, 'https://picsum.photos/id/1091/800/600'),
('c0000024-0000-0000-0000-000000000024', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a6a6a6a6-a6a6-a6a6-a6a6-a6a6a6a6a6a6', 'Look 01 — Studio', 'look01.jpg', 'image/jpeg', 4.5, 'https://picsum.photos/id/1094/800/600'),
('c0000025-0000-0000-0000-000000000025', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a6a6a6a6-a6a6-a6a6-a6a6-a6a6a6a6a6a6', 'Look 02 — Extérieur', 'look02.jpg', 'image/jpeg', 3.9, 'https://picsum.photos/id/1095/800/600'),
('c0000026-0000-0000-0000-000000000026', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'a6a6a6a6-a6a6-a6a6-a6a6-a6a6a6a6a6a6', 'Détail accessoires', 'accessoires.jpg', 'image/jpeg', 2.7, 'https://picsum.photos/id/1096/800/600');
