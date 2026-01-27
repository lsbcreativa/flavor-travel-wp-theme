</main>

<?php $social_links = flavor_get_social_links(); ?>

<footer style="background: #111827; color: white; padding: 60px 0 30px;">
    <style>
        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-col {
            text-align: left;
        }
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .footer-social {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s;
        }
        .footer-social a:hover {
            background: #2563eb;
            transform: translateY(-3px);
        }
        @media (max-width: 1024px) {
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }
            .footer-col {
                text-align: center;
            }
            .footer-logo {
                justify-content: center;
            }
            .footer-social {
                justify-content: center;
            }
        }
    </style>
    
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-logo">
                    <div style="width: 40px; height: 40px; background: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <span style="font-family: 'DM Serif Display', serif; font-size: 1.2rem;"><?php echo get_bloginfo('name') ?: 'Tu Agencia'; ?></span>
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; line-height: 1.6;">Tu agencia de viajes de confianza para explorar el mundo.</p>
                
                <?php if (!empty($social_links)): ?>
                <div class="footer-social">
                    <?php foreach ($social_links as $key => $data): ?>
                    <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo $data['name']; ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo in_array($key, ['whatsapp_social', 'youtube', 'facebook']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $data['icon']; ?></svg>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <h4 style="font-size: 1rem; margin-bottom: 16px; font-weight: 600;">Destinos</h4>
                <a href="<?php echo get_post_type_archive_link('destino'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">Ver todos</a>
                <a href="<?php echo get_post_type_archive_link('paquete'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">Tours</a>
                <a href="<?php echo get_post_type_archive_link('oferta'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">Ofertas</a>
            </div>
            
            <div class="footer-col">
                <h4 style="font-size: 1rem; margin-bottom: 16px; font-weight: 600;">Empresa</h4>
                <a href="<?php echo home_url('/nosotros/'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">Nosotros</a>
                <a href="<?php echo home_url('/contacto/'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">Contacto</a>
            </div>
            
            <div class="footer-col">
                <h4 style="font-size: 1rem; margin-bottom: 16px; font-weight: 600;">Contacto</h4>
                <a href="tel:<?php echo get_theme_mod('flavor_phone', '+00123456789'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">
                    <?php echo get_theme_mod('flavor_phone', '+00 123 456 789'); ?>
                </a>
                <a href="mailto:<?php echo get_theme_mod('flavor_email', 'info@tuagencia.com'); ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 10px; font-size: 0.9rem; text-decoration: none;">
                    <?php echo get_theme_mod('flavor_email', 'info@tuagencia.com'); ?>
                </a>
            </div>
        </div>
        
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 24px; text-align: center;">
            <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">© <?php echo date('Y'); ?> <?php echo get_bloginfo('name') ?: 'Tu Agencia'; ?>. Todos los derechos reservados. · Creado por <a href="https://lsbcreativa.com" target="_blank" rel="noopener" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s;">LSB Creativa</a></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
