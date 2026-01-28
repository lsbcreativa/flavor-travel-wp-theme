<?php
/**
 * Template Name: Página de Viajes
 * Description: Muestra las categorías de viajes (Salidas Confirmadas y Eventos Deportivos)
 */
get_header();

// Contar posts de cada tipo
$salidas_count = wp_count_posts('salida_confirmada')->publish;
$eventos_count = wp_count_posts('evento_deportivo')->publish;
$total_viajes = $salidas_count + $eventos_count;

// Opciones del Customizer
$page_title = get_theme_mod('flavor_viajes_title', 'Nuestros Viajes');
$page_desc = get_theme_mod('flavor_viajes_desc', 'Descubre nuestras salidas confirmadas y eventos deportivos. Experiencias únicas con fechas garantizadas.');
$page_image = get_theme_mod('flavor_viajes_image', '');
if (empty($page_image)) {
    $page_image = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920&q=80';
}

// Imágenes para las categorías
$salidas_image = get_theme_mod('flavor_salidas_image', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80');
$eventos_image = get_theme_mod('flavor_eventos_image', 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&q=80');

// Texto introductorio (arriba de las tarjetas)
$viajes_intro = get_theme_mod('flavor_viajes_intro', '');

// Bloque de contenido adicional (abajo de las tarjetas)
$viajes_content = get_theme_mod('flavor_viajes_content', '');
$viajes_content_image = get_theme_mod('flavor_viajes_content_image', '');
$viajes_layout = get_theme_mod('flavor_viajes_layout', 'text-only');
$viajes_content_image_url = $viajes_content_image ? wp_get_attachment_image_url($viajes_content_image, 'large') : '';
?>

<style>
/* HERO */
.viajes-hero {
    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    align-items: center;
    overflow: hidden;
}
.viajes-hero-bg {
    position: absolute;
    inset: 0;
}
.viajes-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.viajes-hero-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(10,22,40,0.4) 0%, rgba(10,22,40,0.7) 100%);
}
.viajes-hero-content {
    position: relative;
    z-index: 2;
    max-width: 1280px;
    margin: 0 auto;
    padding: 120px 20px 100px;
    text-align: center;
    width: 100%;
}
.scroll-indicator {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: rgba(255,255,255,0.8);
    font-size: 0.85rem;
    animation: bounce 2s infinite;
    z-index: 20;
}
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
    40% { transform: translateX(-50%) translateY(-10px); }
    60% { transform: translateX(-50%) translateY(-5px); }
}

/* GRID DE CATEGORÍAS */
.viajes-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    max-width: 900px;
    margin: 0 auto;
}
.viaje-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    aspect-ratio: 4/3;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.viaje-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}
.viaje-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.viaje-card:hover img {
    transform: scale(1.08);
}
.viaje-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 30%, rgba(10,22,40,0.9) 100%);
}
.viaje-card-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 30px;
    color: white;
}
.viaje-card-icon {
    width: 50px;
    height: 50px;
    background: #2563eb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}
.viaje-card h3 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.75rem;
    margin: 0 0 8px;
}
.viaje-card-count {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
    margin-bottom: 16px;
}
.viaje-card-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: #0a1628;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}
.viaje-card-btn:hover {
    background: #2563eb;
    color: white;
}

@media (max-width: 768px) {
    .viajes-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .viaje-card {
        aspect-ratio: 16/10;
    }
    .viaje-card-content {
        padding: 20px;
    }
    .viaje-card h3 {
        font-size: 1.4rem;
    }
}

/* BLOQUE DE CONTENIDO */
.viajes-content-block {
    padding: 80px 0;
    background: white;
}
.viajes-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}
.viajes-content-grid.reverse {
    direction: rtl;
}
.viajes-content-grid.reverse > * {
    direction: ltr;
}
.viajes-content-grid img {
    width: 100%;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}
.viajes-content-text {
    color: #4b5563;
    line-height: 1.9;
    font-size: 1.05rem;
}
.viajes-content-text a {
    color: #2563eb;
    text-decoration: underline;
}
@media (max-width: 768px) {
    .viajes-content-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .viajes-content-grid.reverse {
        direction: ltr;
    }
}
</style>

