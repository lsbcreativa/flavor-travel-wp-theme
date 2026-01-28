<?php
get_header();
$term = get_queried_object();
$term_image = get_term_meta($term->term_id, 'continente_imagen', true);
if (empty($term_image)) {
    $term_image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1920&q=80';
}
global $wp_query;
$total = $wp_query->found_posts;

// Verificar si es un continente padre o un país (subcategoría)
$is_parent = ($term->parent == 0);
$parent_term = null;
$continent_id = $term->term_id; // Para filtrar búsquedas

if (!$is_parent) {
    $parent_term = get_term($term->parent, 'continente');
    $continent_id = $parent_term->term_id; // Usar el continente padre para filtros
}

// Obtener subcategorías (países) - para filtros
$filter_countries = array();
if ($is_parent) {
    // Si estamos en un continente, obtener sus países
    $filter_countries = get_terms(array(
        'taxonomy' => 'continente',
        'hide_empty' => false,
        'parent' => $term->term_id,
    ));
} else {
    // Si estamos en un país, obtener países hermanos del mismo continente
    $filter_countries = get_terms(array(
        'taxonomy' => 'continente',
        'hide_empty' => false,
        'parent' => $parent_term->term_id,
    ));
}
?>

<style>
/* GRID DE TOURS */
.tours-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.tour-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.tour-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.tour-card .img-wrap {
    display: block;
    overflow: hidden;
    aspect-ratio: 16/9;
    position: relative;
}
.tour-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.tour-card:hover img {
    transform: scale(1.05);
}
@media (max-width: 1024px) { .tours-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .tours-grid { grid-template-columns: 1fr; } }

/* BUSCADOR EN HERO - ESTILO GLASS */
.hero-search {
    max-width: 480px;
    margin-top: 28px;
    position: relative;
}
.hero-search-form {
    display: flex;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
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
    animation: fadeInUp 0.2s ease;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* BARRA DE FILTROS POR PAÍS */
.filter-bar {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 0;
    position: sticky;
    top: 70px;
    z-index: 50;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.filter-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}
.filter-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    margin-right: 8px;
}

