INSERT INTO galleries (
    id,
    photographer_id,
    title,
    description,
    is_public,
    is_published,
    layout,
    created_at,
    published_at
)
VALUES (
    '55555555-5555-5555-5555-555555555555',
    '22222222-2222-2222-2222-222222222222',
    'Galerie publique de test',
    'Une galerie publique pour tester la plateforme',
    TRUE,
    TRUE,
    'grid',
    NOW(),
    NOW()
);

INSERT INTO galleries (
    id,
    photographer_id,
    title,
    description,
    is_public,
    is_published,
    layout,
    access_code,
    access_url,
    client_name,
    client_email,
    created_at,
    published_at
)
VALUES (
    '66666666-6666-6666-6666-666666666666',
    '22222222-2222-2222-2222-222222222222',
    'Galerie privée de test',
    'Une galerie privée pour tester la plateforme',
    FALSE,
    TRUE,
    'masonry',
    'ABC123',
    'http://localhost:8082/galeries/code/ABC123',
    'Client Test',
    'client@test.com',
    NOW(),
    NOW()
);

INSERT INTO photos (
    id,
    photographer_id,
    galerie_id,
    title,
    file_name,
    mime_type,
    file_size,
    storage_url
)
VALUES
(
    '33333333-3333-3333-3333-333333333333',
    '22222222-2222-2222-2222-222222222222',
    '55555555-5555-5555-5555-555555555555',
    'Photo 1',
    'photo1.jpg',
    'image/jpeg',
    2.5,
    'https://picsum.photos/600'
),
(
    '44444444-4444-4444-4444-444444444444',
    '22222222-2222-2222-2222-222222222222',
    '55555555-5555-5555-5555-555555555555',
    'Photo 2',
    'photo2.jpg',
    'image/jpeg',
    3.1,
    'https://picsum.photos/601'
);
