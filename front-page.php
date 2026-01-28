<?php 
get_header(); 

// ===== VISIBILIDAD =====
$show_hero = get_theme_mod('flavor_show_hero', true);
$show_continentes = get_theme_mod('flavor_show_continentes', true);
$show_ofertas = get_theme_mod('flavor_show_ofertas', true);
$show_badges = get_theme_mod('flavor_show_badges', true);
$show_tours = get_theme_mod('flavor_show_tours', true);
$show_destinos = get_theme_mod('flavor_show_destinos', true);
$show_cta = get_theme_mod('flavor_show_cta', true);
$show_newsletter = get_theme_mod('flavor_show_newsletter', true);

// ===== HERO IMAGES =====
$hero_img_1 = get_theme_mod('flavor_hero_image_1', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80');
$hero_img_2 = get_theme_mod('flavor_hero_image_2', '');
$hero_img_3 = get_theme_mod('flavor_hero_image_3', '');
$hero_speed = get_theme_mod('flavor_hero_speed', 5) * 1000;

$hero_images = array();
if (!empty($hero_img_1)) $hero_images[] = $hero_img_1;
if (!empty($hero_img_2)) $hero_images[] = $hero_img_2;
if (!empty($hero_img_3)) $hero_images[] = $hero_img_3;
$is_carousel = count($hero_images) > 1;

// ===== CONTENIDO =====
$hero_badge = get_theme_mod('flavor_hero_badge', 'Más de 50 destinos en 5 continentes');
$hero_title = get_theme_mod('flavor_hero_title', 'Explora el mundo sin límites');
$hero_subtitle = get_theme_mod('flavor_hero_subtitle', 'Desde las playas del Caribe hasta los templos de Asia. Tu próxima aventura te espera.');
$hero_btn1 = get_theme_mod('flavor_hero_btn1_text', 'Explorar Destinos');
$hero_btn1_url = get_theme_mod('flavor_hero_btn1_url', '');
$hero_btn2 = get_theme_mod('flavor_hero_btn2_text', 'Ver Ofertas');
$hero_btn2_url = get_theme_mod('flavor_hero_btn2_url', '');

$continentes_subtitle = get_theme_mod('flavor_continentes_subtitle', 'Destinos por continente');
$continentes_title = get_theme_mod('flavor_continentes_title', 'Explora el mundo');
$ofertas_subtitle = get_theme_mod('flavor_ofertas_home_subtitle', 'Ofertas especiales');
$ofertas_title = get_theme_mod('flavor_ofertas_home_title', 'Descuentos exclusivos');
$tours_subtitle = get_theme_mod('flavor_tours_home_subtitle', 'Experiencias únicas');
$tours_title = get_theme_mod('flavor_tours_home_title', 'Tours destacados');
$destinos_subtitle = get_theme_mod('flavor_destinos_home_subtitle', 'Los más populares');
$destinos_title = get_theme_mod('flavor_destinos_home_title', 'Destinos destacados');
$cta_title = get_theme_mod('flavor_cta_title', 'Llevamos la satisfacción de tus pasajeros al siguiente nivel');
$cta_subtitle = get_theme_mod('flavor_cta_subtitle', 'Somos tu mayorista de confianza. Contáctanos.');
$cta_button = get_theme_mod('flavor_cta_button', 'Consultar por WhatsApp');
$trust1 = get_theme_mod('flavor_trust_1', 'Garantía mejor precio');
$trust2 = get_theme_mod('flavor_trust_2', 'Hasta 12 cuotas sin interés');
$trust3 = get_theme_mod('flavor_trust_3', 'Cancelación flexible');
$newsletter_title = get_theme_mod('flavor_newsletter_title', 'Recibe ofertas exclusivas');
$newsletter_desc = get_theme_mod('flavor_newsletter_desc', 'Suscríbete y recibe las mejores ofertas en tu correo.');

// ===== TEXTOS DESCRIPTIVOS =====
$text_continentes = get_theme_mod('flavor_continentes_text', '');
$text_ofertas = get_theme_mod('flavor_ofertas_home_text', '');
$text_tours = get_theme_mod('flavor_tours_home_text', '');
$text_destinos = get_theme_mod('flavor_destinos_home_text', '');
$text_cta = get_theme_mod('flavor_cta_text', '');


// Queries
$continentes = get_terms(array('taxonomy' => 'continente', 'hide_empty' => false, 'parent' => 0)); // Solo continentes padres
$ofertas = flavor_get_items_con_descuento(4); // Obtiene destinos, tours y ofertas con descuento
$tours = get_posts(array('post_type' => 'paquete', 'posts_per_page' => 6, 'meta_query' => flavor_get_vigencia_meta_query()));
$destinos = get_posts(array('post_type' => 'destino', 'posts_per_page' => 6, 'meta_query' => flavor_get_vigencia_meta_query()));
?>

<style>
/* RESPONSIVE GRIDS */
.ft-continentes { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
.ft-ofertas { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.ft-tours { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.ft-destinos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }

/* ===== EFECTOS HOVER TARJETAS ===== */

/* CONTINENTES */
.ft-continentes a {
    display: block;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    aspect-ratio: 3/4;
    text-decoration: none;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.ft-continentes a:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
}
.ft-continentes a img {
    transition: transform 0.5s ease;
}
.ft-continentes a:hover img {
    transform: scale(1.1);
}
.ft-continentes a::after {
    content: '';
    position: absolute;
    inset: 0;
    border: 3px solid transparent;
    border-radius: 16px;
    transition: border-color 0.3s ease;
    pointer-events: none;
}
.ft-continentes a:hover::after {
    border-color: #2563eb;
}

/* BUSCADOR HERO - SUTIL */
.hero-search {
    max-width: 480px;
    margin-bottom: 28px;
    position: relative;
}
.hero-search-form {
    display: flex;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.hero-search-form:focus-within {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.4);
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}
.hero-search-input {
    flex: 1;
    padding: 14px 24px;
    border: none;
    font-size: 0.95rem;
    outline: none;
    background: transparent;
    color: white;
}
.hero-search-input::placeholder {
    color: rgba(255,255,255,0.6);
}
.hero-search-btn {
    background: transparent;
    color: white;
    border: none;
    padding: 14px 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: all 0.3s;
    opacity: 0.7;
}
.hero-search-btn:hover {
    opacity: 1;
}
.hero-search-results {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    max-height: 320px;
    overflow-y: auto;
    display: none;
    z-index: 100;
}
.hero-search-results.active {
    display: block;
    animation: fadeIn 0.2s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: #1f2937;
    transition: background 0.15s;
}
.search-result-item:first-child {
    border-radius: 16px 16px 0 0;
}
.search-result-item:last-child {
    border-radius: 0 0 16px 16px;
}
.search-result-item:hover {
    background: #f1f5f9;
}
.search-result-img {
    width: 48px;
    height: 36px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
}
.search-result-info {
    flex: 1;
    min-width: 0;
}
.search-result-title {
    font-weight: 500;
    font-size: 0.9rem;
    color: #0a1628;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.search-result-meta {
    font-size: 0.75rem;
    color: #94a3b8;
}
.search-result-price {
    font-weight: 600;
    font-size: 0.85rem;
    color: #2563eb;
}
.search-no-results {
    padding: 24px 16px;
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
}
@media (max-width: 640px) {
    .hero-search-form {
        border-radius: 30px;
    }
    .hero-search-input {
        padding: 12px 20px;
        font-size: 0.9rem;
    }
    .hero-search-btn {
        padding: 12px 16px;
    }
}

/* BADGE DESCUENTO ANIMADO */
@keyframes pulse-badge {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
@keyframes shine {
    0% { left: -100%; }
    100% { left: 100%; }
}
.discount-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.9rem;
    animation: pulse-badge 2s ease-in-out infinite;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    overflow: hidden;
}
.discount-badge::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shine 3s ease-in-out infinite;
}

/* OFERTAS */
.ft-ofertas .oferta-card {
    background: #0f172a;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    position: relative;
}
.ft-ofertas .oferta-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(220, 38, 38, 0.3);
}
.ft-ofertas .oferta-card img {
    transition: transform 0.5s ease;
}
.ft-ofertas .oferta-card:hover img {
    transform: scale(1.08);
}
.ft-ofertas .oferta-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #dc2626, #f59e0b);
    transform: scaleX(0);
    transition: transform 0.4s ease;
    z-index: 10;
}
.ft-ofertas .oferta-card:hover::before {
    transform: scaleX(1);
}

