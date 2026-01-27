<?php
/*
Template Name: Contacto
*/
get_header();

$title = get_theme_mod('flavor_contacto_title', 'Contacto');
$desc = get_theme_mod('flavor_contacto_desc', 'Estamos aquí para ayudarte a planificar tu próximo viaje.');
$hero_image = get_theme_mod('flavor_contacto_image', 'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1920&q=80');
$address = get_theme_mod('flavor_contacto_address', 'Av. Principal 123, Lima, Perú');
$hours = get_theme_mod('flavor_contacto_hours', 'Lunes a Viernes: 9am - 6pm');

// Teléfono: primero busca en Página Contacto, luego en Contacto global
$phone = get_theme_mod('flavor_contacto_phone', '');
if (empty($phone)) {
    $phone = get_theme_mod('flavor_phone', '+00 123 456 789');
}

// Email: primero busca en Página Contacto, luego en Contacto global
$email = get_theme_mod('flavor_contacto_email', '');
if (empty($email)) {
    $email = get_theme_mod('flavor_email', 'info@tuagencia.com');
}

// WhatsApp: primero busca en Página Contacto, luego en Contacto global
$whatsapp = get_theme_mod('flavor_contacto_whatsapp', '');
if (empty($whatsapp)) {
    $whatsapp = get_theme_mod('flavor_whatsapp', '00123456789');
}

$content = get_theme_mod('flavor_contacto_content', '');
$content_image = get_theme_mod('flavor_contacto_content_image', '');
$layout = get_theme_mod('flavor_contacto_layout', 'image-right');
$map_embed = get_theme_mod('flavor_contacto_map', '');

$content_image_url = $content_image ? wp_get_attachment_image_url($content_image, 'large') : '';

// Ofuscar email para evitar spam
$email_parts = explode('@', $email);
$email_user = $email_parts[0];
$email_domain = isset($email_parts[1]) ? $email_parts[1] : '';
?>

<style>
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
.contact-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}
.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 24px 0;
    border-bottom: 1px solid #f1f5f9;
}
.contact-item:last-child {
    border-bottom: none;
}
.contact-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.contact-icon svg {
    width: 24px;
    height: 24px;
    stroke: #2563eb;
}
.contact-info h3 {
    font-size: 0.85rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0 0 8px;
    font-weight: 600;
}
.contact-info p {
    font-size: 1.1rem;
    color: #0a1628;
    margin: 0;
    font-weight: 500;
}
.contact-info a {
    color: #0a1628;
    text-decoration: none;
    transition: color 0.3s;
}
.contact-info a:hover {
    color: #2563eb;
}
.contact-buttons {
    display: flex;
    gap: 16px;
    margin-top: 30px;
    flex-wrap: wrap;
}
.contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 28px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}
.contact-btn--whatsapp {
    background: #25D366;
    color: white;
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
}
.contact-btn--whatsapp:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(37, 211, 102, 0.4);
}
.contact-btn--call {
    background: white;
    color: #0a1628;
    border: 2px solid #e2e8f0;
}
.contact-btn--call:hover {
    border-color: #2563eb;
    color: #2563eb;
}
.map-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}
.map-container iframe {
    width: 100%;
    height: 400px;
    border: none;
}
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
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
@media (max-width: 768px) {
    .contact-card {
        padding: 30px 24px;
    }
    .contact-buttons {
        flex-direction: column;
    }
    .contact-btn {
        justify-content: center;
    }
    .content-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .content-grid.reverse {
        direction: ltr;
    }
    .contact-main-grid {
        grid-template-columns: 1fr !important;
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
        <span style="display: block; margin-bottom: 8px;">Ver información</span>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
    </div>
</section>

<!-- INFO DE CONTACTO -->
<section style="padding: 80px 0; background: #f8fafc;">
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
        <div class="contact-main-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: start;">
            
            <!-- TARJETA DE CONTACTO -->
            <div class="contact-card">
                <h2 style="font-family: var(--font-display); font-size: 1.75rem; color: #0a1628; margin: 0 0 10px;">Información de contacto</h2>
                <p style="color: #64748b; margin: 0 0 20px;">Elige el medio que prefieras para comunicarte con nosotros.</p>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div class="contact-info">
                        <h3>Teléfono</h3>
                        <p><a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>"><?php echo esc_html($phone); ?></a></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div class="contact-info">
                        <h3>Correo electrónico</h3>
                        <p>
                            <a href="#" class="email-link" data-user="<?php echo esc_attr($email_user); ?>" data-domain="<?php echo esc_attr($email_domain); ?>">
                                <span class="email-text"><?php echo esc_html($email_user); ?> [arroba] <?php echo esc_html($email_domain); ?></span>
                            </a>
                        </p>
                    </div>
                </div>
                
                <?php if ($address): ?>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="contact-info">
                        <h3>Dirección</h3>
                        <p><?php echo esc_html($address); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($hours): ?>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="contact-info">
                        <h3>Horario de atención</h3>
                        <p><?php echo esc_html($hours); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="contact-buttons">
                    <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>?text=<?php echo urlencode('Hola, me gustaría más información sobre sus servicios.'); ?>" target="_blank" class="contact-btn contact-btn--whatsapp">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        WhatsApp
                    </a>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>" class="contact-btn contact-btn--call">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Llamar
                    </a>
                </div>
            </div>
            
            <!-- MAPA O IMAGEN -->
            <div>
                <?php if ($map_embed): ?>
                <div class="map-container">
                    <?php echo $map_embed; ?>
                </div>
                <?php else: ?>
                <div style="background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 100%); border-radius: 20px; padding: 60px 40px; text-align: center; color: white; height: 100%; min-height: 400px; display: flex; flex-direction: column; justify-content: center;">
                    <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-display); font-size: 1.5rem; margin-bottom: 12px;">Encuéntranos</h3>
                    <p style="opacity: 0.8; line-height: 1.7;"><?php echo esc_html($address); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- BLOQUE ADICIONAL -->
<?php if ($content): ?>
<section style="padding: 80px 0;">
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
        <?php if ($layout === 'text-only' || empty($content_image_url)): ?>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <div style="color: #4b5563; line-height: 1.9; font-size: 1.05rem;"><?php echo wp_kses_post($content); ?></div>
            </div>
        <?php elseif ($layout === 'image-left'): ?>
            <div class="content-grid">
                <div>
                    <img src="<?php echo esc_url($content_image_url); ?>" alt="">
                </div>
                <div style="color: #4b5563; line-height: 1.9; font-size: 1.05rem;"><?php echo wp_kses_post($content); ?></div>
            </div>
        <?php elseif ($layout === 'image-right'): ?>
            <div class="content-grid reverse">
                <div>
                    <img src="<?php echo esc_url($content_image_url); ?>" alt="">
                </div>
                <div style="color: #4b5563; line-height: 1.9; font-size: 1.05rem;"><?php echo wp_kses_post($content); ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Script para revelar email al hacer click -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailLinks = document.querySelectorAll('.email-link');
    emailLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const user = this.dataset.user;
            const domain = this.dataset.domain;
            const email = user + '@' + domain;
            this.href = 'mailto:' + email;
            this.querySelector('.email-text').textContent = email;
            window.location.href = 'mailto:' + email;
        });
    });
});
</script>

<?php get_footer(); ?>
