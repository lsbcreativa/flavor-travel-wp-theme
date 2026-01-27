<?php
/**
 * Template para páginas generales
 */
get_header();

while (have_posts()): the_post();
    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80';
?>

<!-- HERO -->
<section style="position: relative; height: 100vh; display: flex; align-items: center;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($featured_image); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.4) 0%, rgba(10,22,40,0.7) 100%);"></div>
    </div>
    <div class="container" style="position: relative; z-index: 10; padding: 120px 0 60px; text-align: center;">
        <h1 style="font-family: var(--font-display); font-size: clamp(2.5rem, 8vw, 4rem); color: #fff; margin: 0; text-shadow: 0 2px 20px rgba(0,0,0,0.5);">
            <?php the_title(); ?>
        </h1>
    </div>
</section>

<!-- CONTENIDO -->
<section class="section" style="padding: 80px 0;">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <?php if (get_the_content()): ?>
            <div style="font-size: 1.1rem; line-height: 1.8; color: var(--gray-700);">
                <?php the_content(); ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; background: var(--gray-50); border-radius: 16px;">
                <p style="color: var(--gray-500);">Esta página aún no tiene contenido. Edítala desde el panel de WordPress.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