/* TOURS */
.ft-tours .tour-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    position: relative;
}
.ft-tours .tour-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2);
}
.ft-tours .tour-card img {
    transition: transform 0.5s ease;
}
.ft-tours .tour-card:hover img {
    transform: scale(1.08);
}
.ft-tours .tour-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #2563eb;
    transform: scaleX(0);
    transition: transform 0.4s ease;
}
.ft-tours .tour-card:hover::after {
    transform: scaleX(1);
}

/* DESTINOS */
.ft-destinos .destino-card {
    display: block;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    aspect-ratio: 4/3;
    text-decoration: none;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.ft-destinos .destino-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2);
}
.ft-destinos .destino-card img {
    transition: transform 0.5s ease;
}
.ft-destinos .destino-card:hover img {
    transform: scale(1.08);
}
.ft-destinos .destino-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #06b6d4);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}
.ft-destinos .destino-card:hover::after {
    transform: scaleX(1);
}

/* BOTONES CON EFECTOS */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #2563eb;
    color: white;
    padding: 14px 28px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
    background: #1d4ed8;
}
.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid white;
    color: white;
    padding: 14px 28px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.3s ease, background 0.3s ease;
}
.btn-secondary:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,0.15);
}

/* ANIMACIONES AL SCROLL */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.animate-on-scroll.visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 1024px) {
    .ft-ofertas { grid-template-columns: repeat(2, 1fr); }
    .ft-tours { grid-template-columns: repeat(2, 1fr); }
    .ft-destinos { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .ft-continentes { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .ft-continentes > a:nth-child(5) { grid-column: span 2; }
    .ft-ofertas { grid-template-columns: 1fr; gap: 16px; }
    .ft-tours { grid-template-columns: 1fr; }
    .ft-destinos { grid-template-columns: 1fr; }
    
    /* Reducir efectos en móvil */
    .ft-continentes a:hover,
    .ft-ofertas .oferta-card:hover,
    .ft-tours .tour-card:hover,
    .ft-destinos .destino-card:hover {
        transform: translateY(-5px);
    }
}

</style>

<?php if ($show_hero): ?>
<!-- HERO -->
<section style="position: relative; min-height: 100vh; display: flex; align-items: center; overflow: hidden;">
    <?php if ($is_carousel): ?>
    <div style="position: absolute; inset: 0;">
        <?php foreach ($hero_images as $i => $img): ?>
        <div class="hero-slide" style="position: absolute; inset: 0; opacity: <?php echo $i === 0 ? '1' : '0'; ?>; transition: opacity 1s;">
            <img src="<?php echo esc_url($img); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <?php endforeach; ?>
        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(10,22,40,0.8) 0%, rgba(10,22,40,0.4) 100%);"></div>
    </div>
    <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px; z-index: 20;">
        <?php foreach ($hero_images as $i => $img): ?>
        <button class="hero-dot" data-index="<?php echo $i; ?>" style="width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; background: <?php echo $i === 0 ? 'white' : 'transparent'; ?>; cursor: pointer;"></button>
        <?php endforeach; ?>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var slides = document.querySelectorAll('.hero-slide');
        var dots = document.querySelectorAll('.hero-dot');
        var current = 0, total = slides.length, speed = <?php echo $hero_speed; ?>;
        function goTo(n) {
            slides[current].style.opacity = '0';
            dots[current].style.background = 'transparent';
            current = (n + total) % total;
            slides[current].style.opacity = '1';
            dots[current].style.background = 'white';
        }
        setInterval(function() { goTo(current + 1); }, speed);
        dots.forEach(function(dot, i) { dot.onclick = function() { goTo(i); }; });
    });
    </script>
    <?php else: ?>
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($hero_images[0] ?? $hero_img_1); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(10,22,40,0.8) 0%, rgba(10,22,40,0.4) 100%);"></div>
    </div>
    <?php endif; ?>
    
    <div class="container" style="position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 120px 20px 80px;">
        <span style="display: inline-block; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.9rem; margin-bottom: 24px;"><?php echo esc_html($hero_badge); ?></span>
        <h1 style="font-family: var(--font-display); font-size: clamp(2.2rem, 6vw, 4.5rem); color: white; max-width: 700px; line-height: 1.1; margin-bottom: 24px;"><?php echo esc_html($hero_title); ?></h1>
        <p style="font-size: 1.15rem; color: rgba(255,255,255,0.9); max-width: 500px; line-height: 1.7; margin-bottom: 28px;"><?php echo esc_html($hero_subtitle); ?></p>
        
        <!-- BUSCADOR -->
        <div class="hero-search">
            <form class="hero-search-form" action="<?php echo home_url('/'); ?>" method="get" autocomplete="off">
                <input type="text" name="s" class="hero-search-input" id="hero-search-input" placeholder="Buscar destinos, tours..." autocomplete="off">
                <input type="hidden" name="post_type" value="destino,paquete,oferta">
                <button type="submit" class="hero-search-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
            <div class="hero-search-results" id="hero-search-results"></div>
        </div>
        
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?php echo !empty($hero_btn1_url) ? esc_url($hero_btn1_url) : get_post_type_archive_link('destino'); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; font-weight: 600; text-decoration: none;"><?php echo esc_html($hero_btn1); ?></a>
            <a href="<?php echo !empty($hero_btn2_url) ? esc_url($hero_btn2_url) : get_post_type_archive_link('oferta'); ?>" style="display: inline-flex; align-items: center; gap: 8px; border: 2px solid white; color: white; padding: 14px 28px; border-radius: 8px; font-weight: 600; text-decoration: none;"><?php echo esc_html($hero_btn2); ?></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_continentes && $continentes && !is_wp_error($continentes)): ?>
