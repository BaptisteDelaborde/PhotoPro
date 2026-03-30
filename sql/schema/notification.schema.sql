DROP TABLE IF EXISTS notifications;

CREATE TABLE notifications (
    id UUID PRIMARY KEY,

    gallery_id UUID NOT NULL,
    client_email VARCHAR(255) NOT NULL,

    type VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP
);