<!-- HERO -->
<section class="viajes-hero">
    <div class="viajes-hero-bg">
        <img src="<?php echo esc_url($page_image); ?>" alt="<?php echo esc_attr($page_title); ?>">
    </div>
    <div class="viajes-hero-content">
        <nav style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            <a href="<?php echo home_url(); ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">Inicio</a>
            <span style="color: rgba(255,255,255,0.5);">›</span>
            <span style="color: white;">Viajes</span>
        </nav>
        <?php if ($total_viajes > 0): ?>
        <span style="display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 20px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            <?php echo $total_viajes; ?> viajes disponibles
        </span>
        <?php endif; ?>
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2.5rem, 8vw, 4rem); color: white; margin: 0 0 20px;"><?php echo esc_html($page_title); ?></h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9); max-width: 600px; margin: 0 auto; line-height: 1.7;"><?php echo esc_html($page_desc); ?></p>
    </div>

    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <span style="display: block; margin-bottom: 8px;">Ver más</span>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
    </div>
</section>

<!-- CATEGORÍAS DE VIAJES -->
<section style="padding: 80px 0; background: #f8fafc;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <?php if ($viajes_intro): ?>
        <div style="max-width: 900px; margin: 0 auto 50px; text-align: center; color: #4b5563; line-height: 1.9; font-size: 1.05rem;">
            <?php echo wp_kses_post($viajes_intro); ?>
        </div>
        <?php endif; ?>
        <div class="viajes-grid">
            <!-- Salidas Confirmadas -->
            <a href="<?php echo get_post_type_archive_link('salida_confirmada'); ?>" class="viaje-card">
                <img src="<?php echo esc_url($salidas_image); ?>" alt="Salidas Confirmadas">
                <div class="viaje-card-overlay"></div>
                <div class="viaje-card-content">
                    <div class="viaje-card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <path d="M9 16l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3>Salidas Confirmadas</h3>
                    <p class="viaje-card-count"><?php echo $salidas_count; ?> <?php echo $salidas_count == 1 ? 'salida disponible' : 'salidas disponibles'; ?></p>
                    <span class="viaje-card-btn">
                        Ver todas
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

            <!-- Eventos Deportivos -->
            <a href="<?php echo get_post_type_archive_link('evento_deportivo'); ?>" class="viaje-card">
                <img src="<?php echo esc_url($eventos_image); ?>" alt="Eventos Deportivos">
                <div class="viaje-card-overlay"></div>
                <div class="viaje-card-content">
                    <div class="viaje-card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                            <path d="M2 12h20"></path>
                        </svg>
                    </div>
                    <h3>Eventos Deportivos</h3>
                    <p class="viaje-card-count"><?php echo $eventos_count; ?> <?php echo $eventos_count == 1 ? 'evento disponible' : 'eventos disponibles'; ?></p>
                    <span class="viaje-card-btn">
                        Ver todos
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- BLOQUE DE CONTENIDO ADICIONAL -->
<?php if ($viajes_content): ?>
<section class="viajes-content-block">
    <?php if ($viajes_layout === 'text-only' || empty($viajes_content_image_url)): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <div class="viajes-content-text">
                <?php echo wp_kses_post($viajes_content); ?>
            </div>
        </div>
    <?php elseif ($viajes_layout === 'image-left'): ?>
        <div class="viajes-content-grid">
            <div>
                <img src="<?php echo esc_url($viajes_content_image_url); ?>" alt="">
            </div>
            <div class="viajes-content-text">
                <?php echo wp_kses_post($viajes_content); ?>
            </div>
        </div>
    <?php elseif ($viajes_layout === 'image-right'): ?>
        <div class="viajes-content-grid reverse">
            <div>
                <img src="<?php echo esc_url($viajes_content_image_url); ?>" alt="">
            </div>
            <div class="viajes-content-text">
                <?php echo wp_kses_post($viajes_content); ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- CTA -->
<section style="padding: 80px 0; background: #0a1628; text-align: center;">
    <div style="max-width: 700px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-family: 'DM Serif Display', serif; font-size: clamp(1.75rem, 4vw, 2.5rem); color: white; margin: 0 0 16px;">¿No encuentras lo que buscas?</h2>
        <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem; margin-bottom: 30px;">Contáctanos y te ayudamos a encontrar el viaje perfecto para ti.</p>
        <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 10px; background: #25D366; color: white; padding: 16px 32px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 1rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            Consultar por WhatsApp
        </a>
    </div>
</section>

<?php get_footer(); ?>