<!-- CONTINENTES -->
<section style="padding: 80px 0; background: white;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="color: #2563eb; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;"><?php echo esc_html($continentes_subtitle); ?></span>
            <h2 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 2.75rem); margin-top: 12px; color: #0a1628;"><?php echo esc_html($continentes_title); ?></h2>
            <?php if ($text_continentes): ?>
            <div style="color: #64748b; max-width: 700px; margin: 16px auto 0; line-height: 1.8;"><?php echo wp_kses_post($text_continentes); ?></div>
            <?php endif; ?>
        </div>
        <div class="ft-continentes">
            <?php foreach ($continentes as $cont): $img = get_term_meta($cont->term_id, 'continente_imagen', true) ?: 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600&q=80'; ?>
            <a href="<?php echo get_term_link($cont); ?>" style="display: block; position: relative; border-radius: 16px; overflow: hidden; aspect-ratio: 3/4; text-decoration: none;">
                <img src="<?php echo esc_url($img); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.8) 100%);"></div>
                <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; color: white;">
                    <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin: 0 0 4px;"><?php echo esc_html($cont->name); ?></h3>
                    <span style="font-size: 0.85rem; opacity: 0.8;"><?php echo $cont->count; ?> destinos</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_ofertas && $ofertas): ?>
<!-- OFERTAS -->
<section style="padding: 80px 0; background: #0a1628;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="color: #2563eb; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;"><?php echo esc_html($ofertas_subtitle); ?></span>
            <h2 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 2.75rem); margin-top: 12px; color: white;"><?php echo esc_html($ofertas_title); ?></h2>
            <?php if ($text_ofertas): ?>
            <div style="color: rgba(255,255,255,0.7); margin-top: 16px; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.8;"><?php echo wp_kses_post($text_ofertas); ?></div>
            <?php endif; ?>
        </div>
        <div class="ft-ofertas">
            <?php foreach ($ofertas as $post): setup_postdata($post);
                $precio = get_post_meta($post->ID, '_flavor_precio', true);
                $precio_oferta = get_post_meta($post->ID, '_flavor_precio_oferta', true);
                $duracion = get_post_meta($post->ID, '_flavor_duracion', true);
                $img = get_the_post_thumbnail_url($post->ID, 'large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $cont = get_the_terms($post->ID, 'continente'); $loc = ($cont && !is_wp_error($cont)) ? $cont[0]->name : '';
                $disc = ($precio && $precio_oferta && $precio > $precio_oferta) ? round((($precio - $precio_oferta) / $precio) * 100) : 0;
            ?>
            <article class="oferta-card">
                <a href="<?php the_permalink(); ?>" style="display: block; position: relative; aspect-ratio: 4/3;">
                    <img src="<?php echo $img; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php if ($disc > 0): ?><span class="discount-badge"><?php echo $disc; ?>% OFF</span><?php endif; ?>
                </a>
                <div style="padding: 20px;">
                    <?php if ($loc): ?><div style="color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-bottom: 8px;"><?php echo $loc; ?></div><?php endif; ?>
                    <h3 style="font-family: var(--font-display); font-size: 1.15rem; margin-bottom: 8px;"><a href="<?php the_permalink(); ?>" style="color: white; text-decoration: none;"><?php the_title(); ?></a></h3>
                    <?php if ($duracion): ?><div style="color: rgba(255,255,255,0.6); font-size: 0.85rem; margin-bottom: 16px;"><?php echo $duracion; ?></div><?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <?php if ($disc > 0): ?><span style="text-decoration: line-through; color: rgba(255,255,255,0.5); font-size: 0.85rem;">$<?php echo number_format($precio); ?></span><?php endif; ?>
                            <div style="font-size: 1.5rem; font-weight: 700; color: white;">$<?php echo number_format($precio_oferta ?: $precio); ?></div>
                        </div>
                        <a href="<?php the_permalink(); ?>" style="background: white; color: #0a1628; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 500;">Ver más</a>
                    </div>
                </div>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <div style="text-align: center; margin-top: 50px;">
            <a href="<?php echo get_post_type_archive_link('oferta'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 14px 32px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#1d4ed8';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#2563eb';this.style.transform='none'">Ver todas las ofertas</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_badges): ?>