/* PILLS DE PAÍSES */
.country-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex: 2;
}
.country-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}
.country-pill:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.country-pill.active {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}
.country-pill .count {
    background: rgba(0,0,0,0.1);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.75rem;
}
.country-pill.active .count {
    background: rgba(255,255,255,0.2);
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

/* SECCIÓN DE PAÍSES - Estilo navegación */
.paises-section {
    background: #eef2ff; /* Fondo azul claro para contrastar */
    padding: 40px 0;
    border-top: 1px solid #c7d2fe;
    border-bottom: 1px solid #c7d2fe;
}
.paises-section h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.5rem;
    margin: 0 0 24px;
    color: #1e3a8a;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
.paises-section h2::before {
    content: '🗺️';
    font-size: 1.25rem;
}
.paises-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
}
.pais-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px 12px 12px;
    background: white;
    border: 2px solid #e0e7ff;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
}
.pais-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(37, 99, 235, 0.18);
    border-color: #2563eb;
    background: #f8faff;
}
.pais-card-img {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.pais-card-overlay {
    display: none;
}
.pais-card-content {
    position: static;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.pais-card h3 {
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 600;
    margin: 0;
}
.pais-card span {
    color: #2563eb;
    font-size: 0.85rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .hero-search {
        max-width: 100%;
    }
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
    .filter-container {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .filter-label {
        display: none;
    }
    .country-filters {
        justify-content: flex-start;
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .country-pill {
        white-space: nowrap;
        flex-shrink: 0;
    }
    /* PAÍSES EN MÓVIL */
    .paises-section {
        padding: 24px 0;
    }
    .paises-section h2 {
        font-size: 1.2rem;
        margin-bottom: 16px;
    }
    .paises-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .pais-card {
        padding: 12px;
        border-radius: 14px;
        gap: 10px;
    }
    .pais-card-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
    }
    .pais-card h3 {
        font-size: 0.95rem;
    }
    .pais-card span {
        font-size: 0.8rem;
    }
}
</style>

<!-- HERO FULLSCREEN -->
<section style="position: relative; height: 100vh; display: flex; align-items: center; overflow: hidden;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($term_image); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.3) 0%, rgba(10,22,40,0.6) 100%);"></div>
    </div>
    <div style="position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 0 20px; width: 100%;">
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            <a href="<?php echo home_url('/'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">Inicio</a>
            <span style="color: rgba(255,255,255,0.5);">›</span>
            <?php if ($parent_term): ?>
            <a href="<?php echo get_term_link($parent_term); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;"><?php echo esc_html($parent_term->name); ?></a>
            <span style="color: rgba(255,255,255,0.5);">›</span>
            <?php endif; ?>
            <span style="color: #fff;"><?php echo esc_html($term->name); ?></span>
        </nav>
        <span style="display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 20px;">
            🌴 <?php echo $total; ?> tours disponibles
        </span>
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(3rem, 10vw, 5rem); color: #fff; margin: 0 0 20px;">
            <?php echo esc_html($term->name); ?>
        </h1>
        <?php if ($term->description): ?>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.95); max-width: 600px; line-height: 1.7; margin-bottom: 0;">
            <?php echo esc_html($term->description); ?>
        </p>
        <?php endif; ?>

        <!-- BUSCADOR EN HERO -->
        <div class="hero-search">
            <form class="hero-search-form" action="<?php echo home_url('/'); ?>" method="get" autocomplete="off">
                <input type="text" name="s" class="hero-search-input" id="continent-search-input" placeholder="Buscar tours en <?php echo esc_attr($term->name); ?>..." autocomplete="off">
                <input type="hidden" name="post_type" value="paquete">
                <input type="hidden" name="continente" value="<?php echo esc_attr($is_parent ? $term->slug : $parent_term->slug); ?>">
                <button type="submit" class="hero-search-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
            <div class="hero-search-results" id="continent-search-results"></div>
        </div>
    </div>
</section>

<!-- BARRA DE FILTROS POR PAÍS -->
<?php if (!empty($filter_countries) && !is_wp_error($filter_countries)): ?>
<div class="filter-bar">
    <div class="filter-container">
        <span class="filter-label">Filtrar por:</span>
        <div class="country-filters">
            <!-- Pill "Todos" para volver al continente -->
            <?php if (!$is_parent): ?>
            <a href="<?php echo get_term_link($parent_term); ?>" class="country-pill">
                Todos
            </a>
            <?php else: ?>
            <span class="country-pill active">
                Todos <span class="count"><?php echo $total; ?></span>
            </span>
            <?php endif; ?>

            <?php foreach ($filter_countries as $country): ?>
            <a href="<?php echo get_term_link($country); ?>" class="country-pill <?php echo ($country->term_id === $term->term_id) ? 'active' : ''; ?>">
                <?php echo esc_html($country->name); ?>
                <?php if ($country->count > 0): ?>
                <span class="count"><?php echo $country->count; ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- PAÍSES - Navegación por país -->
<?php if ($is_parent && !empty($filter_countries) && !is_wp_error($filter_countries)): ?>
<section class="paises-section">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <h2>Explora por país</h2>
        <div class="paises-grid">
            <?php foreach ($filter_countries as $pais):
                $pais_image = get_term_meta($pais->term_id, 'continente_imagen', true);
                if (empty($pais_image)) {
                    $pais_image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=400&q=80';
                }
            ?>
            <a href="<?php echo get_term_link($pais); ?>" class="pais-card">
                <img src="<?php echo esc_url($pais_image); ?>" alt="<?php echo esc_attr($pais->name); ?>" class="pais-card-img">
                <div class="pais-card-content">
                    <h3><?php echo esc_html($pais->name); ?></h3>
                    <?php if ($pais->count > 0): ?>
                    <span><?php echo $pais->count; ?> tours</span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- TOURS -->
<section style="background: #f8fafc; padding: 80px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <?php if (have_posts()): ?>
        <h2 style="font-family: 'DM Serif Display', serif; font-size: 2rem; margin: 0 0 30px; color: #0f172a;">
            <?php echo $is_parent ? 'Todos los tours en ' . esc_html($term->name) : 'Tours en ' . esc_html($term->name); ?>
        </h2>
        <div class="tours-grid">
            <?php while (have_posts()): the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                if (!$img) $img = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $precio = get_post_meta(get_the_ID(), '_flavor_precio', true);
                $precio_oferta = get_post_meta(get_the_ID(), '_flavor_precio_oferta', true);
                $duracion = get_post_meta(get_the_ID(), '_flavor_duracion', true);
            ?>
            <article class="tour-card">
                <a href="<?php the_permalink(); ?>" class="img-wrap">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php if ($duracion): ?>
                    <span style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.7); color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem;">
                        ⏱️ <?php echo esc_html($duracion); ?>
                    </span>
                    <?php endif; ?>
                </a>
                <div style="padding: 24px;">
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.25rem; margin: 0 0 16px;">
                        <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0f172a;"><?php the_title(); ?></a>
                    </h3>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <div>
                            <?php if ($precio_oferta && $precio > $precio_oferta): ?>
                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.85rem;">$<?php echo number_format($precio); ?></span>
                            <?php endif; ?>
                            <span style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">$<?php echo number_format($precio_oferta ?: $precio ?: 0); ?></span>
                        </div>
                        <a href="<?php the_permalink(); ?>" style="background: #0f172a; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem;">Ver detalles</a>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '← Anterior',
            'next_text' => 'Siguiente →',
        )); ?>

        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🌴</div>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 24px;">Aún no hay tours en <?php echo esc_html($term->name); ?>.</p>
            <a href="<?php echo home_url('/contacto/'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">Contáctanos</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript para búsqueda en tiempo real -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('continent-search-input');
    const results = document.getElementById('continent-search-results');
    const continenteSlug = '<?php echo esc_js($is_parent ? $term->slug : $parent_term->slug); ?>';
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
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=flavor_search&q=' + encodeURIComponent(query) + '&continente=' + encodeURIComponent(continenteSlug))
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
