
20

Automatic Zoom
↓
PASARELA DE PAGO
↓
PayPal / Mercado Pago
↓
Confirmación del pago
↓
Actualización del pedido
23. FUNCIONALIDADES FUTURAS
Cuando KAIJUU STORE esté completamente terminada se plantea incorporar:
Usuarios
Registro.
Inicio de sesión.
Recuperación de contraseña.
Perfil.
Historial de pedidos.
Direcciones guardadas.
Administración
Panel administrativo.
Crear productos.
Editar productos.
Eliminar productos.
Gestionar categorías.
Gestionar usuarios.
Gestionar pedidos.
Inventario
Control de stock.
Alertas de stock bajo.
Registro de entradas.
Registro de salidas.
Historial de movimientos.
Pedidos
Estados como:
Pendiente → Pagado → Preparando → Enviado → Entregado
También:
Cancelado / Devuelto
24. SISTEMA DE ENVÍOS
En una versión avanzada se podrá incorporar:
Costo de envío.
Dirección de entrega.
Transportista.
Código de seguimiento.
Estado del envío.
Fecha estimada de entrega.
25. SISTEMA DE PROMOCIONES
Se podrá implementar:
Cupones.
Descuentos.
Ofertas especiales.
Productos destacados.
Promociones por temporada.
Descuentos por cantidad.
Campañas especiales.
26. SISTEMA DE FAVORITOS
Los usuarios registrados podrán guardar productos en una lista de favoritos.
Ejemplo:
Mis favoritos
Esto permitirá que el cliente pueda regresar posteriormente a productos que le interesaron.
27. SISTEMA DE RESEÑAS
En una versión futura se podrán implementar:
Calificación por estrellas.
Comentarios.
Carrito.
Checkout.
Supabase.
FASE 2 — PAGOS
Implementar:
PayPal.
Mercado Pago.
Webhooks.
Confirmación automática.
FASE 3 — ADMINISTRACIÓN
Implementar:
Login administrativo.
Panel.
Productos.
Categorías.
Pedidos.
FASE 4 — INVENTARIO
Implementar:
Stock.
Movimientos.
Alertas.
FASE 5 — LOGÍSTICA
Implementar:
Envíos.
Tracking.
Estados.
FASE 6 — EXPERIENCIA DEL CLIENTE
Implementar:
Favoritos.
Reseñas.
Cupones.
Historial.
Perfil.
FASE 7 — MARKETING
Implementar:
SEO.
Analítica.
Promociones.
Campañas.
FASE 8 — LANZAMIENTO
Implementar:
Dominio.
HTTPS.
Backups.
Monitoreo.
Pruebas finales.
35. ESCALABILIDAD DEL NEGOCIO
KAIJUU STORE puede comenzar como:
B2C
Empresa → Consumidor
Posteriormente podría evolucionar hacia:
B2C + B2B
Empresa → Consumidor
Empresa → Empresa
Y en una etapa mucho más avanzada podría convertirse en:
MARKETPLACE
Vendedor → KAIJUU STORE → Consumidor
Sin embargo, cada etapa requiere nuevas funcionalidades, reglas comerciales y controles de
seguridad.
36. RIESGOS TÉCNICOS
Entre los principales riesgos se encuentran:
Credenciales de pago incorrectamente configuradas.
Manipulación de precios desde el navegador.
Manipulación de stock.
Falta de confirmación mediante webhook.
Acceso no autorizado al panel administrativo.
Uso de imágenes externas sin control.
Dependencia excesiva de servicios externos.
Falta de backups.
Falta de políticas legales.
Falta de pruebas antes del lanzamiento.
37. RECOMENDACIONES
Antes de considerar KAIJUU STORE como una tienda completamente terminada se
recomienda:
Configurar correctamente Supabase.
Configurar las credenciales de producción.
Implementar webhooks.
Validar stock en servidor.
Crear el panel administrativo.
Implementar autenticación.
Implementar gestión de pedidos.
Implementar envíos.
Implementar backups.
Optimizar imágenes.
Configurar dominio.
Utilizar HTTPS.
Crear políticas de privacidad.
Crear términos y condiciones.
Definir política de cambios y devoluciones.
Realizar pruebas completas.
38. FICHA TÉCNICA RESUMIDA
Característica
Especificación
Nombre
KAIJUU STORE
Slogan
“Viste tu fandom. Domina tu colección.”
Tipo
E-commerce
Modelo principal
B2C
Modelo futuro
B2B / Marketplace
Sector
Anime y cultura geek
Frontend
HTML5, CSS3, JavaScript
Diseño
Tailwind CSS + CSS personalizado
Backend
Supabase
Base de datos
PostgreSQL
Pagos
PayPal / Mercado Pago
Persistencia
localStorage + Supabase
Seguridad
RLS
Funciones servidor
Supabase Edge Functions
Catálogo
Sí
Búsqueda
Sí
Categorías
Sí
Carrito
Sí
Checkout
Sí
Panel administrativo
Futuro
Autenticación
Futuro
Inventario avanzado
Futuro
Tracking
Futuro
Favoritos
Futuro
Reseñas
Futuro
Cupones
Futuro
Analítica
Futuro
Estado
Prototipo funcional / evolución a producción
39. CONCLUSIÓN
KAIJUU STORE es un proyecto de comercio electrónico especializado en anime y cultura geek
cuyo modelo inicial corresponde principalmente al B2C (Business to Consumer).
La plataforma permitirá que KAIJUU STORE comercialice directamente sus productos a
consumidores finales mediante una tienda virtual.
Actualmente el proyecto cuenta con una base funcional compuesta por catálogo, búsqueda,
categorías, control visual de stock, carrito, almacenamiento local, checkout, estructura de
pedidos, Supabase y preparación para integración con PayPal y Mercado Pago.
La evolución del proyecto estará enfocada en convertir esta base en una plataforma completa
mediante la incorporación de autenticación, panel administrativo, inventario, pagos
verificados, seguimiento de pedidos, envíos, promociones, favoritos, reseñas, notificaciones y
analítica.
A futuro, el modelo podría ampliarse de B2C hacia B2B o incluso transformarse en un
marketplace. Sin embargo, para la primera versión comercial, B2C es el modelo principal, ya
que permite mantener una arquitectura más controlada y enfocada en la venta directa al
consumidor.
KAIJUU STORE
“Viste tu fandom. Domina tu colección.”
Diferencia lo actual de lo futuro
Reduce repeticiones y compacta secciones
