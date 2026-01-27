<?php
get_header();
$term = get_queried_object();
$term_image = get_term_meta($term->term_id, 'continente_imagen', true);
if (empty($term_image)) {
    $term_image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1920&q=80';
}
global $wp_query;
$total = $wp_query->found_posts;
?>

<style>
.destinos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.destino-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.destino-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.destino-card .img-wrap {
    display: block;
    overflow: hidden;
    aspect-ratio: 16/10;
}
.destino-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.destino-card:hover img {
    transform: scale(1.05);
}
@media (max-width: 1024px) { .destinos-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .destinos-grid { grid-template-columns: 1fr; } }
</style>

<!-- HERO FULLSCREEN -->
<section style="position: relative; height: 100vh; display: flex; align-items: center; overflow: hidden;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($term_image); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.3) 0%, rgba(10,22,40,0.6) 100%);"></div>
    </div>
    <div style="position: relative; z-index: 10; max-width: 1280px; margin: 0 auto; padding: 0 20px; width: 100%;">
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            <a href="<?php echo home_url('/'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">🏠 Inicio</a>
            <span style="color: rgba(255,255,255,0.5);">›</span>
            <a href="<?php echo get_post_type_archive_link('destino'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;">Destinos</a>
            <span style="color: rgba(255,255,255,0.5);">›</span>
            <span style="color: #fff;"><?php echo esc_html($term->name); ?></span>
        </nav>
        <span style="display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 20px;">
            🗺️ <?php echo $total; ?> destinos disponibles
        </span>
        <h1 style="font-family: 'DM Serif Display', serif; font-size: clamp(3rem, 10vw, 5rem); color: #fff; margin: 0 0 20px;">
            <?php echo esc_html($term->name); ?>
        </h1>
        <?php if ($term->description): ?>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.95); max-width: 600px; line-height: 1.7;">
            <?php echo esc_html($term->description); ?>
        </p>
        <?php endif; ?>
    </div>
</section>

<!-- DESTINOS -->
<section style="background: #f8fafc; padding: 80px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <?php if (have_posts()): ?>
        <div class="destinos-grid">
            <?php while (have_posts()): the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                if (!$img) $img = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
            ?>
            <article class="destino-card">
                <a href="<?php the_permalink(); ?>" class="img-wrap">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
                </a>
                <div style="padding: 24px;">
                    <h3 style="font-family: 'DM Serif Display', serif; font-size: 1.4rem; margin: 0 0 12px;">
                        <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #0f172a;"><?php the_title(); ?></a>
                    </h3>
                    <?php if (has_excerpt()): ?>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                    </p>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" style="display: inline-flex; align-items: center; gap: 8px; color: #2563eb; font-weight: 600; text-decoration: none;">
                        Explorar destino
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
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
            <p style="color: #64748b; font-size: 1.1rem;">Aún no hay destinos en <?php echo esc_html($term->name); ?>.</p>
            <a href="<?php echo get_post_type_archive_link('destino'); ?>" style="display: inline-block; margin-top: 20px; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">Ver todos los destinos</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
