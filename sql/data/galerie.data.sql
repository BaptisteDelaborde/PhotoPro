INSERT INTO photos (
    id,
    photographer_id,
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
    'Photo 1',
    'photo1.jpg',
    'image/jpeg',
    2.5,
    'https://picsum.photos/600'
),
(
    '44444444-4444-4444-4444-444444444444',
    '22222222-2222-2222-2222-222222222222',
    'Photo 2',
    'photo2.jpg',
    'image/jpeg',
    3.1,
    'https://picsum.photos/601'
);

INSERT INTO gallery_photos (gallery_id, photo_id, position)
VALUES
(
    '55555555-5555-5555-5555-555555555555',
    '33333333-3333-3333-3333-333333333333',
    1
),
(
    '55555555-5555-5555-5555-555555555555',
    '44444444-4444-4444-4444-444444444444',
    2
);