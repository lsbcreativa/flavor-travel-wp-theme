<?php
get_header();
global $wp_query; 
$total = $wp_query->found_posts;

// Opciones del Customizer
$page_title = get_theme_mod('flavor_destinos_title', 'Destinos');
$page_desc = get_theme_mod('flavor_destinos_desc', 'Explora destinos increíbles en los 5 continentes.');
$page_image = get_theme_mod('flavor_destinos_image', '');
if (empty($page_image)) {
    $page_image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1920&q=80';
}

$position = get_theme_mod('flavor_destinos_position', 'center');
$show_badge = get_theme_mod('flavor_destinos_show_badge', true);
$hide_empty_badge = get_theme_mod('flavor_destinos_hide_empty_badge', true);
$show_cta = get_theme_mod('flavor_destinos_show_cta', true);
$cta_text = get_theme_mod('flavor_destinos_cta_text', 'Consultar por WhatsApp');
$cta_url = get_theme_mod('flavor_destinos_cta_url', '');
$show_scroll = get_theme_mod('flavor_destinos_show_scroll', true);
$show_search = get_theme_mod('flavor_destinos_show_search', false);

// Si no hay URL, usar WhatsApp
if (empty($cta_url)) {
    $cta_url = 'https://wa.me/' . get_theme_mod('flavor_whatsapp', '00123456789') . '?text=' . urlencode('Hola, me interesa información sobre destinos');
}

// Posición del contenido
$align_items = 'center';
$padding_top = '0';
if ($position === 'top') {
    $align_items = 'flex-start';
    $padding_top = '120px';
} elseif ($position === 'bottom') {
    $align_items = 'flex-end';
    $padding_top = '0';
}

$continentes = get_terms(array('taxonomy' => 'continente', 'hide_empty' => false));
?>

<style>
/* HERO ANIMATIONS */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
    40% { transform: translateY(-10px) translateX(-50%); }
    60% { transform: translateY(-5px) translateX(-50%); }
}
.hero-content { animation: fadeInUp 0.8s ease-out; }
.scroll-indicator { animation: bounce 2s infinite; }

/* GRIDS */
.archive-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.cont-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
.cont-card {
    display: block;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    aspect-ratio: 4/5;
    transition: transform 0.3s;
}
.cont-card:hover { transform: translateY(-5px); }
.cont-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}
.archive-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.archive-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.archive-card .img-wrap {
    display: block;
    overflow: hidden;
    aspect-ratio: 16/9;
}
.archive-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s;
}
.archive-card:hover img { transform: scale(1.05); }

/* SEARCH BAR */
.hero-search {
    display: flex;
    max-width: 500px;
    margin-top: 24px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 6px;
}
.hero-search input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 14px 20px;
    color: white;
    font-size: 1rem;
    outline: none;
}
.hero-search input::placeholder { color: rgba(255,255,255,0.7); }
.hero-search button {
    background: #2563eb;
    border: none;
    padding: 14px 24px;
    border-radius: 50px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}
.hero-search button:hover { background: #1d4ed8; }

/* CTA BUTTON */
.hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #25D366;
    color: white;
    padding: 14px 28px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 24px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.hero-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
}

