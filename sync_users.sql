-- 1. Insertar todos los usuarios que ya existían en Supabase Auth hacia tu tabla pública
INSERT INTO public.users (id, email, role, created_at)
SELECT id, email, 'customer', created_at
FROM auth.users au
WHERE NOT EXISTS (
  SELECT 1 FROM public.users pu WHERE pu.email = au.email
)
ON CONFLICT (id) DO NOTHING;

-- 2. Crear una función para que cualquier usuario futuro se copie automáticamente
CREATE OR REPLACE FUNCTION public.handle_new_user() 
RETURNS trigger AS $$
BEGIN
  INSERT INTO public.users (id, email, role, created_at)
  VALUES (new.id, new.email, 'customer', new.created_at)
  ON CONFLICT (id) DO NOTHING;
  RETURN new;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- 3. Crear el Trigger para que se active cada vez que alguien se registre
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();
