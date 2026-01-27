<?php
get_header();

// Query personalizada: todos los posts con precio de oferta (destinos, paquetes, ofertas)
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$today = current_time('Y-m-d');

$args = array(
    'post_type' => array('destino', 'paquete', 'oferta'),
    'posts_per_page' => 12,
    'paged' => $paged,
    'meta_query' => array(
        'relation' => 'AND',
        // Debe tener precio de oferta
        array(
            'key' => '_flavor_precio_oferta',
            'value' => '',
            'compare' => '!='
        ),
        array(
            'key' => '_flavor_precio_oferta',
            'value' => '0',
            'compare' => '>'
        ),
        // Vigencia
        array(
            'relation' => 'OR',
            array(
                'key' => '_flavor_siempre_visible',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array('key' => '_flavor_fecha_inicio', 'compare' => 'NOT EXISTS'),
                    array('key' => '_flavor_fecha_inicio', 'value' => '', 'compare' => '='),
                    array('key' => '_flavor_fecha_inicio', 'value' => $today, 'compare' => '<=', 'type' => 'DATE')
                ),
                array(
                    'relation' => 'OR',
                    array('key' => '_flavor_fecha_fin', 'compare' => 'NOT EXISTS'),
                    array('key' => '_flavor_fecha_fin', 'value' => '', 'compare' => '='),
                    array('key' => '_flavor_fecha_fin', 'value' => $today, 'compare' => '>=', 'type' => 'DATE')
                )
            )
        )
    ),
    'orderby' => 'meta_value_num',
    'meta_key' => '_flavor_precio_oferta',
    'order' => 'ASC'
);

$ofertas_query = new WP_Query($args);
$total = $ofertas_query->found_posts;

// Opciones del Customizer
$page_title = get_theme_mod('flavor_ofertas_title', 'Ofertas');
$page_desc = get_theme_mod('flavor_ofertas_desc', 'Aprovecha nuestras ofertas especiales y descuentos exclusivos.');
$page_image = get_theme_mod('flavor_ofertas_image', '');
if (empty($page_image)) {
    $page_image = 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1920&q=80';
}

$position = get_theme_mod('flavor_ofertas_position', 'center');
$show_badge = get_theme_mod('flavor_ofertas_show_badge', true);
$hide_empty_badge = get_theme_mod('flavor_ofertas_hide_empty_badge', true);
$show_cta = get_theme_mod('flavor_ofertas_show_cta', true);
$cta_text = get_theme_mod('flavor_ofertas_cta_text', 'Ver ofertas ahora');
$cta_url = get_theme_mod('flavor_ofertas_cta_url', '');
$show_scroll = get_theme_mod('flavor_ofertas_show_scroll', true);

if (empty($cta_url)) {
    $cta_url = 'https://wa.me/' . get_theme_mod('flavor_whatsapp', '00123456789') . '?text=' . urlencode('Hola, me interesan las ofertas especiales');
}

$align_items = 'center';
$padding_top = '0';
if ($position === 'top') {
    $align_items = 'flex-start';
    $padding_top = '120px';
} elseif ($position === 'bottom') {
    $align_items = 'flex-end';
}
?>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
    40% { transform: translateY(-10px) translateX(-50%); }
    60% { transform: translateY(-5px) translateX(-50%); }
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.hero-content { animation: fadeInUp 0.8s ease-out; }
.scroll-indicator { animation: bounce 2s infinite; }

.ofertas-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.oferta-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.oferta-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.oferta-card .img-wrap {
    position: relative;
    display: block;
    aspect-ratio: 16/10;
    overflow: hidden;
}
.oferta-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}
.oferta-card:hover img {
    transform: scale(1.08);
}
.oferta-card .discount {
    position: absolute;
    top: 16px;
    left: 16px;
    background: #dc2626;
    color: white;
    padding: 6px 14px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.9rem;
}
.oferta-card .type-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@media (max-width: 1024px) {
    .ofertas-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .ofertas-grid { grid-template-columns: 1fr; }
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 50px;
}
.pagination-container .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    margin: 0 4px;
    padding: 0 12px;
    background: white;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    font-weight: 500;
    transition: all 0.2s;
}
.pagination-container .page-numbers:hover {
    background: #2563eb;
    color: white;
}
.pagination-container .page-numbers.current {
    background: #2563eb;
    color: white;
}
</style>

