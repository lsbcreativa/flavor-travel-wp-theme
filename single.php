<?php
get_header();
while (have_posts()): the_post();
    $precio = get_post_meta(get_the_ID(), '_flavor_precio', true);
    $precio_oferta = get_post_meta(get_the_ID(), '_flavor_precio_oferta', true);
    $duracion = get_post_meta(get_the_ID(), '_flavor_duracion', true);
    $img = get_the_post_thumbnail_url(get_the_ID(), 'full') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80';
    $continente = get_the_terms(get_the_ID(), 'continente');
    $pais = get_the_terms(get_the_ID(), 'pais');
    $location = '';
    $location_link = '';
    
    // Mostrar país si existe, sino continente
    if ($pais && !is_wp_error($pais)) {
        $location = $pais[0]->name;
        $location_link = get_term_link($pais[0]);
    } elseif ($continente && !is_wp_error($continente)) {
        $location = $continente[0]->name;
        $location_link = get_term_link($continente[0]);
    }
    $post_type = get_post_type();
    $discount = ($precio && $precio_oferta && $precio > $precio_oferta) ? round((($precio - $precio_oferta) / $precio) * 100) : 0;
?>

<style>
/* ===== SINGLE POST PREMIUM STYLES ===== */
.single-hero {
    position: relative;
    min-height: 70vh;
    display: flex;
    align-items: flex-end;
    padding-bottom: 60px;
}
.single-hero-bg {
    position: absolute;
    inset: 0;
}
.single-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.single-hero-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(10,22,40,0.3) 0%, rgba(10,22,40,0.9) 100%);
}
.single-hero-content {
    position: relative;
    z-index: 10;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
    width: 100%;
}
.single-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    color: white;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.85rem;
    margin-bottom: 20px;
}
.single-title {
    font-family: var(--font-display);
    font-size: clamp(2.5rem, 6vw, 4rem);
    color: white;
    margin: 0 0 24px;
    line-height: 1.1;
    max-width: 800px;
}
.single-meta {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    align-items: center;
}
.single-meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
}
.single-meta-icon {
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.single-meta-icon svg {
    width: 22px;
    height: 22px;
    stroke: white;
}
.single-meta-text span {
    display: block;
    font-size: 0.8rem;
    opacity: 0.7;
}
.single-meta-text strong {
    font-size: 1.25rem;
}

/* Content Area */
.single-content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 60px 20px 80px;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 60px;
}
@media (max-width: 1024px) {
    .single-content-wrapper {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

/* Main Content */
.single-main {
    min-width: 0;
}
.single-content {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #374151;
}
.single-content h2 {
    font-family: var(--font-display);
    font-size: 1.75rem;
    color: #0a1628;
    margin: 50px 0 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
}
.single-content h3 {
    font-family: var(--font-display);
    font-size: 1.35rem;
    color: #0a1628;
    margin: 40px 0 16px;
}
.single-content h4 {
    font-size: 1.15rem;
    color: #1f2937;
    margin: 30px 0 12px;
}
.single-content p {
    margin-bottom: 24px;
}
.single-content ul, .single-content ol {
    margin: 24px 0;
    padding-left: 0;
    list-style: none;
}
.single-content ul li, .single-content ol li {
    position: relative;
    padding-left: 32px;
    margin-bottom: 12px;
}
.single-content ul li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 10px;
    width: 8px;
    height: 8px;
    background: #2563eb;
    border-radius: 50%;
}
.single-content ol {
    counter-reset: item;
}
.single-content ol li::before {
    content: counter(item);
    counter-increment: item;
    position: absolute;
    left: 0;
    top: 2px;
    width: 24px;
    height: 24px;
    background: #2563eb;
    color: white;
    border-radius: 50%;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}
.single-content img {
    max-width: 100%;
    height: auto;
    border-radius: 16px;
    margin: 30px 0;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}
.single-content blockquote {
    background: #f0f9ff;
    border-left: 4px solid #2563eb;
    padding: 24px 30px;
    margin: 30px 0;
    border-radius: 0 12px 12px 0;
    font-style: italic;
    color: #1e40af;
}
.single-content a {
    color: #2563eb;
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 3px;
}
.single-content a:hover {
    color: #1d4ed8;
}
.single-content strong {
    color: #0a1628;
}

/* Sidebar */
.single-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}
.sidebar-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 30px rgba(0,0,0,0.08);
    border: 1px solid #f1f5f9;
    margin-bottom: 24px;
}
.sidebar-price {
    text-align: center;
    padding-bottom: 24px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 24px;
}
.sidebar-price-label {
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 8px;
}
.sidebar-price-old {
    text-decoration: line-through;
    color: #94a3b8;
    font-size: 1.1rem;
}
.sidebar-price-current {
    font-family: var(--font-display);
    font-size: 2.5rem;
    color: #0a1628;
}
.sidebar-price-current span {
    font-size: 1rem;
    color: #64748b;
    font-family: var(--font-body);
}
.sidebar-discount {
    display: inline-block;
    background: #fef2f2;
    color: #dc2626;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 10px;
}
.sidebar-features {
    margin-bottom: 24px;
}
.sidebar-feature {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.sidebar-feature:last-child {
    border-bottom: none;
}
.sidebar-feature svg {
    width: 20px;
    height: 20px;
    stroke: #2563eb;
    flex-shrink: 0;
}
.sidebar-feature span {
    color: #374151;
}
.sidebar-cta {
    display: block;
    width: 100%;
    background: #25D366;
    color: white;
    padding: 18px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
}
.sidebar-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
}
.sidebar-cta svg {
    display: inline;
    width: 22px;
    height: 22px;
    vertical-align: middle;
    margin-right: 8px;
}
.sidebar-help {
    text-align: center;
    margin-top: 16px;
    font-size: 0.85rem;
    color: #64748b;
}
.sidebar-help a {
    color: #2563eb;
    text-decoration: none;
}

