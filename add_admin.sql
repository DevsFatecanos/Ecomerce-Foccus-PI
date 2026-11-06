-- Promove uma conta JÁ CADASTRADA a administrador.
-- 1. Crie a conta normalmente pelo site (/register), com uma senha forte.
-- 2. Troque o e-mail abaixo e execute no PostgreSQL.
--
-- Para criar o admin inicial do zero, use:
--   php artisan db:seed --class=AdminUserSeeder   (senha em ADMIN_PASSWORD no .env)

UPDATE users SET is_admin = true, updated_at = NOW() WHERE email = 'seu-email@exemplo.com';

SELECT id, name, email, is_admin FROM users WHERE email = 'seu-email@exemplo.com';
