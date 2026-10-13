-- Tabla de sesiones/tokens de autenticación propia (sin Supabase Auth)
CREATE TABLE public.user_sessions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES public.users(id) ON DELETE CASCADE,
    token TEXT UNIQUE NOT NULL,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Tabla de direcciones guardadas del usuario
CREATE TABLE public.user_addresses (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES public.users(id) ON DELETE CASCADE,
    label TEXT, -- 'casa', 'trabajo', etc.
    address TEXT NOT NULL,
    city TEXT,
    country TEXT,
    is_default BOOLEAN DEFAULT false
);

-- Tabla de uso de cupones (para controlar usage_limit)
CREATE TABLE public.coupon_usages (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    coupon_id UUID REFERENCES public.coupons(id) ON DELETE CASCADE,
    order_id UUID REFERENCES public.orders(id) ON DELETE CASCADE,
    user_id UUID REFERENCES public.users(id) ON DELETE SET NULL,
    used_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- MODIFICACIONES A TABLAS EXISTENTES
-- orders: agregar referencia a cupón y dirección de envío
ALTER TABLE public.orders ADD COLUMN coupon_id UUID REFERENCES public.coupons(id) ON DELETE SET NULL;
ALTER TABLE public.orders ADD COLUMN shipping_address TEXT;

-- users: agregar campos de perfil
ALTER TABLE public.users ADD COLUMN name TEXT;
ALTER TABLE public.users ADD COLUMN phone TEXT;
ALTER TABLE public.users ADD COLUMN avatar_url TEXT;