/* Related */
.single-related {
    background: #f8fafc;
    padding: 80px 0;
}
.single-related-title {
    font-family: var(--font-display);
    font-size: 2rem;
    text-align: center;
    margin-bottom: 40px;
    color: #0a1628;
}
.single-related-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
}
@media (max-width: 1024px) {
    .single-related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    .single-related-grid {
        grid-template-columns: 1fr;
    }
}
.related-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}
.related-card-img {
    aspect-ratio: 16/10;
    overflow: hidden;
}
.related-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.related-card:hover .related-card-img img {
    transform: scale(1.05);
}
.related-card-body {
    padding: 20px;
}
.related-card-title {
    font-family: var(--font-display);
    font-size: 1.2rem;
    margin: 0 0 8px;
    color: #0a1628;
}
.related-card-title a {
    text-decoration: none;
    color: inherit;
}
.related-card-price {
    color: #2563eb;
    font-weight: 700;
    font-size: 1.1rem;
}
</style>

<!-- HERO -->
<section class="single-hero">
    <div class="single-hero-bg">
        <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>">
    </div>
    <div class="single-hero-content">
        <?php if ($location): ?>
        <a href="<?php echo esc_url($location_link); ?>" class="single-badge" style="text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/></svg>
            <?php echo esc_html($location); ?>
        </a>
        <?php endif; ?>
        
        <h1 class="single-title"><?php the_title(); ?></h1>
        
        <div class="single-meta">
            <?php if ($duracion): ?>
            <div class="single-meta-item">
                <div class="single-meta-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="single-meta-text">
                    <span>Duración</span>
                    <strong><?php echo esc_html($duracion); ?></strong>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($precio): ?>
            <div class="single-meta-item">
                <div class="single-meta-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="single-meta-text">
                    <span>Desde</span>
                    <strong>$<?php echo number_format($precio_oferta ?: $precio); ?></strong>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($post_type): ?>
            <div class="single-meta-item">
                <div class="single-meta-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div class="single-meta-text">
                    <span>Tipo</span>
                    <strong><?php echo ($post_type == 'destino') ? 'Destino' : (($post_type == 'paquete') ? 'Tour' : 'Oferta'); ?></strong>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CONTENT -->
