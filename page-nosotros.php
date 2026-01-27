<?php
/*
Template Name: Nosotros
*/
get_header();

$title = get_theme_mod('flavor_nosotros_title', 'Nosotros');
$desc = get_theme_mod('flavor_nosotros_desc', 'Conoce nuestra historia y pasión por los viajes.');
$hero_image = get_theme_mod('flavor_nosotros_image', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&q=80');
$content = get_theme_mod('flavor_nosotros_content', 'Somos una agencia de viajes con más de 10 años de experiencia.');
$content_image = get_theme_mod('flavor_nosotros_content_image', '');
$layout = get_theme_mod('flavor_nosotros_layout', 'image-right');

$block2_title = get_theme_mod('flavor_nosotros_block2_title', '');
$block2_text = get_theme_mod('flavor_nosotros_block2_text', '');
$block2_image = get_theme_mod('flavor_nosotros_block2_image', '');
$block2_layout = get_theme_mod('flavor_nosotros_block2_layout', 'image-left');

$content_image_url = $content_image ? wp_get_attachment_image_url($content_image, 'large') : '';
$block2_image_url = $block2_image ? wp_get_attachment_image_url($block2_image, 'large') : '';
?>

<style>
.content-block {
    padding: 80px 0;
}
.content-block:nth-child(even) {
    background: #f8fafc;
}
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}
.content-grid.reverse {
    direction: rtl;
}
.content-grid.reverse > * {
    direction: ltr;
}
.content-grid img {
    width: 100%;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}
.content-text h2 {
    font-family: var(--font-display);
    font-size: 2rem;
    color: #0a1628;
    margin-bottom: 20px;
}
.content-text p, .content-text div {
    color: #4b5563;
    line-height: 1.9;
    font-size: 1.05rem;
}
.content-text a {
    color: #2563eb;
    text-decoration: underline;
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
@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .content-grid.reverse {
        direction: ltr;
    }
}
</style>

<!-- HERO FULLSCREEN -->
<section style="position: relative; min-height: 100vh; min-height: 100dvh; display: flex; align-items: center; overflow: hidden;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo esc_url($hero_image); ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.3) 0%, rgba(10,22,40,0.6) 100%);"></div>
    </div>
    <div style="position: relative; z-index: 10; max-width: 800px; margin: 0 auto; padding: 120px 20px 100px; text-align: center; width: 100%;">
        <h1 style="font-family: var(--font-display); font-size: clamp(2.5rem, 8vw, 4.5rem); color: white; margin: 0 0 20px;">
            <?php echo esc_html($title); ?>
        </h1>
        <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9); line-height: 1.7; max-width: 600px; margin: 0 auto;">
            <?php echo esc_html($desc); ?>
        </p>
    </div>
    
    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <span style="display: block; margin-bottom: 8px;">Ver más</span>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
    </div>
</section>

<!-- BLOQUE 1 - CONTENIDO PRINCIPAL -->
<?php if ($content): ?>
<section class="content-block" style="background: white;">
    <?php if ($layout === 'text-only' || empty($content_image_url)): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <div class="content-text">
                <div><?php echo wp_kses_post($content); ?></div>
            </div>
        </div>
    <?php elseif ($layout === 'image-left'): ?>
        <div class="content-grid">
            <div>
                <img src="<?php echo esc_url($content_image_url); ?>" alt="">
            </div>
            <div class="content-text">
                <div><?php echo wp_kses_post($content); ?></div>
            </div>
        </div>
    <?php elseif ($layout === 'image-right'): ?>
        <div class="content-grid reverse">
            <div>
                <img src="<?php echo esc_url($content_image_url); ?>" alt="">
            </div>
            <div class="content-text">
                <div><?php echo wp_kses_post($content); ?></div>
            </div>
        </div>
    <?php elseif ($layout === 'image-top'): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <img src="<?php echo esc_url($content_image_url); ?>" alt="" style="width: 100%; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); margin-bottom: 40px;">
            <div class="content-text">
                <div><?php echo wp_kses_post($content); ?></div>
            </div>
        </div>
    <?php elseif ($layout === 'image-bottom'): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <div class="content-text" style="margin-bottom: 40px;">
                <div><?php echo wp_kses_post($content); ?></div>
            </div>
            <img src="<?php echo esc_url($content_image_url); ?>" alt="" style="width: 100%; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.1);">
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- BLOQUE 2 - ADICIONAL -->
<?php if ($block2_title || $block2_text): ?>
<section class="content-block" style="background: #f8fafc;">
    <?php if ($block2_layout === 'text-only' || empty($block2_image_url)): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <?php if ($block2_title): ?><h2 style="font-family: var(--font-display); font-size: 2rem; color: #0a1628; margin-bottom: 20px;"><?php echo esc_html($block2_title); ?></h2><?php endif; ?>
            <div class="content-text">
                <div><?php echo wp_kses_post($block2_text); ?></div>
            </div>
        </div>
    <?php elseif ($block2_layout === 'image-left'): ?>
        <div class="content-grid">
            <div>
                <img src="<?php echo esc_url($block2_image_url); ?>" alt="">
            </div>
            <div class="content-text">
                <?php if ($block2_title): ?><h2><?php echo esc_html($block2_title); ?></h2><?php endif; ?>
                <div><?php echo wp_kses_post($block2_text); ?></div>
            </div>
        </div>
    <?php elseif ($block2_layout === 'image-right'): ?>
        <div class="content-grid reverse">
            <div>
                <img src="<?php echo esc_url($block2_image_url); ?>" alt="">
            </div>
            <div class="content-text">
                <?php if ($block2_title): ?><h2><?php echo esc_html($block2_title); ?></h2><?php endif; ?>
                <div><?php echo wp_kses_post($block2_text); ?></div>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- CTA -->
<section style="padding: 80px 0; background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 100%); text-align: center;">
    <div style="max-width: 600px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-family: var(--font-display); font-size: 2rem; color: white; margin-bottom: 16px;">¿Listo para tu próxima aventura?</h2>
        <p style="color: rgba(255,255,255,0.8); margin-bottom: 30px;">Contáctanos y planifiquemos juntos el viaje de tus sueños.</p>
        <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 10px; background: #25D366; color: white; padding: 16px 32px; border-radius: 50px; font-weight: 600; text-decoration: none; box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            Escríbenos por WhatsApp
        </a>
    </div>
</section>

<?php get_footer(); ?>
