<?php
get_header();
$query = get_search_query();
$total = $wp_query->found_posts;
?>

<style>
.search-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.search-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.search-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.search-card .img-wrap {
    position: relative;
    display: block;
    aspect-ratio: 16/10;
    overflow: hidden;
}
.search-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}
.search-card:hover img {
    transform: scale(1.08);
}
.search-card .type-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    text-transform: uppercase;
}
@media (max-width: 1024px) {
    .search-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .search-grid { grid-template-columns: 1fr; }
}
</style>

<!-- HERO -->
<section style="background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 100%); padding: 140px 0 80px;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2rem, 5vw, 3rem); color: white; margin-bottom: 16px;">
            Resultados de búsqueda
        </h1>
        <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem; margin-bottom: 30px;">
            <?php echo $total; ?> resultado<?php echo $total != 1 ? 's' : ''; ?> para "<strong><?php echo esc_html($query); ?></strong>"
        </p>
        
        <!-- Buscador -->
        <form action="<?php echo home_url('/'); ?>" method="get" style="display: flex; max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden;">
            <input type="text" name="s" value="<?php echo esc_attr($query); ?>" placeholder="Buscar destinos, tours..." style="flex: 1; padding: 16px 20px; border: none; font-size: 1rem; outline: none;">
            <input type="hidden" name="post_type" value="destino,paquete,oferta">
            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 16px 24px; cursor: pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
    </div>
</section>

<!-- RESULTADOS -->
<section style="padding: 80px 0; background: #f8fafc;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <?php if (have_posts()): ?>
        <div class="search-grid">
            <?php while (have_posts()): the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $precio = get_post_meta(get_the_ID(), '_flavor_precio', true);
                $precio_oferta = get_post_meta(get_the_ID(), '_flavor_precio_oferta', true);
                $duracion = get_post_meta(get_the_ID(), '_flavor_duracion', true);
                $post_type = get_post_type();
                $type_labels = array('destino' => 'Destino', 'paquete' => 'Tour', 'oferta' => 'Oferta', 'post' => 'Blog', 'page' => 'Página');
                $type_label = $type_labels[$post_type] ?? 'Contenido';
                
                $pais = get_the_terms(get_the_ID(), 'pais');
                $location = ($pais && !is_wp_error($pais)) ? $pais[0]->name : '';
            ?>
            <article class="search-card">
                <a href="<?php the_permalink(); ?>" class="img-wrap">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                    <span class="type-badge"><?php echo $type_label; ?></span>
                </a>
                <div style="padding: 24px;">
                    <?php if ($location): ?><div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">📍 <?php echo esc_html($location); ?></div><?php endif; ?>
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.25rem; margin: 0 0 8px;">
                        <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0f172a;"><?php the_title(); ?></a>
                    </h3>
                    <?php if ($duracion): ?><div style="color: #64748b; font-size: 0.85rem; margin-bottom: 16px;">⏱️ <?php echo esc_html($duracion); ?></div><?php endif; ?>
                    <?php if ($precio): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <span style="font-size: 1.25rem; font-weight: 700; color: #2563eb;">$<?php echo number_format($precio_oferta ?: $precio); ?></span>
                        <a href="<?php the_permalink(); ?>" style="color: #2563eb; text-decoration: none; font-weight: 500;">Ver más →</a>
                    </div>
                    <?php else: ?>
                    <a href="<?php the_permalink(); ?>" style="color: #2563eb; text-decoration: none; font-weight: 500;">Ver más →</a>
                    <?php endif; ?>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(array('mid_size' => 2, 'prev_text' => '← Anterior', 'next_text' => 'Siguiente →')); ?>
        
        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
            <h2 style="font-family: 'DM Serif Display', serif; color: #0a1628; margin-bottom: 12px;">No encontramos resultados</h2>
            <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 24px;">No hay destinos, tours u ofertas que coincidan con "<strong><?php echo esc_html($query); ?></strong>"</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo get_post_type_archive_link('destino'); ?>" style="display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">Ver destinos</a>
                <a href="<?php echo get_post_type_archive_link('oferta'); ?>" style="display: inline-block; background: white; color: #2563eb; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 2px solid #2563eb;">Ver ofertas</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
