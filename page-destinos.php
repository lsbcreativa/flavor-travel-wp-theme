<?php
/**
 * Template Name: Página de Destinos
 * Description: Muestra todos los continentes con buscador
 */
get_header();

// Obtener todos los continentes (solo padres)
$continentes = get_terms(array(
    'taxonomy' => 'continente',
    'hide_empty' => false,
    'parent' => 0,
));

// Contar total de tours
$total_tours = wp_count_posts('paquete')->publish;

// Opciones del Customizer
$page_title = get_theme_mod('flavor_destinos_title', 'Nuestros Destinos');
$page_desc = get_theme_mod('flavor_destinos_desc', 'Explora los mejores destinos del mundo. Desde playas paradisíacas hasta montañas majestuosas.');
$page_image = get_theme_mod('flavor_destinos_image', '');
if (empty($page_image)) {
    $page_image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1920&q=80';
}

// Bloque de contenido adicional
$destinos_content = get_theme_mod('flavor_destinos_seo_text', '');
$destinos_content_image = get_theme_mod('flavor_destinos_seo_image', '');
$destinos_layout = get_theme_mod('flavor_destinos_seo_layout', 'text-only');
$destinos_content_image_url = $destinos_content_image ? wp_get_attachment_image_url($destinos_content_image, 'large') : '';
?>

<style>
/* HERO */
.destinos-hero {
    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    align-items: center;
    overflow: hidden;
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
.destinos-hero-bg {
    position: absolute;
    inset: 0;
}
.destinos-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.destinos-hero-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(10,22,40,0.4) 0%, rgba(10,22,40,0.7) 100%);
}
.destinos-hero-content {
    position: relative;
    z-index: 10;
    max-width: 1280px;
    margin: 0 auto;
    padding: 120px 20px 100px;
    width: 100%;
    text-align: center;
}

/* BUSCADOR */
.destinos-search {
    max-width: 500px;
    margin-top: 28px;
    position: relative;
}
.destinos-search-form {
    display: flex;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.destinos-search-form:focus-within {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.4);
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}
.destinos-search-input {
    flex: 1;
    padding: 16px 24px;
    border: none;
    font-size: 1rem;
    outline: none;
    background: transparent;
    color: white;
}
.destinos-search-input::placeholder {
    color: rgba(255,255,255,0.6);
}
.destinos-search-btn {
    background: transparent;
    color: white;
    border: none;
    padding: 16px 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: all 0.3s;
    opacity: 0.7;
}
.destinos-search-btn:hover {
    opacity: 1;
}
.destinos-search-results {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    max-height: 350px;
    overflow-y: auto;
    display: none;
    z-index: 100;
}
.destinos-search-results.active {
    display: block;
    animation: fadeIn 0.2s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* GRID DE CONTINENTES */
.continentes-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 24px;
    max-width: 1100px;
    margin: 0 auto;
}
.continente-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    aspect-ratio: 4/3;
    text-decoration: none;
    display: block;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    width: calc((100% - 48px) / 3); /* 3 columnas con gap */
}
.continente-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
.continente-card img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.continente-card:hover img {
    transform: scale(1.08);
}
.continente-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 30%, rgba(10,22,40,0.85) 100%);
}
.continente-card-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 24px;
    color: white;
}
.continente-card-title {
    font-family: 'DM Serif Display', serif;
    font-size: 1.75rem;
    margin: 0 0 8px;
}
.continente-card-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 0.9rem;
    opacity: 0.9;
}
.continente-card-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #2563eb;
    color: white;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* RESULTADO DE BÚSQUEDA */