<!-- BADGES -->
<section style="padding: 40px 0; background: #f9fafb; border-top: 1px solid #e5e7eb;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; text-align: center;">
            <div style="display: flex; align-items: center; gap: 12px;"><span style="font-weight: 500; color: #0a1628;"><?php echo esc_html($trust1); ?></span></div>
            <div style="display: flex; align-items: center; gap: 12px;"><span style="font-weight: 500; color: #0a1628;"><?php echo esc_html($trust2); ?></span></div>
            <div style="display: flex; align-items: center; gap: 12px;"><span style="font-weight: 500; color: #0a1628;"><?php echo esc_html($trust3); ?></span></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_tours && $tours): ?>
<!-- TOURS -->
<section style="padding: 80px 0; background: white;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="color: #2563eb; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;"><?php echo esc_html($tours_subtitle); ?></span>
            <h2 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 2.75rem); margin-top: 12px; color: #0a1628;"><?php echo esc_html($tours_title); ?></h2>
            <?php if ($text_tours): ?>
            <div style="color: #64748b; max-width: 700px; margin: 16px auto 0; line-height: 1.8;"><?php echo wp_kses_post($text_tours); ?></div>
            <?php endif; ?>
        </div>
        <div class="ft-tours">
            <?php foreach ($tours as $post): setup_postdata($post);
                $precio = get_post_meta($post->ID, '_flavor_precio', true);
                $precio_oferta = get_post_meta($post->ID, '_flavor_precio_oferta', true);
                $duracion = get_post_meta($post->ID, '_flavor_duracion', true);
                $img = get_the_post_thumbnail_url($post->ID, 'large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $cont = get_the_terms($post->ID, 'continente'); $loc = ($cont && !is_wp_error($cont)) ? $cont[0]->name : '';
            ?>
            <article class="tour-card">
                <a href="<?php the_permalink(); ?>" style="display: block; aspect-ratio: 4/3;"><img src="<?php echo $img; ?>" style="width: 100%; height: 100%; object-fit: cover;"></a>
                <div style="padding: 20px;">
                    <?php if ($loc): ?><div style="color: #6b7280; font-size: 0.85rem; margin-bottom: 8px;"><?php echo $loc; ?></div><?php endif; ?>
                    <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin: 0 0 12px;"><a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0a1628;"><?php the_title(); ?></a></h3>
                    <?php if ($duracion): ?><div style="color: #6b7280; font-size: 0.85rem; margin-bottom: 16px;"><?php echo $duracion; ?></div><?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">$<?php echo number_format($precio_oferta ?: $precio); ?></div>
                        <a href="<?php the_permalink(); ?>" style="background: #0a1628; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">Ver más</a>
                    </div>
                </div>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <div style="text-align: center; margin-top: 40px;"><a href="<?php echo home_url('/destinos/'); ?>" style="display: inline-block; border: 2px solid #0a1628; color: #0a1628; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none;">Ver todos los tours</a></div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_destinos && $destinos): ?>