<div class="single-content-wrapper">
    <main class="single-main">
        <article class="single-content">
            <?php the_content(); ?>
        </article>
    </main>
    
    <aside class="single-sidebar">
        <div class="sidebar-card">
            <?php if ($precio): ?>
            <div class="sidebar-price">
                <div class="sidebar-price-label">Precio por persona</div>
                <?php if ($precio_oferta && $precio > $precio_oferta): ?>
                <div class="sidebar-price-old">$<?php echo number_format($precio); ?></div>
                <?php endif; ?>
                <div class="sidebar-price-current">
                    $<?php echo number_format($precio_oferta ?: $precio); ?>
                    <span>USD</span>
                </div>
                <?php if ($discount > 0): ?>
                <div class="sidebar-discount"><?php echo $discount; ?>% de descuento</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="sidebar-features">
                <?php if ($duracion): ?>
                <div class="sidebar-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><?php echo esc_html($duracion); ?></span>
                </div>
                <?php endif; ?>
                <div class="sidebar-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Atención personalizada</span>
                </div>
                <div class="sidebar-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Reserva segura</span>
                </div>
                <div class="sidebar-feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>Soporte 24/7</span>
                </div>
            </div>
            
            <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>?text=<?php echo urlencode('Hola, me interesa: ' . get_the_title() . ' - ' . get_permalink()); ?>" class="sidebar-cta" target="_blank">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Consultar por WhatsApp
            </a>
            <p class="sidebar-help">¿Prefieres llamar? <a href="tel:<?php echo get_theme_mod('flavor_phone', '+00123456789'); ?>"><?php echo get_theme_mod('flavor_phone', '+00 123 456 789'); ?></a></p>
        </div>
    </aside>
</div>

<!-- RELATED -->
<?php
// Obtener país y continente del post actual
$current_pais = get_the_terms(get_the_ID(), 'pais');
$current_continente = get_the_terms(get_the_ID(), 'continente');
$related = array();
$exclude_ids = array(get_the_ID());

// 1. PRIMERO: Buscar del mismo PAÍS
if ($current_pais && !is_wp_error($current_pais)) {
    $related = get_posts(array(
        'post_type' => $post_type,
        'posts_per_page' => 3,
        'post__not_in' => $exclude_ids,
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'pais',
                'field' => 'term_id',
                'terms' => $current_pais[0]->term_id,
            ),
        ),
    ));
    
    foreach ($related as $r) {
        $exclude_ids[] = $r->ID;
    }
}

// 2. SEGUNDO: Si faltan, buscar del mismo CONTINENTE
if (count($related) < 3 && $current_continente && !is_wp_error($current_continente)) {
    $more_continent = get_posts(array(
        'post_type' => $post_type,
        'posts_per_page' => 3 - count($related),
        'post__not_in' => $exclude_ids,
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'continente',
                'field' => 'term_id',
                'terms' => $current_continente[0]->term_id,
            ),
        ),
    ));
    
    $related = array_merge($related, $more_continent);
    
    foreach ($more_continent as $r) {
        $exclude_ids[] = $r->ID;
    }
}

// 3. TERCERO: Si todavía faltan, completar con cualquier otro
if (count($related) < 3) {
    $more_any = get_posts(array(
        'post_type' => $post_type,
        'posts_per_page' => 3 - count($related),
        'post__not_in' => $exclude_ids,
        'orderby' => 'rand',
    ));
    
    $related = array_merge($related, $more_any);
}

if ($related): ?>
<section class="single-related">
    <h2 class="single-related-title">También te puede interesar</h2>
    <div class="single-related-grid">
        <?php foreach ($related as $rel): 
            $rel_img = get_the_post_thumbnail_url($rel->ID, 'medium_large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
            $rel_precio = get_post_meta($rel->ID, '_flavor_precio', true);
            $rel_precio_oferta = get_post_meta($rel->ID, '_flavor_precio_oferta', true);
        ?>
        <article class="related-card">
            <a href="<?php echo get_permalink($rel->ID); ?>" class="related-card-img">
                <img src="<?php echo esc_url($rel_img); ?>" alt="<?php echo esc_attr($rel->post_title); ?>">
            </a>
            <div class="related-card-body">
                <h3 class="related-card-title"><a href="<?php echo get_permalink($rel->ID); ?>"><?php echo esc_html($rel->post_title); ?></a></h3>
                <?php if ($rel_precio): ?>
                <div class="related-card-price">Desde $<?php echo number_format($rel_precio_oferta ?: $rel_precio); ?></div>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php endwhile; get_footer(); ?>
