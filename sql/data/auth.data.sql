--POUR SE CONNECTER: MAIL: alicedubois@mail.com MOT DE PASSE: password
INSERT INTO users (id, email, password, role) VALUES (
                                                         '11111111-1111-1111-1111-111111111111',
                                                         'alice.dubois@mail.com',
                                                         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                                                         0
                                                     );

INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone, description, profile_image_url) VALUES (
                                                                                                                                    '11111111-1111-1111-1111-111111111111', -- Même ID que l'utilisateur pour la cohérence
                                                                                                                                    '11111111-1111-1111-1111-111111111111', -- Lien vers la table users
                                                                                                                                    'alice_photo', 'Alice', 'Dubois',
                                                                                                                                    'alice.dubois@mail.com',
                                                                                                                                    '06 11 22 33 44',
                                                                                                                                    'Photographe passionnée, spécialisée dans les paysages naturels et les reportages de mariage.',
                                                                                                                                    'https://picsum.photos/seed/alice/200'
                                                                                                                                );