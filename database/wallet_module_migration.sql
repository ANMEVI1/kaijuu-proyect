-- TABLA 1: zen_exchange_rates — Historial de tipos de cambio
CREATE TABLE public.zen_exchange_rates (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    currency    TEXT NOT NULL DEFAULT 'PEN',
    rate        NUMERIC(10, 4) NOT NULL,
    is_active   BOOLEAN NOT NULL DEFAULT true,
    set_by      UUID REFERENCES public.users(id) ON DELETE SET NULL,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    CONSTRAINT unique_active_currency UNIQUE (currency, is_active)
);
CREATE INDEX idx_zen_exchange_rates_active ON public.zen_exchange_rates (currency, is_active);
INSERT INTO public.zen_exchange_rates (currency, rate, is_active) VALUES ('PEN', 3.5000, true);

-- TABLA 2: wallets — Monedero de cada usuario
CREATE TABLE public.wallets (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id     UUID NOT NULL UNIQUE REFERENCES public.users(id) ON DELETE CASCADE,
    balance_zen NUMERIC(12, 2) NOT NULL DEFAULT 0.00 CHECK (balance_zen >= 0),
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at  TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
CREATE INDEX idx_wallets_user_id ON public.wallets (user_id);

-- TABLA 3: wallet_transactions — Historial inmutable de movimientos
CREATE TABLE public.wallet_transactions (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    wallet_id       UUID NOT NULL REFERENCES public.wallets(id) ON DELETE CASCADE,
    user_id         UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    type            TEXT NOT NULL CHECK (type IN ('recarga', 'compra', 'reembolso', 'ajuste_admin', 'bono')),
    amount_zen      NUMERIC(12, 2) NOT NULL CHECK (amount_zen > 0),
    direction       TEXT NOT NULL CHECK (direction IN ('credito', 'debito')),
    balance_before  NUMERIC(12, 2) NOT NULL,
    balance_after   NUMERIC(12, 2) NOT NULL,
    exchange_rate_id UUID REFERENCES public.zen_exchange_rates(id) ON DELETE SET NULL,
    amount_fiat     NUMERIC(10, 2),
    currency        TEXT DEFAULT 'PEN',
    order_id        UUID REFERENCES public.orders(id) ON DELETE SET NULL,
    reference_code  TEXT,
    status          TEXT NOT NULL DEFAULT 'completado' CHECK (status IN ('pendiente', 'completado', 'fallido', 'revertido')),
    notes           TEXT,
    created_at      TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
CREATE INDEX idx_wallet_tx_wallet_id   ON public.wallet_transactions (wallet_id);
CREATE INDEX idx_wallet_tx_user_id     ON public.wallet_transactions (user_id);
CREATE INDEX idx_wallet_tx_type        ON public.wallet_transactions (type);
CREATE INDEX idx_wallet_tx_status      ON public.wallet_transactions (status);
CREATE INDEX idx_wallet_tx_created_at  ON public.wallet_transactions (created_at DESC);
CREATE INDEX idx_wallet_tx_order_id    ON public.wallet_transactions (order_id) WHERE order_id IS NOT NULL;

-- TABLA 4: recharge_packages — Paquetes de recarga predefinidos
CREATE TABLE public.recharge_packages (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name            TEXT NOT NULL,
    zen_amount      NUMERIC(12, 2) NOT NULL,
    price_pen       NUMERIC(10, 2) NOT NULL,
    bonus_zen       NUMERIC(12, 2) DEFAULT 0,
    is_active       BOOLEAN DEFAULT true,
    is_featured     BOOLEAN DEFAULT false,
    sort_order      INTEGER DEFAULT 0,
    created_at      TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
INSERT INTO public.recharge_packages (name, zen_amount, price_pen, bonus_zen, is_featured, sort_order) VALUES
('Paquete Genin',      100,   350.00,   0,  false, 1),
('Paquete Chunin',     300,  1050.00,  15,  false, 2),
('Paquete Jonin',      500,  1750.00,  50,  true,  3),
('Paquete Kage',      1000,  3500.00, 150,  false, 4),
('Paquete Legendario', 3000, 10500.00, 600, false, 5);

-- MODIFICACIONES A TABLAS EXISTENTES
ALTER TABLE public.orders
    ADD COLUMN IF NOT EXISTS payment_method TEXT DEFAULT 'zen_wallet' CHECK (payment_method IN ('zen_wallet', 'paypal', 'mercadopago', 'mixto')),
    ADD COLUMN IF NOT EXISTS zen_used       NUMERIC(12, 2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS fiat_used      NUMERIC(10, 2) DEFAULT 0;