.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: #1f2937;
    transition: background 0.15s;
    border-bottom: 1px solid #f1f5f9;
}
.search-result-item:last-child {
    border-bottom: none;
}
.search-result-item:hover {
    background: #f8fafc;
}
.search-result-img {
    width: 56px;
    height: 42px;
    border-radius: 8px;
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
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.search-result-meta {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 2px;
}
.search-result-price {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2563eb;
}
.search-no-results {
    padding: 24px 16px;
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
    .continente-card {
        width: calc((100% - 24px) / 2); /* 2 columnas */
    }
}
@media (max-width: 640px) {
    .continentes-grid {
        gap: 16px;
        display: block; /* Cambiar a block en móvil para evitar problemas de flexbox */
    }
    .continente-card {
        width: 100%;
        height: 200px; /* Altura fija en móvil */
        aspect-ratio: auto; /* Quitar aspect-ratio, usar altura fija */
        margin-bottom: 16px;
    }
    .continente-card:last-child {
        margin-bottom: 0;
    }
    .destinos-search {
        max-width: 100%;
    }
    .continente-card-title {
        font-size: 1.5rem;
    }
    .continente-card-content {
        padding: 20px;
    }
    .continente-card-meta {
        font-size: 0.8rem;
        gap: 8px;
    }
}

/* BLOQUE DE CONTENIDO */
.destinos-content-block {
    padding: 0 0 80px 0; /* Sin padding arriba, 80px abajo */
    background: #f8fafc; /* Mismo fondo que la sección de continentes */
}
.destinos-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}
.destinos-content-grid.reverse {
    direction: rtl;
}
.destinos-content-grid.reverse > * {
    direction: ltr;
}
.destinos-content-grid img {
    width: 100%;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}
.destinos-content-text {
    color: #4b5563;
    line-height: 1.9;
    font-size: 1.05rem;
}
.destinos-content-text a {
    color: #2563eb;
    text-decoration: underline;
}
@media (max-width: 768px) {
    .destinos-content-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .destinos-content-grid.reverse {
        direction: ltr;
    }
}
</style>

<!-- HERO -->
<section class="destinos-hero">
    <div class="destinos-hero-bg">
        <img src="<?php echo esc_url($page_image); ?>" alt="Destinos">
    </div>
    <div class="destinos-hero-content">
        <nav style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            <a href="<?php echo home_url('/'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">Inicio</a>
            <span style="color: rgba(255,255,255,0.5);">›</span>
            <span style="color: #fff;">Destinos</span>
        </nav>

        <span style="display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 20px;">
            🌍 <?php echo count($continentes); ?> continentes · <?php echo $total_tours; ?> tours
        </span>

        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2.5rem, 8vw, 4rem); color: #fff; margin: 0 0 16px; max-width: 700px; margin-left: auto; margin-right: auto;">
            <?php echo esc_html($page_title); ?>
        </h1>

        <p style="font-size: 1.15rem; color: rgba(255,255,255,0.9); max-width: 600px; line-height: 1.7; margin: 0 auto 0;">
            <?php echo esc_html($page_desc); ?>
        </p>

        <!-- BUSCADOR -->
        <div class="destinos-search" style="margin-left: auto; margin-right: auto;">
            <form class="destinos-search-form" action="<?php echo home_url('/'); ?>" method="get" autocomplete="off">
                <input type="text" name="s" class="destinos-search-input" id="destinos-search-input" placeholder="Buscar destinos, tours, experiencias..." autocomplete="off">
                <input type="hidden" name="post_type" value="paquete">
                <button type="submit" class="destinos-search-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
            <div class="destinos-search-results" id="destinos-search-results"></div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <span style="display: block; margin-bottom: 8px;">Ver más</span>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
    </div>
</section>

<!-- CONTINENTES -->
<section style="background: #f8fafc; padding: 80px 0 40px;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-family: 'DM Serif Display', serif; font-size: clamp(1.75rem, 4vw, 2.5rem); color: #0f172a; margin: 0 0 16px;">Explora por Continente</h2>
            <p style="color: #64748b; max-width: 600px; margin: 0 auto; line-height: 1.7;">Selecciona un continente para descubrir todos los destinos y tours disponibles</p>
        </div>

        <?php if ($continentes && !is_wp_error($continentes)): ?>
        <div class="continentes-grid">
            <?php foreach ($continentes as $continente):
                $img = get_term_meta($continente->term_id, 'continente_imagen', true);
                if (empty($img)) {
                    $img = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=800&q=80';
                }

                // Contar tours en este continente (incluyendo subcategorías) - sin duplicados
                $children = get_terms(array(
                    'taxonomy' => 'continente',
                    'parent' => $continente->term_id,
                    'hide_empty' => false,
                    'fields' => 'ids',
                ));
                $term_ids = array($continente->term_id);
                if (!is_wp_error($children) && !empty($children)) {
                    $term_ids = array_merge($term_ids, $children);
                }

                // Query real para contar tours únicos
                $tour_count_query = new WP_Query(array(
                    'post_type' => array('paquete', 'salida_confirmada', 'evento_deportivo'),
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'continente',
                            'field' => 'term_id',
                            'terms' => $term_ids,
                        ),
                    ),
                ));
                $tour_count = $tour_count_query->found_posts;
                wp_reset_postdata();

                // Reconvertir children para el conteo de países
                $children = get_terms(array(
                    'taxonomy' => 'continente',
                    'parent' => $continente->term_id,
                    'hide_empty' => false,
                ));

                // Contar países
                $paises_count = !is_wp_error($children) ? count($children) : 0;
            ?>
            <a href="<?php echo get_term_link($continente); ?>" class="continente-card">
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($continente->name); ?>">
                <div class="continente-card-overlay"></div>
                <?php if ($tour_count > 0): ?>
                <span class="continente-card-badge"><?php echo $tour_count; ?> tours</span>
                <?php endif; ?>
                <div class="continente-card-content">
                    <h3 class="continente-card-title"><?php echo esc_html($continente->name); ?></h3>
                    <div class="continente-card-meta">
                        <?php if ($paises_count > 0): ?>
                        <span><?php echo $paises_count; ?> países</span>
                        <?php endif; ?>
                        <?php if ($continente->description): ?>
                        <span><?php echo wp_trim_words($continente->description, 8); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🌍</div>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 24px;">Aún no hay continentes configurados.</p>
            <a href="<?php echo home_url('/contacto/'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">Contáctanos</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- BLOQUE DE CONTENIDO ADICIONAL -->
