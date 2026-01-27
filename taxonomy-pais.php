<?php
get_header();
$term = get_queried_object();
$term_image = get_term_meta($term->term_id, 'pais_image', true);
if (empty($term_image)) {
    $term_image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1920&q=80';
}

// Obtener el continente asociado
$continente = '';
$posts_in_pais = get_posts(array('post_type' => array('destino', 'paquete', 'oferta'), 'posts_per_page' => 1, 'tax_query' => array(array('taxonomy' => 'pais', 'field' => 'term_id', 'terms' => $term->term_id))));
if ($posts_in_pais) {
    $cont = get_the_terms($posts_in_pais[0]->ID, 'continente');
    if ($cont && !is_wp_error($cont)) {
        $continente = $cont[0]->name;
    }
}
?>

<style>
.pais-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.pais-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.pais-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.pais-card .img-wrap {
    position: relative;
    display: block;
    aspect-ratio: 16/10;
    overflow: hidden;
}
.pais-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}
.pais-card:hover img {
    transform: scale(1.08);
}
@media (max-width: 1024px) {
    .pais-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .pais-grid { grid-template-columns: 1fr; }
}
</style>

<!-- HERO -->
<section style="position: relative; min-height: 50vh; display: flex; align-items: center;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($term_image); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.4) 0%, rgba(10,22,40,0.85) 100%);"></div>
    </div>
    <div style="position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 120px 20px 60px; width: 100%; text-align: center;">
        <?php if ($continente): ?>
        <a href="<?php echo get_term_link(get_term_by('name', $continente, 'continente')); ?>" style="display: inline-block; background: rgba(255,255,255,0.15); color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 16px; text-decoration: none;">
            🌍 <?php echo esc_html($continente); ?>
        </a>
        <?php endif; ?>
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(2.5rem, 6vw, 4rem); color: white; margin: 0 0 16px;">
            <?php echo esc_html($term->name); ?>
        </h1>
        <?php if ($term->description): ?>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9); max-width: 600px; margin: 0 auto;">
            <?php echo esc_html($term->description); ?>
        </p>
        <?php endif; ?>
    </div>
</section>

<!-- CONTENIDO -->
<section style="padding: 80px 0; background: #f8fafc;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <?php if (have_posts()): ?>
        <div class="pais-grid">
            <?php while (have_posts()): the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $precio = get_post_meta(get_the_ID(), '_flavor_precio', true);
                $precio_oferta = get_post_meta(get_the_ID(), '_flavor_precio_oferta', true);
                $duracion = get_post_meta(get_the_ID(), '_flavor_duracion', true);
                $post_type = get_post_type();
                $type_label = ($post_type == 'destino') ? 'Destino' : (($post_type == 'paquete') ? 'Tour' : 'Oferta');
            ?>
            <article class="pais-card">
                <a href="<?php the_permalink(); ?>" class="img-wrap">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                    <span style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem;"><?php echo $type_label; ?></span>
                </a>
                <div style="padding: 24px;">
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.25rem; margin: 0 0 8px;">
                        <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0f172a;"><?php the_title(); ?></a>
                    </h3>
                    <?php if ($duracion): ?><div style="color: #64748b; font-size: 0.85rem; margin-bottom: 16px;">⏱️ <?php echo esc_html($duracion); ?></div><?php endif; ?>
                    <?php if ($precio): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <span style="font-size: 1.25rem; font-weight: 700; color: #2563eb;">$<?php echo number_format($precio_oferta ?: $precio); ?></span>
                        <a href="<?php the_permalink(); ?>" style="color: #2563eb; text-decoration: none; font-weight: 500;">Ver más →</a>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(array('mid_size' => 2, 'prev_text' => '← Anterior', 'next_text' => 'Siguiente →')); ?>
        
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <p style="color: #64748b;">No hay contenido disponible para este país.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
