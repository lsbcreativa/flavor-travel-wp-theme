<?php
// Solo obtener continentes padres (no países/subcategorías)
$continentes = get_terms(array(
    'taxonomy' => 'continente',
    'hide_empty' => false,
    'parent' => 0
));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { overflow-x: hidden; margin: 0; padding: 0; }
    img { max-width: 100%; height: auto; }
    
    .site-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 16px 0;
        background: transparent;
        transition: background 0.3s;
    }
    .site-header.scrolled {
        background: rgba(10, 22, 40, 0.95);
        backdrop-filter: blur(10px);
    }
    .site-header .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .site-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: white;
    }
    .site-logo__icon {
        width: 44px;
        height: 44px;
        background: #2563eb;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .site-logo__text {
        font-family: 'DM Serif Display', serif;
        font-size: 1.4rem;
    }
    
    /* NAV CON SUBMENÚS */
    .main-nav {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .nav-item {
        position: relative;
    }
    .nav-item > a {
        display: flex;
        align-items: center;
        gap: 4px;
        color: rgba(255,255,255,0.9);
        text-decoration: none;
        font-weight: 500;
        padding: 10px 16px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .nav-item > a:hover {
        color: #60a5fa;
        background: rgba(255,255,255,0.1);
    }
    .nav-item > a svg {
        width: 16px;
        height: 16px;
        transition: transform 0.3s;
    }
    .nav-item:hover > a svg {
        transform: rotate(180deg);
    }
    
    /* SUBMENÚ */
    .submenu {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 200px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s;
        z-index: 100;
    }
    .nav-item:hover .submenu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .submenu a {
        display: block;
        padding: 12px 16px;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    .submenu a:hover {
        background: #eff6ff;
        color: #2563eb;
    }
    
    /* HEADER ACTIONS */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .header-phone {
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
        text-decoration: none;
    }
    .header-whatsapp {
        width: 44px;
        height: 44px;
        background: #25D366;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    /* MOBILE TOGGLE */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 10px;
        z-index: 1002;
    }
    .mobile-menu-toggle span {
        display: block;
        width: 28px;
        height: 3px;
        background: white;
        margin: 6px 0;
        transition: all 0.3s;
        border-radius: 2px;
    }
    .mobile-menu-toggle.active span:nth-child(1) { transform: rotate(45deg) translate(6px, 6px); }
    .mobile-menu-toggle.active span:nth-child(2) { opacity: 0; }
    .mobile-menu-toggle.active span:nth-child(3) { transform: rotate(-45deg) translate(6px, -6px); }
    
    /* MOBILE MENU */
    .mobile-menu {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #0a1628;
        z-index: 1001;
        padding: 30px;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
    }
    .mobile-menu.active { transform: translateX(0); }
    
    /* BOTÓN CERRAR */
    .mobile-menu-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }
    .mobile-menu-close:hover {
        background: rgba(255,255,255,0.2);
    }
    .mobile-menu-close svg {
        width: 24px;
        height: 24px;
        color: white;
    }
    
    .mobile-menu nav {
        margin-top: 60px;
    }
    .mobile-menu nav > a,
    .mobile-menu nav > .mobile-nav-item > a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: white;
        text-decoration: none;
        font-size: 1.3rem;
        font-family: 'DM Serif Display', serif;
        padding: 16px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .mobile-menu nav > a:hover,
    .mobile-menu nav > .mobile-nav-item > a:hover { color: #60a5fa; }
    .mobile-nav-item svg {
        width: 20px;
        height: 20px;
        transition: transform 0.3s;
    }
    .mobile-nav-item.open svg {
        transform: rotate(180deg);
    }
    .mobile-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        padding-left: 20px;
        border-left: 2px solid #2563eb;
        margin-left: 10px;
    }
    .mobile-nav-item.open .mobile-submenu {
        max-height: 500px;
    }
    .mobile-submenu a {
        display: block;
        font-size: 1rem !important;
        font-family: 'Inter', sans-serif !important;
        padding: 12px 0 !important;
        color: rgba(255,255,255,0.8) !important;
        border-bottom: none !important;
    }
    .mobile-submenu a:hover {
        color: #60a5fa !important;
    }
    .mobile-menu-social {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .mobile-menu-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        color: white;
        transition: all 0.3s;
    }
    .mobile-menu-social a:hover {
        background: #2563eb;
    }
    .mobile-menu-contact {
        margin-top: auto;
        padding-top: 30px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .mobile-menu-contact a {
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
        text-decoration: none;
        padding: 12px 0;
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
    }
    
    /* WHATSAPP FLOAT */
    .whatsapp-float {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 60px;
        height: 60px;
        background: #25D366;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 998;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .whatsapp-float svg { width: 32px; height: 32px; }
    
    @media (max-width: 1024px) {
        .main-nav, .header-phone, .header-whatsapp { display: none !important; }
        .mobile-menu-toggle { display: block !important; }
        .whatsapp-float { display: flex !important; }
        .site-logo__text { font-size: 1.1rem; }
        .site-logo__icon { width: 38px; height: 38px; }
    }
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header">
    <div class="container">
        <a href="<?php echo home_url('/'); ?>" class="site-logo">
            <div class="site-logo__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            </div>
            <span class="site-logo__text"><?php echo get_bloginfo('name') ?: 'Tu Agencia'; ?></span>
        </a>
        
        <nav class="main-nav">
            <div class="nav-item">
                <a href="<?php echo home_url('/'); ?>">Inicio</a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo home_url('/destinos/'); ?>">
                    Destinos
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <?php if ($continentes && !is_wp_error($continentes) && count($continentes) > 0): ?>
                <div class="submenu">
                    <?php foreach ($continentes as $cont): ?>
                    <a href="<?php echo get_term_link($cont); ?>"><?php echo esc_html($cont->name); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo home_url('/viajes/'); ?>">
                    Viajes
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="submenu">
                    <a href="<?php echo get_post_type_archive_link('salida_confirmada'); ?>">Salidas Confirmadas</a>
                    <a href="<?php echo get_post_type_archive_link('evento_deportivo'); ?>">Eventos Deportivos</a>
                </div>
            </div>

            <div class="nav-item">
                <a href="<?php echo get_post_type_archive_link('oferta'); ?>">Ofertas</a>
            </div>

            <div class="nav-item">
                <a href="<?php echo home_url('/nosotros/'); ?>">Nosotros</a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo home_url('/contacto/'); ?>">Contacto</a>
            </div>
        </nav>
        
        <div class="header-actions">
            <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" class="header-whatsapp" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            </a>
        </div>
        
        <button class="mobile-menu-toggle" id="mobile-toggle" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu">
    <!-- BOTÓN CERRAR -->
    <button class="mobile-menu-close" id="mobile-close" aria-label="Cerrar menú">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>
    
    <nav>
        <a href="<?php echo home_url('/'); ?>">Inicio</a>
        
        <div class="mobile-nav-item" id="mobile-destinos">
            <a href="javascript:void(0);" onclick="toggleMobileSubmenu('mobile-destinos')">
                Destinos
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </a>
            <div class="mobile-submenu">
                <a href="<?php echo home_url('/destinos/'); ?>" style="font-weight: 600; color: #2563eb;">Ver todos los destinos</a>
                <?php if ($continentes && !is_wp_error($continentes) && count($continentes) > 0): ?>
                <?php foreach ($continentes as $cont): ?>
                <a href="<?php echo get_term_link($cont); ?>"><?php echo esc_html($cont->name); ?></a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mobile-nav-item" id="mobile-viajes">
            <a href="javascript:void(0);" onclick="toggleMobileSubmenu('mobile-viajes')">
                Viajes
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </a>
            <div class="mobile-submenu">
                <a href="<?php echo home_url('/viajes/'); ?>" style="font-weight: 600; color: #2563eb;">Ver todos los viajes</a>
                <a href="<?php echo get_post_type_archive_link('salida_confirmada'); ?>">Salidas Confirmadas</a>
                <a href="<?php echo get_post_type_archive_link('evento_deportivo'); ?>">Eventos Deportivos</a>
            </div>
        </div>

        <a href="<?php echo get_post_type_archive_link('oferta'); ?>">Ofertas</a>
        <a href="<?php echo home_url('/nosotros/'); ?>">Nosotros</a>
        <a href="<?php echo home_url('/contacto/'); ?>">Contacto</a>
    </nav>
    <div class="mobile-menu-contact">
        <a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" target="_blank" style="background: #25D366; color: white; padding: 14px 24px; border-radius: 8px; justify-content: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            WhatsApp
        </a>
    </div>
    
    <?php 
    $social_links = flavor_get_social_links();
    if (!empty($social_links)): 
    ?>
    <div class="mobile-menu-social">
        <?php foreach ($social_links as $key => $data): ?>
        <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo $data['name']; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo in_array($key, ['whatsapp_social', 'youtube', 'facebook']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $data['icon']; ?></svg>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- WHATSAPP FLOAT -->
<a href="https://wa.me/<?php echo get_theme_mod('flavor_whatsapp', '00123456789'); ?>" class="whatsapp-float" target="_blank">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<script>
(function() {
    var toggle = document.getElementById('mobile-toggle');
    var menu = document.getElementById('mobile-menu');
    var closeBtn = document.getElementById('mobile-close');
    
    function openMenu() {
        toggle.classList.add('active');
        menu.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMenu() {
        toggle.classList.remove('active');
        menu.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (toggle && menu) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (menu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }
    
    // Botón cerrar
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeMenu();
        });
    }
    
    // Cerrar al hacer clic en enlaces (excepto submenú toggle)
    var links = menu.querySelectorAll('nav > a');
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', closeMenu);
    }
    
    // Cerrar submenú links
    var subLinks = menu.querySelectorAll('.mobile-submenu a');
    for (var i = 0; i < subLinks.length; i++) {
        subLinks[i].addEventListener('click', closeMenu);
    }
    
    // Header scroll effect
    var header = document.getElementById('site-header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
})();

// Mobile submenu toggle
function toggleMobileSubmenu(id) {
    var item = document.getElementById(id);
    if (item) {
        item.classList.toggle('open');
    }
}
</script>

<main>