<!-- DESTINOS -->
<section style="padding: 80px 0; background: #f9fafb;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="color: #2563eb; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;"><?php echo esc_html($destinos_subtitle); ?></span>
            <h2 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 2.75rem); margin-top: 12px; color: #0a1628;"><?php echo esc_html($destinos_title); ?></h2>
            <?php if ($text_destinos): ?>
            <div style="color: #64748b; max-width: 700px; margin: 16px auto 0; line-height: 1.8;"><?php echo wp_kses_post($text_destinos); ?></div>
            <?php endif; ?>
        </div>
        <div class="ft-destinos">
            <?php foreach ($destinos as $post): setup_postdata($post);
                $img = get_the_post_thumbnail_url($post->ID, 'large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $cont = get_the_terms($post->ID, 'continente'); $loc = ($cont && !is_wp_error($cont)) ? $cont[0]->name : '';
            ?>
            <a href="<?php the_permalink(); ?>" class="destino-card">
                <img src="<?php echo $img; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.8) 100%);"></div>
                <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; color: white;">
                    <?php if ($loc): ?><span style="font-size: 0.85rem; opacity: 0.8;"><?php echo $loc; ?></span><?php endif; ?>
                    <h3 style="font-family: var(--font-display); font-size: 1.5rem; margin: 4px 0 0;"><?php the_title(); ?></h3>
                </div>
            </a>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <div style="text-align: center; margin-top: 40px;"><a href="<?php echo get_post_type_archive_link('destino'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none;">Ver todos los destinos</a></div>
    </div>
</section>
<?php endif; ?>

<?php if ($show_cta): ?>
<!-- CTA -->
<section style="padding: 100px 0; background: #0a1628;">
    <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px; text-align: center;">
        <h2 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3rem); color: white; margin-bottom: 16px;"><?php echo esc_html($cta_title); ?></h2>
        <p style="color: rgba(255,255,255,0.8); font-size: 1.1rem; max-width: 600px; margin: 0 auto 32px;"><?php echo esc_html($cta_subtitle); ?></p>
        <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" style="display: inline-block; background: #25D366; color: white; padding: 16px 32px; border-radius: 8px; font-size: 1.1rem; font-weight: 600; text-decoration: none;" target="_blank"><?php echo esc_html($cta_button); ?></a>
        <?php if ($text_cta): ?>
        <div style="color: rgba(255,255,255,0.7); margin-top: 24px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.7; font-size: 0.95rem;"><?php echo wp_kses_post($text_cta); ?></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($show_newsletter): ?>
<!-- NEWSLETTER -->
<section style="padding: 60px 0; background: #111827;">
    <div class="container" style="max-width: 600px; margin: 0 auto; padding: 0 20px; text-align: center;">
        <h3 style="font-family: var(--font-display); font-size: 1.75rem; color: white; margin-bottom: 12px;"><?php echo esc_html($newsletter_title); ?></h3>
        <p style="color: rgba(255,255,255,0.7); margin-bottom: 24px;"><?php echo esc_html($newsletter_desc); ?></p>
        <form id="newsletter-form" style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
            <input type="email" name="email" placeholder="Tu correo electrónico" required style="flex: 1; min-width: 250px; padding: 14px 20px; border: none; border-radius: 8px; font-size: 1rem;">
            <button type="submit" style="background: #2563eb; color: white; padding: 14px 28px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: opacity 0.3s;">Suscribirme</button>
        </form>
    </div>
</section>
<?php endif; ?>



<?php get_footer(); ?>