@media (max-width: 1024px) {
    .archive-grid { grid-template-columns: repeat(2, 1fr); }
    .cont-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .archive-grid { grid-template-columns: 1fr; }
    .cont-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<!-- HERO FULLSCREEN -->
<section style="position: relative; min-height: 100vh; min-height: 100dvh; display: flex; align-items: <?php echo $align_items; ?>; overflow: hidden;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($page_image); ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.3) 0%, rgba(10,22,40,0.6) 100%);"></div>
    </div>
    
    <div class="hero-content" style="position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: <?php echo $padding_top; ?> 20px 100px; width: 100%;">
        <nav style="margin-bottom: 20px; font-size: 0.9rem;">
            <a href="<?php echo home_url('/'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">Inicio</a>
            <span style="color: rgba(255,255,255,0.5); margin: 0 10px;">›</span>
            <span style="color: #fff;"><?php echo esc_html($page_title); ?></span>
        </nav>
        
        <?php if ($show_badge && (!$hide_empty_badge || $total > 0)): ?>
        <span style="display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 20px;">
            🗺️ <?php echo $total; ?> destinos disponibles
        </span>
        <?php endif; ?>
        
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2.5rem, 8vw, 4.5rem); color: #fff; margin: 0 0 16px;">
            <?php echo esc_html($page_title); ?>
        </h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.95); max-width: 600px; line-height: 1.7;">
            <?php echo wp_kses_post($page_desc); ?>
        </p>
        
        <?php if ($show_search): ?>
        <form class="hero-search" action="<?php echo home_url('/'); ?>" method="get">
            <input type="hidden" name="post_type" value="destino">
            <input type="text" name="s" placeholder="Buscar destinos...">
            <button type="submit">Buscar</button>
        </form>
        <?php endif; ?>
        
        <?php if ($show_cta): ?>
        <a href="<?php echo esc_url($cta_url); ?>" class="hero-cta" <?php echo strpos($cta_url, 'wa.me') !== false ? 'target="_blank"' : ''; ?>>
            <?php if (strpos($cta_url, 'wa.me') !== false): ?>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            <?php endif; ?>
            <?php echo esc_html($cta_text); ?>
        </a>
        <?php endif; ?>
    </div>
    
    <?php if ($show_scroll): ?>
    <div class="scroll-indicator" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); text-align: center; color: white; cursor: pointer;" onclick="document.querySelector('.content-section').scrollIntoView({behavior: 'smooth'})">
        <div style="font-size: 0.85rem; margin-bottom: 8px; opacity: 0.8;">Explorar</div>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
    <?php endif; ?>
</section>

<!-- CONTINENTES -->
<?php if ($continentes && !is_wp_error($continentes) && count($continentes) > 0): ?>
<section class="content-section" style="padding: 80px 0; background: white;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-family: 'DM Serif Display', serif; font-size: 2rem; text-align: center; margin-bottom: 40px;">Explora por Continente</h2>
        <div class="cont-grid">
            <?php foreach ($continentes as $cont): 
                $img = get_term_meta($cont->term_id, 'continente_imagen', true);
                if (empty($img)) $img = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600&q=80';
            ?>
            <a href="<?php echo get_term_link($cont); ?>" class="cont-card">
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($cont->name); ?>">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.8) 100%);"></div>
                <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; color: white;">
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.25rem; margin: 0 0 4px;"><?php echo esc_html($cont->name); ?></h3>
                    <span style="font-size: 0.85rem; opacity: 0.8;"><?php echo $cont->count; ?> destinos</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- TODOS LOS DESTINOS -->
<section class="<?php echo (!$continentes || is_wp_error($continentes) || count($continentes) == 0) ? 'content-section' : ''; ?>" style="background: #f8fafc; padding: 80px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-family: 'DM Serif Display', serif; font-size: 1.75rem; margin-bottom: 30px;">Todos los Destinos</h2>
        
        <?php if (have_posts()): ?>
        <div class="archive-grid">
            <?php while (have_posts()): the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                if (empty($img)) $img = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $cont = get_the_terms(get_the_ID(), 'continente');
                $loc = ($cont && !is_wp_error($cont)) ? $cont[0]->name : '';
            ?>
            <article class="archive-card">
                <a href="<?php the_permalink(); ?>" class="img-wrap">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                </a>
                <div style="padding: 24px;">
                    <?php if ($loc): ?><div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">📍 <?php echo $loc; ?></div><?php endif; ?>
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.25rem; margin: 0 0 12px;"><a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0f172a;"><?php the_title(); ?></a></h3>
                    <?php if (get_the_excerpt()): ?>
                    <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px; line-height: 1.6;"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" style="display: inline-flex; align-items: center; gap: 6px; color: #2563eb; font-weight: 600; text-decoration: none;">
                        Explorar
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(array('mid_size' => 2, 'prev_text' => '← Anterior', 'next_text' => 'Siguiente →')); ?>
        
        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🗺️</div>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 24px;">Aún no hay destinos publicados.</p>
            <a href="<?php echo home_url('/contacto/'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">Contáctanos</a>
        </div>
        <?php endif; ?>
    </div>
</section>



<?php 
$seo_text = get_theme_mod('flavor_destinos_seo_text', '');
$seo_image = get_theme_mod('flavor_destinos_seo_image', '');
$seo_layout = get_theme_mod('flavor_destinos_seo_layout', 'text-only');
if ($seo_text || $seo_image): 
    flavor_render_seo_block($seo_text, $seo_image, $seo_layout);
endif; 
?>

<?php get_footer(); ?>
