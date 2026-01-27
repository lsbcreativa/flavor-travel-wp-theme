<?php get_header(); ?>

<section style="min-height: 80vh; display: flex; align-items: center; background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 100%); position: relative; overflow: hidden;">
    
    <!-- Decoración de fondo -->
    <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(37, 99, 235, 0.1); border-radius: 50%; filter: blur(60px);"></div>
    <div style="position: absolute; bottom: -150px; left: -150px; width: 500px; height: 500px; background: rgba(37, 99, 235, 0.08); border-radius: 50%; filter: blur(80px);"></div>
    
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 60px 20px; text-align: center; position: relative; z-index: 10;">
        
        <!-- Icono animado -->
        <div style="margin-bottom: 30px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 120px; height: 120px; background: rgba(37, 99, 235, 0.2); border-radius: 50%; margin-bottom: 20px;">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01"/>
                </svg>
            </div>
        </div>
        
        <!-- Número 404 -->
        <div style="font-size: clamp(6rem, 20vw, 10rem); font-weight: 800; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; margin-bottom: 20px; letter-spacing: -5px;">
            404
        </div>
        
        <!-- Título -->
        <h1 style="font-family: var(--font-display); font-size: clamp(1.5rem, 4vw, 2.5rem); color: white; margin: 0 0 16px;">
            ¡Ups! Destino no encontrado
        </h1>
        
        <!-- Descripción -->
        <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem; max-width: 500px; margin: 0 auto 40px; line-height: 1.7;">
            Parece que esta ruta no existe en nuestro mapa. No te preocupes, te ayudamos a encontrar tu próximo destino.
        </p>
        
        <!-- Botones -->
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-bottom: 50px;">
            <a href="<?php echo home_url('/'); ?>" style="display: inline-flex; align-items: center; gap: 10px; background: #2563eb; color: white; padding: 16px 32px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Volver al inicio
            </a>
            <a href="<?php echo get_post_type_archive_link('destino'); ?>" style="display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.1); color: white; padding: 16px 32px; border-radius: 12px; font-weight: 600; text-decoration: none; border: 2px solid rgba(255,255,255,0.2); transition: all 0.3s ease;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/></svg>
                Explorar destinos
            </a>
        </div>
        
        <!-- Sugerencias -->
        <div style="background: rgba(255,255,255,0.05); border-radius: 16px; padding: 30px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
            <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px;">También puedes visitar</p>
            <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
                <a href="<?php echo get_post_type_archive_link('paquete'); ?>" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px; opacity: 0.8; transition: opacity 0.3s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/></svg>
                    Tours
                </a>
                <a href="<?php echo get_post_type_archive_link('oferta'); ?>" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px; opacity: 0.8; transition: opacity 0.3s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    Ofertas
                </a>
                <a href="<?php echo home_url('/contacto/'); ?>" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px; opacity: 0.8; transition: opacity 0.3s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Contacto
                </a>
            </div>
        </div>
        
    </div>
</section>

<style>
/* Hover effects for 404 */
.container a:hover {
    transform: translateY(-2px);
}
.container a[href*="destino"]:hover,
.container a[href*="inicio"]:hover {
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.5);
}
</style>

<?php get_footer(); ?>
