<?php
get_header();

$continentes = array(
    'america' => array('América', 'Desde la Patagonia hasta Alaska, descubre paisajes únicos y culturas ancestrales.', 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1920&q=80'),
    'europa' => array('Europa', 'Arte, historia milenaria, gastronomía exquisita y ciudades de ensueño.', 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=1920&q=80'),
    'asia' => array('Asia', 'Templos milenarios, tecnología futurista y sabores únicos.', 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=1920&q=80'),
    'africa' => array('África', 'Safaris épicos y la vida salvaje más espectacular del planeta.', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1920&q=80'),
    'oceania' => array('Oceanía', 'La Gran Barrera de Coral, Nueva Zelanda y playas de ensueño.', 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=1920&q=80'),
);

$title = 'Archivo'; $desc = ''; $image = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80';

if (is_tax('continente')) {
    $obj = get_queried_object();
    $slug = strtolower(str_replace(array('á','é','í','ó','ú','Á','É','Í','Ó','Ú'), array('a','e','i','o','u','a','e','i','o','u'), $obj->slug));
    if (isset($continentes[$slug])) { $title = $continentes[$slug][0]; $desc = $continentes[$slug][1]; $image = $continentes[$slug][2]; }
    else { $title = $obj->name; $desc = $obj->description; }
} elseif (is_post_type_archive('destino')) { 
    $title = 'Destinos'; $desc = 'Explora destinos increíbles en los 5 continentes.'; 
    $image = 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1920&q=80'; 
} elseif (is_post_type_archive('paquete')) { 
    $title = 'Tours y Paquetes'; $desc = 'Experiencias diseñadas para crear los mejores recuerdos.'; 
    $image = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920&q=80'; 
} elseif (is_post_type_archive('oferta')) { 
    $title = 'Ofertas Especiales'; $desc = 'Aprovecha descuentos exclusivos en destinos increíbles.'; 
    $image = 'https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=1920&q=80'; 
}

global $wp_query; $total = $wp_query->found_posts;
?>

<!-- HERO SIN FONDO NEGRO -->
<section style="position: relative; min-height: 50vh; display: flex; align-items: center;">
    <div style="position: absolute; inset: 0;">
        <img src="<?php echo $image; ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,22,40,0.4) 0%, rgba(10,22,40,0.7) 100%);"></div>
    </div>
    <div class="container" style="position: relative; z-index: 10; padding: 120px 0 60px;">
        <nav style="margin-bottom: 20px; font-size: 0.9rem;">
            <a href="<?php echo home_url('/'); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none;"><i data-lucide="home" style="width: 14px; height: 14px;"></i> Inicio</a>
            <span style="color: rgba(255,255,255,0.5); margin: 0 10px;">›</span>
            <span style="color: #fff;"><?php echo esc_html($title); ?></span>
        </nav>
        
        <span style="display: inline-flex; align-items: center; gap: 8px; background: var(--accent); color: white; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; margin-bottom: 20px;">
            <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
            <?php echo $total; ?> <?php echo ($total == 1) ? 'disponible' : 'disponibles'; ?>
        </span>
        
        <h1 style="font-family: var(--font-display); font-size: clamp(2.5rem, 8vw, 4rem); color: #fff; margin: 0 0 16px; text-shadow: 0 2px 20px rgba(0,0,0,0.5);">
            <?php echo esc_html($title); ?>
        </h1>
        
        <?php if ($desc): ?>
        <p style="font-size: 1.15rem; color: rgba(255,255,255,0.95); max-width: 600px; line-height: 1.7; text-shadow: 0 1px 10px rgba(0,0,0,0.3);">
            <?php echo esc_html($desc); ?>
        </p>
        <?php endif; ?>
    </div>
</section>

<!-- CONTENIDO -->
<section class="section" style="background: var(--gray-50); padding: 50px 0 80px;">
    <div class="container">
        <?php if (have_posts()): ?>
        <div class="grid grid--3" style="gap: 30px;">
            <?php while (have_posts()): the_post();
                $precio = get_post_meta(get_the_ID(), '_flavor_precio', true);
                $precio_oferta = get_post_meta(get_the_ID(), '_flavor_precio_oferta', true);
                $duracion = get_post_meta(get_the_ID(), '_flavor_duracion', true);
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80';
                $cont = get_the_terms(get_the_ID(), 'continente');
                $loc = ($cont && !is_wp_error($cont)) ? $cont[0]->name : '';
                $disc = ($precio && $precio_oferta && $precio > $precio_oferta) ? round((($precio - $precio_oferta) / $precio) * 100) : 0;
            ?>
            <article style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 12px 30px rgba(0,0,0,0.12)';" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)';">
                <a href="<?php the_permalink(); ?>" style="display: block; position: relative; aspect-ratio: 4/3; overflow: hidden;">
                    <img src="<?php echo $img; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='';">
                    <?php if ($disc > 0): ?><span style="position: absolute; top: 12px; right: 12px; background: rgba(239,68,68,0.9); color: white; padding: 6px 10px; border-radius: 8px; font-weight: 600; font-size: 0.8rem;">-<?php echo $disc; ?>%</span><?php endif; ?>
                </a>
                <div style="padding: 20px;">
                    <?php if ($loc): ?><div style="display: flex; align-items: center; gap: 6px; color: var(--gray-500); font-size: 0.85rem; margin-bottom: 8px;"><i data-lucide="map-pin" style="width: 14px; height: 14px;"></i><?php echo $loc; ?></div><?php endif; ?>
                    <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin: 0 0 12px;"><a href="<?php the_permalink(); ?>" style="text-decoration: none; color: var(--primary);"><?php the_title(); ?></a></h3>
                    <?php if ($duracion): ?><div style="display: flex; align-items: center; gap: 6px; color: var(--gray-500); font-size: 0.85rem; margin-bottom: 16px;"><i data-lucide="calendar" style="width: 14px; height: 14px;"></i><?php echo $duracion; ?></div><?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid var(--gray-100);">
                        <div>
                            <?php if ($precio || $precio_oferta): ?>
                                <?php if ($disc > 0): ?><span style="text-decoration: line-through; color: var(--gray-400); font-size: 0.85rem;">$<?php echo number_format($precio); ?></span><?php endif; ?>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent);">$<?php echo number_format($precio_oferta ?: $precio); ?></div>
                            <?php else: ?><span style="color: var(--gray-500);">Consultar</span><?php endif; ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" style="background: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: background 0.3s;">Ver más</a>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <?php if ($wp_query->max_num_pages > 1): ?><div style="text-align: center; margin-top: 50px;"><?php echo paginate_links(); ?></div><?php endif; ?>
        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px;">
            <div style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i data-lucide="map-pin" style="width: 32px; height: 32px; color: var(--gray-400);"></i>
            </div>
            <h2 style="font-family: var(--font-display); margin-bottom: 12px;">Próximamente</h2>
            <p style="color: var(--gray-500); margin-bottom: 24px;">Estamos preparando contenido increíble para ti.</p>
            <a href="<?php echo home_url('/'); ?>" class="btn btn--primary">Volver al inicio</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section style="background: var(--primary); padding: 60px 0;">
    <div class="container" style="text-align: center;">
        <h2 style="font-family: var(--font-display); color: white; font-size: 1.75rem; margin-bottom: 12px;">¿Listo para tu próxima aventura?</h2>
        <p style="color: rgba(255,255,255,0.8); margin-bottom: 24px;">Contáctanos y diseñaremos el viaje perfecto para ti</p>
        <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" class="btn" style="background: white; color: var(--primary); padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;" target="_blank">
            <i data-lucide="message-circle" style="width: 18px; height: 18px;"></i>
            Consultar ahora
        </a>
    </div>
</section>

<?php get_footer(); ?>