<?php if ($destinos_content): ?>
<section class="destinos-content-block">
    <?php if ($destinos_layout === 'text-only' || empty($destinos_content_image_url)): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <div class="destinos-content-text">
                <?php echo wp_kses_post($destinos_content); ?>
            </div>
        </div>
    <?php elseif ($destinos_layout === 'image-left'): ?>
        <div class="destinos-content-grid">
            <div>
                <img src="<?php echo esc_url($destinos_content_image_url); ?>" alt="">
            </div>
            <div class="destinos-content-text">
                <?php echo wp_kses_post($destinos_content); ?>
            </div>
        </div>
    <?php elseif ($destinos_layout === 'image-right'): ?>
        <div class="destinos-content-grid reverse">
            <div>
                <img src="<?php echo esc_url($destinos_content_image_url); ?>" alt="">
            </div>
            <div class="destinos-content-text">
                <?php echo wp_kses_post($destinos_content); ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- CTA -->
<section style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 80px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px; text-align: center;">
        <h2 style="font-family: 'DM Serif Display', serif; font-size: clamp(1.75rem, 4vw, 2.5rem); color: white; margin: 0 0 16px;">¿No encuentras lo que buscas?</h2>
        <p style="color: rgba(255,255,255,0.9); max-width: 500px; margin: 0 auto 28px; line-height: 1.7;">Contáctanos y te ayudaremos a encontrar el destino perfecto para ti</p>
        <a href="<?php echo home_url('/contacto/'); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: white; color: #1e3a8a; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Contáctanos
        </a>
    </div>
</section>

<!-- JavaScript para búsqueda -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('destinos-search-input');
    const results = document.getElementById('destinos-search-results');
    let timeout;

    if (!input || !results) return;

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();

        if (query.length < 2) {
            results.classList.remove('active');
            return;
        }

        timeout = setTimeout(function() {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=flavor_search&q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        results.innerHTML = '<div class="search-no-results">No se encontraron resultados para "<strong>' + query + '</strong>"</div>';
                        results.classList.add('active');
                        return;
                    }

                    let html = '';
                    data.forEach(item => {
                        const price = item.price ? '$' + Number(item.price).toLocaleString() : '';
                        html += '<a href="' + item.url + '" class="search-result-item">';
                        html += '<img src="' + item.image + '" class="search-result-img" alt="">';
                        html += '<div class="search-result-info">';
                        html += '<div class="search-result-title">' + item.title + '</div>';
                        html += '<div class="search-result-meta">' + item.type + (item.location ? ' • ' + item.location : '') + '</div>';
                        html += '</div>';
                        if (price) html += '<div class="search-result-price">' + price + '</div>';
                        html += '</a>';
                    });
                    results.innerHTML = html;
                    results.classList.add('active');
                })
                .catch(() => {
                    results.innerHTML = '<div class="search-no-results">Error al buscar. Intenta de nuevo.</div>';
                    results.classList.add('active');
                });
        }, 300);
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.classList.remove('active');
        }
    });

    // Cerrar al presionar Escape
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            results.classList.remove('active');
            input.blur();
        }
    });
});
</script>

<?php get_footer(); ?>