<!-- HERO -->
<section class="page-hero" style="position: relative; min-height: 100vh; min-height: 100dvh; display: flex; overflow: hidden; align-items: <?php echo $align_items; ?>; padding-top: <?php echo $padding_top; ?>;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($page_image); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.4) 0%, rgba(10,22,40,0.85) 100%);"></div>
    </div>
    
    <div class="hero-content" style="position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 120px 20px 80px; width: 100%; text-align: center;">
        <?php if ($show_badge && ($total > 0 || !$hide_empty_badge)): ?>
        <div style="display: inline-block; background: rgba(220, 38, 38, 0.9); padding: 8px 20px; border-radius: 50px; margin-bottom: 20px; animation: pulse 2s infinite;">
            <span style="color: white; font-size: 0.9rem; font-weight: 600;">🔥 <?php echo $total; ?> oferta<?php echo $total != 1 ? 's' : ''; ?> disponible<?php echo $total != 1 ? 's' : ''; ?></span>
        </div>
        <?php endif; ?>
        
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2.5rem, 6vw, 4rem); color: white; margin: 0 0 20px; text-shadow: 0 4px 30px rgba(0,0,0,0.3);">
            <?php echo esc_html($page_title); ?>
        </h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9); max-width: 600px; margin: 0 auto 30px; line-height: 1.7;">
            <?php echo wp_kses_post($page_desc); ?>
        </p>
        
        <?php if ($show_cta): ?>
        <a href="<?php echo esc_url($cta_url); ?>" style="display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%); color: white; padding: 16px 32px; border-radius: 50px; font-weight: 600; text-decoration: none; box-shadow: 0 8px 30px rgba(220, 38, 38, 0.4); transition: transform 0.3s, box-shadow 0.3s;" target="_blank" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <?php echo esc_html($cta_text); ?>
        </a>
        <?php endif; ?>
    </div>
    
    <?php if ($show_scroll): ?>
    <div class="scroll-indicator" style="position: absolute; bottom: 30px; left: 50%; z-index: 10;">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
    </div>
    <?php endif; ?>
</section>

<!-- OFERTAS -->
<section class="content-section" style="background: #f8fafc; padding: 80px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <?php if ($ofertas_query->have_posts()): ?>
        <div class="ofertas-grid">
            <?php while ($ofertas_query->have_posts()): $ofertas_query->the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                if (empty($img)) $img = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $precio = get_post_meta(get_the_ID(), '_flavor_precio', true);
                $precio_oferta = get_post_meta(get_the_ID(), '_flavor_precio_oferta', true);
                $duracion = get_post_meta(get_the_ID(), '_flavor_duracion', true);
                $fecha_fin = get_post_meta(get_the_ID(), '_flavor_fecha_fin', true);
                $cont = get_the_terms(get_the_ID(), 'continente');
                $loc = ($cont && !is_wp_error($cont)) ? $cont[0]->name : '';
                $disc = ($precio && $precio_oferta && $precio > $precio_oferta) ? round((($precio - $precio_oferta) / $precio) * 100) : 0;
                $post_type = get_post_type();
                $type_label = ($post_type == 'destino') ? 'Destino' : (($post_type == 'paquete') ? 'Tour' : 'Oferta');
            ?>
            <article class="oferta-card">
                <a href="<?php the_permalink(); ?>" class="img-wrap">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php if ($disc > 0): ?><span class="discount">-<?php echo $disc; ?>%</span><?php endif; ?>
                    <span class="type-badge"><?php echo $type_label; ?></span>
                    <?php if ($fecha_fin): ?>
                    <span style="position: absolute; bottom: 12px; right: 12px; background: rgba(0,0,0,0.7); color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem;">
                        ⏰ Hasta <?php echo date('d/m', strtotime($fecha_fin)); ?>
                    </span>
                    <?php endif; ?>
                </a>
                <div style="padding: 24px;">
                    <?php if ($loc): ?><div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">📍 <?php echo $loc; ?></div><?php endif; ?>
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.25rem; margin: 0 0 8px;"><a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0f172a;"><?php the_title(); ?></a></h3>
                    <?php if ($duracion): ?><div style="color: #64748b; font-size: 0.85rem; margin-bottom: 16px;">⏱️ <?php echo esc_html($duracion); ?></div><?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <div>
                            <?php if ($disc > 0): ?>
                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.85rem; display: block;">$<?php echo number_format($precio); ?></span>
                            <?php endif; ?>
                            <span style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">$<?php echo number_format($precio_oferta ?: $precio ?: 0); ?></span>
                        </div>
                        <a href="<?php the_permalink(); ?>" style="background: #dc2626; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem;">¡Lo quiero!</a>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        
        <!-- Paginación -->
        <div class="pagination-container">
            <?php
            echo paginate_links(array(
                'total' => $ofertas_query->max_num_pages,
                'current' => $paged,
                'prev_text' => '← Anterior',
                'next_text' => 'Siguiente →',
            ));
            ?>
        </div>
        
        <?php wp_reset_postdata(); ?>
        
        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🏷️</div>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 12px;">Aún no hay ofertas publicadas.</p>
            <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 24px;">¡Pero tenemos destinos increíbles esperándote!</p>
            <a href="<?php echo get_post_type_archive_link('destino'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">Explorar destinos</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php 
$seo_text = get_theme_mod('flavor_ofertas_seo_text', '');
$seo_image = get_theme_mod('flavor_ofertas_seo_image', '');
$seo_layout = get_theme_mod('flavor_ofertas_seo_layout', 'text-only');
if ($seo_text || $seo_image): 
    flavor_render_seo_block($seo_text, $seo_image, $seo_layout);
endif; 
?>

<?php get_footer(); ?>
