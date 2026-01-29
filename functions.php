<?php
if (!defined('ABSPATH')) exit;

define('FLAVOR_VERSION', time()); // Cache busting automático
define('FLAVOR_URI', get_template_directory_uri());

function flavor_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'flavor_setup');

function flavor_scripts() {
    wp_enqueue_style('flavor-fonts', 'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@300;400;500;600;700&display=swap', array(), null);
    wp_enqueue_style('flavor-style', get_stylesheet_uri(), array(), FLAVOR_VERSION);
    wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest', array(), null, true);
    wp_enqueue_script('flavor-main', FLAVOR_URI . '/assets/js/main.js', array(), FLAVOR_VERSION, true);
}
add_action('wp_enqueue_scripts', 'flavor_scripts');

// Sanitizador que permite iframes (para Google Maps)
function flavor_sanitize_iframe($input) {
    $allowed = array(
        'iframe' => array(
            'src' => array(),
            'width' => array(),
            'height' => array(),
            'style' => array(),
            'allowfullscreen' => array(),
            'loading' => array(),
            'referrerpolicy' => array(),
            'frameborder' => array(),
            'allow' => array(),
        ),
    );
    return wp_kses($input, $allowed);
}

// ========== CPT Y TAXONOMÍAS ==========
function flavor_cpt() {
    // Destino - CPT legacy, oculto del admin (no se usa)
    register_post_type('destino', array(
        'labels' => array('name' => 'Destinos', 'singular_name' => 'Destino'),
        'public' => false,
        'show_ui' => false,
        'show_in_menu' => false,
        'has_archive' => false,
        'rewrite' => false,
    ));
    register_post_type('paquete', array(
        'labels' => array('name' => 'Tours', 'singular_name' => 'Tour', 'add_new' => 'Agregar Tour', 'add_new_item' => 'Agregar Nuevo Tour'),
        'public' => true,
        'has_archive' => false, // Sin página /tours/
        'rewrite' => false, // URLs personalizadas via filtro
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-palmtree',
        'show_in_rest' => true,
    ));
    register_post_type('oferta', array(
        'labels' => array('name' => 'Ofertas', 'singular_name' => 'Oferta', 'add_new' => 'Agregar Oferta', 'add_new_item' => 'Agregar Nueva Oferta'),
        'public' => true, 'has_archive' => true, 'rewrite' => array('slug' => 'ofertas'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'), 'menu_icon' => 'dashicons-tag', 'show_in_rest' => true,
    ));

    // Salidas Confirmadas
    register_post_type('salida_confirmada', array(
        'labels' => array(
            'name' => 'Salidas Confirmadas',
            'singular_name' => 'Salida Confirmada',
            'add_new' => 'Agregar Salida',
            'add_new_item' => 'Agregar Nueva Salida Confirmada',
            'edit_item' => 'Editar Salida Confirmada',
            'view_item' => 'Ver Salida Confirmada',
        ),
        'public' => true, 'has_archive' => true, 'rewrite' => array('slug' => 'salidas-confirmadas'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'), 'menu_icon' => 'dashicons-calendar-alt', 'show_in_rest' => true,
    ));

    // Eventos Deportivos
    register_post_type('evento_deportivo', array(
        'labels' => array(
            'name' => 'Eventos Deportivos',
            'singular_name' => 'Evento Deportivo',
            'add_new' => 'Agregar Evento',
            'add_new_item' => 'Agregar Nuevo Evento Deportivo',
            'edit_item' => 'Editar Evento Deportivo',
            'view_item' => 'Ver Evento Deportivo',
        ),
        'public' => true, 'has_archive' => true, 'rewrite' => array('slug' => 'eventos-deportivos'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'), 'menu_icon' => 'dashicons-awards', 'show_in_rest' => true,
    ));

    // Taxonomía Continentes - Se asocia a tours, salidas y eventos
    // show_in_menu => false para que no aparezca como submenú de cada CPT
    // El menú principal se agrega por separado con add_menu_page
    register_taxonomy('continente', array('paquete', 'oferta', 'salida_confirmada', 'evento_deportivo'), array(
        'labels' => array(
            'name' => 'Continentes',
            'singular_name' => 'Continente',
            'add_new_item' => 'Agregar Continente',
            'edit_item' => 'Editar Continente',
            'all_items' => 'Todos los Continentes',
            'menu_name' => 'Continentes',
        ),
        'hierarchical' => true,
        'rewrite' => false,
        'show_in_rest' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_admin_column' => true,
    ));
}
add_action('init', 'flavor_cpt');

// Agregar menú separado para Continentes en el admin
function flavor_add_continentes_menu() {
    add_menu_page(
        'Continentes',
        'Continentes',
        'manage_categories',
        'edit-tags.php?taxonomy=continente',
        '',
        'dashicons-location-alt',
        6
    );
}
add_action('admin_menu', 'flavor_add_continentes_menu');

// Resaltar el menú correcto cuando se edita un continente
function flavor_highlight_continentes_menu($parent_file) {
    global $current_screen;
    if ($current_screen->taxonomy == 'continente') {
        return 'edit-tags.php?taxonomy=continente';
    }
    return $parent_file;
}
add_filter('parent_file', 'flavor_highlight_continentes_menu');

// ========== REWRITE RULES PARA CONTINENTES SIN PREFIJO ==========
function flavor_continente_rewrite_rules() {
    // Obtener todos los términos de continente para crear reglas específicas
    $continentes = get_terms(array(
        'taxonomy' => 'continente',
        'hide_empty' => false,
        'parent' => 0, // Solo padres (continentes principales)
    ));

    if (!is_wp_error($continentes) && !empty($continentes)) {
        foreach ($continentes as $continente) {
            // Regla para continentes principales: /america/
            add_rewrite_rule(
                '^' . $continente->slug . '/?$',
                'index.php?continente=' . $continente->slug,
                'top'
            );

            // Regla para subcategorías (países) O tours sin país: /america/peru/ o /america/tour-name/
            // Usa flavor_check_slug para resolver la ambigüedad
            add_rewrite_rule(
                '^' . $continente->slug . '/([^/]+)/?$',
                'index.php?flavor_parent_continente=' . $continente->slug . '&flavor_slug=$matches[1]',
                'top'
            );
        }
    }
}
add_action('init', 'flavor_continente_rewrite_rules', 11);

// Modificar los permalinks de continentes
function flavor_continente_term_link($termlink, $term, $taxonomy) {
    if ($taxonomy === 'continente') {
        $parent = $term->parent;
        if ($parent) {
            $parent_term = get_term($parent, 'continente');
            if ($parent_term && !is_wp_error($parent_term)) {
                return home_url('/' . $parent_term->slug . '/' . $term->slug . '/');
            }
        }
        return home_url('/' . $term->slug . '/');
    }
    return $termlink;
}
add_filter('term_link', 'flavor_continente_term_link', 10, 3);

// Flush rewrite rules cuando se crea/edita un continente
function flavor_flush_on_term_change($term_id, $tt_id, $taxonomy) {
    if ($taxonomy === 'continente') {
        flush_rewrite_rules();
    }
}
add_action('created_term', 'flavor_flush_on_term_change', 10, 3);
add_action('edited_term', 'flavor_flush_on_term_change', 10, 3);

// ========== REWRITE RULES PARA TOURS (continente/pais/tour-name) ==========
function flavor_tour_rewrite_rules() {
    $continentes = get_terms(array(
        'taxonomy' => 'continente',
        'hide_empty' => false,
        'parent' => 0,
    ));

    if (!is_wp_error($continentes) && !empty($continentes)) {
        foreach ($continentes as $continente) {
            // Regla para tours: /america/peru/tour-name/
            add_rewrite_rule(
                '^' . $continente->slug . '/([^/]+)/([^/]+)/?$',
                'index.php?paquete=$matches[2]',
                'top'
            );
        }
    }
}
add_action('init', 'flavor_tour_rewrite_rules', 12);

// Modificar los permalinks de tours para usar /continente/pais/tour-name/
function flavor_tour_permalink($post_link, $post) {
    if ($post->post_type !== 'paquete') {
        return $post_link;
    }

    $terms = get_the_terms($post->ID, 'continente');
    if ($terms && !is_wp_error($terms)) {
        // Buscar el término más específico (país, no continente)
        $pais = null;
        $continente_principal = null;

        foreach ($terms as $term) {
            if ($term->parent > 0) {
                $pais = $term;
                $continente_principal = get_term($term->parent, 'continente');
                break;
            }
        }

        // Si solo tiene continente (sin país), usar el continente
        if (!$pais) {
            foreach ($terms as $term) {
                if ($term->parent == 0) {
                    $continente_principal = $term;
                    break;
                }
            }
        }

        if ($continente_principal && $pais) {
            return home_url('/' . $continente_principal->slug . '/' . $pais->slug . '/' . $post->post_name . '/');
        } elseif ($continente_principal) {
            // Tour asignado solo a continente (sin país específico)
            return home_url('/' . $continente_principal->slug . '/' . $post->post_name . '/');
        }
    }

    // Fallback: usar post_name directamente
    return home_url('/' . $post->post_name . '/');
}
add_filter('post_type_link', 'flavor_tour_permalink', 10, 2);

// Query vars para resolver URLs ambiguas
function flavor_add_query_vars($vars) {
    $vars[] = 'flavor_parent_continente';
    $vars[] = 'flavor_slug';
    return $vars;
}
add_filter('query_vars', 'flavor_add_query_vars');

// Resolver la ambigüedad entre /america/peru/ (país) y /america/tour-name/ (tour sin país)
function flavor_resolve_tour_or_term($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    $parent_continente = get_query_var('flavor_parent_continente');
    $slug = get_query_var('flavor_slug');

    if ($parent_continente && $slug) {
        // Primero verificar si es un término de continente (país)
        $term = get_term_by('slug', $slug, 'continente');

        if ($term) {
            // Es un término de continente (país), configurar como taxonomy query
            $query->set('continente', $slug);
            $query->set('taxonomy', 'continente');
            $query->set('term', $slug);
            $query->is_tax = true;
            $query->is_archive = true;
            $query->is_home = false;
            $query->is_front_page = false;

            // Configurar tax_query para obtener posts
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'continente',
                    'field' => 'slug',
                    'terms' => $slug,
                    'include_children' => true,
                ),
            ));
        } else {
            // No es un término, buscar como tour
            $tour = get_page_by_path($slug, OBJECT, 'paquete');
            if ($tour) {
                $query->set('post_type', 'paquete');
                $query->set('name', $slug);
                $query->set('p', $tour->ID);
                $query->is_single = true;
                $query->is_singular = true;
                $query->is_tax = false;
                $query->is_archive = false;
                $query->is_home = false;
                $query->is_front_page = false;
            } else {
                // No encontrado, dejar que WordPress maneje el 404
                $query->set_404();
            }
        }
    }
}
add_action('pre_get_posts', 'flavor_resolve_tour_or_term', 1);

// Forzar la plantilla correcta para subcategorías de continente
function flavor_force_taxonomy_template($template) {
    $parent_continente = get_query_var('flavor_parent_continente');
    $slug = get_query_var('flavor_slug');

    if ($parent_continente && $slug) {
        $term = get_term_by('slug', $slug, 'continente');

        if ($term) {
            // Forzar la plantilla de taxonomy-continente.php
            $taxonomy_template = locate_template('taxonomy-continente.php');
            if ($taxonomy_template) {
                // Configurar variables globales para que el template funcione
                global $wp_query;
                $wp_query->queried_object = $term;
                $wp_query->queried_object_id = $term->term_id;

                return $taxonomy_template;
            }
        } else {
            // Verificar si es un tour
            $tour = get_page_by_path($slug, OBJECT, 'paquete');
            if ($tour) {
                $single_template = locate_template('single.php');
                if ($single_template) {
                    return $single_template;
                }
            }
        }
    }

    return $template;
}
add_filter('template_include', 'flavor_force_taxonomy_template', 99);

// Prevenir redirect canónico para URLs de subcategorías
function flavor_prevent_canonical_redirect($redirect_url, $requested_url) {
    $parent_continente = get_query_var('flavor_parent_continente');
    $slug = get_query_var('flavor_slug');

    if ($parent_continente && $slug) {
        // Verificar si es un término válido
        $term = get_term_by('slug', $slug, 'continente');
        if ($term) {
            return false; // No redirect
        }
    }

    return $redirect_url;
}
add_filter('redirect_canonical', 'flavor_prevent_canonical_redirect', 10, 2);

// ========== CAMPOS PARA CONTINENTES CON MEDIA LIBRARY ==========
function flavor_admin_scripts($hook) {
    if ($hook == 'term.php' || $hook == 'edit-tags.php' || $hook == 'post.php' || $hook == 'post-new.php') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'flavor_admin_scripts');

function flavor_continente_add_fields() {
    ?>
    <div class="form-field">
        <label>Imagen del Continente</label>
        <div style="margin-bottom: 10px;">
            <input type="url" name="continente_imagen" id="continente_imagen" style="width: 70%;">
            <button type="button" class="button" onclick="flavorSelectImage('continente_imagen', 'continente_preview')">📷 Seleccionar</button>
        </div>
        <div id="continente_preview"></div>
        <p class="description">Selecciona una imagen o pega una URL de Unsplash</p>
    </div>
    <script>
    function flavorSelectImage(inputId, previewId) {
        var frame = wp.media({title: 'Seleccionar imagen', multiple: false});
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            document.getElementById(inputId).value = attachment.url;
            document.getElementById(previewId).innerHTML = '<img src="' + attachment.url + '" style="max-width: 300px; border-radius: 8px; margin-top: 10px;">';
        });
        frame.open();
    }
    </script>
    <?php
}
add_action('continente_add_form_fields', 'flavor_continente_add_fields');

function flavor_continente_edit_fields($term) {
    $imagen = get_term_meta($term->term_id, 'continente_imagen', true);
    ?>
    <tr class="form-field">
        <th><label>Imagen del Continente</label></th>
        <td>
            <div style="margin-bottom: 10px;">
                <input type="url" name="continente_imagen" id="continente_imagen" value="<?php echo esc_attr($imagen); ?>" style="width: 70%;">
                <button type="button" class="button" onclick="flavorSelectImage('continente_imagen', 'continente_preview')">📷 Seleccionar</button>
            </div>
            <div id="continente_preview">
                <?php if ($imagen): ?>
                <img src="<?php echo esc_url($imagen); ?>" style="max-width: 300px; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <p class="description">Esta imagen se usa en las tarjetas de continentes y en el banner de la página del continente</p>
            <script>
            function flavorSelectImage(inputId, previewId) {
                var frame = wp.media({title: 'Seleccionar imagen', multiple: false});
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    document.getElementById(inputId).value = attachment.url;
                    document.getElementById(previewId).innerHTML = '<img src="' + attachment.url + '" style="max-width: 300px; border-radius: 8px;">';
                });
                frame.open();
            }
            </script>
        </td>
    </tr>
    <?php
}
add_action('continente_edit_form_fields', 'flavor_continente_edit_fields');

function flavor_save_continente_fields($term_id) {
    if (isset($_POST['continente_imagen'])) update_term_meta($term_id, 'continente_imagen', esc_url_raw($_POST['continente_imagen']));
}
add_action('created_continente', 'flavor_save_continente_fields');
add_action('edited_continente', 'flavor_save_continente_fields');

// ========== CREAR/ACTUALIZAR CONTINENTES CON IMÁGENES ==========
function flavor_setup_continentes() {
    $continentes = array(
        'África' => array(
            'slug' => 'africa', 
            'imagen' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1920&q=80',
            'desc' => 'Safaris épicos, desiertos infinitos, culturas vibrantes y la vida salvaje más espectacular del planeta.'
        ),
        'América' => array(
            'slug' => 'america', 
            'imagen' => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1920&q=80',
            'desc' => 'Desde la Patagonia hasta Alaska, descubre paisajes únicos, culturas ancestrales y aventuras inolvidables.'
        ),
        'Asia' => array(
            'slug' => 'asia', 
            'imagen' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=1920&q=80',
            'desc' => 'Templos milenarios, megaciudades futuristas y tradiciones que transformarán tu perspectiva del mundo.'
        ),
        'Europa' => array(
            'slug' => 'europa', 
            'imagen' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=1920&q=80',
            'desc' => 'Historia milenaria, arquitectura impresionante y una gastronomía que deleitará todos tus sentidos.'
        ),
        'Oceanía' => array(
            'slug' => 'oceania', 
            'imagen' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=1920&q=80',
            'desc' => 'La Gran Barrera de Coral, playas paradisíacas y una naturaleza única en el mundo.'
        ),
    );
    
    foreach ($continentes as $nombre => $data) {
        $term = term_exists($nombre, 'continente');
        
        if (!$term) {
            // Crear nuevo
            $term = wp_insert_term($nombre, 'continente', array('slug' => $data['slug'], 'description' => $data['desc']));
            if (!is_wp_error($term)) {
                update_term_meta($term['term_id'], 'continente_imagen', $data['imagen']);
            }
        } else {
            // Actualizar existente si no tiene imagen
            $term_id = is_array($term) ? $term['term_id'] : $term;
            $existing_imagen = get_term_meta($term_id, 'continente_imagen', true);
            
            if (empty($existing_imagen)) {
                update_term_meta($term_id, 'continente_imagen', $data['imagen']);
            }
            
            // Actualizar descripción si está vacía
            $term_obj = get_term($term_id, 'continente');
            if (empty($term_obj->description)) {
                wp_update_term($term_id, 'continente', array('description' => $data['desc']));
            }
        }
    }
}
add_action('admin_init', 'flavor_setup_continentes');


// ========== CUSTOMIZER ==========
function flavor_customizer($wp_customize) {
    // CONTACTO
    $wp_customize->add_section('flavor_contact', array('title' => '📞 Contacto', 'priority' => 30));
    $wp_customize->add_setting('flavor_phone', array('default' => '+00 123 456 789', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_phone', array('label' => 'Teléfono', 'section' => 'flavor_contact'));
    $wp_customize->add_setting('flavor_whatsapp', array('default' => '00123456789', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_whatsapp', array('label' => 'WhatsApp (solo números)', 'section' => 'flavor_contact'));
    $wp_customize->add_setting('flavor_email', array('default' => 'info@tuagencia.com', 'sanitize_callback' => 'sanitize_email'));
    $wp_customize->add_control('flavor_email', array('label' => 'Email', 'section' => 'flavor_contact', 'type' => 'email'));
    
    // BADGES
    $wp_customize->add_section('flavor_trust', array('title' => '✅ Badges de Confianza', 'priority' => 35));
    $wp_customize->add_setting('flavor_trust_1', array('default' => 'Garantía mejor precio', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_trust_1', array('label' => 'Badge 1', 'section' => 'flavor_trust'));
    $wp_customize->add_setting('flavor_trust_2', array('default' => 'Hasta 12 cuotas sin interés', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_trust_2', array('label' => 'Badge 2', 'section' => 'flavor_trust'));
    $wp_customize->add_setting('flavor_trust_3', array('default' => 'Cancelación flexible', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_trust_3', array('label' => 'Badge 3', 'section' => 'flavor_trust'));
    
    // PÁGINAS DE ARCHIVO
    // ========== PÁGINA DESTINOS ==========
    $wp_customize->add_section('flavor_destinos_page', array('title' => '🗺️ Página Destinos', 'priority' => 40));
    
    // Básico
    $wp_customize->add_setting('flavor_destinos_title', array('default' => 'Destinos', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_title', array('label' => 'Título', 'section' => 'flavor_destinos_page'));
    $wp_customize->add_setting('flavor_destinos_desc', array('default' => 'Explora destinos increíbles en los 5 continentes.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_desc', array('label' => 'Descripción', 'section' => 'flavor_destinos_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_destinos_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_destinos_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_destinos_page', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_destinos_seo_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_destinos_seo_text', array('label' => 'Texto adicional (acepta HTML)', 'section' => 'flavor_destinos_page', 'description' => 'Acepta enlaces, negritas, imágenes, etc.', 'type' => 'textarea'));
    
    // Imagen SEO
    $wp_customize->add_setting('flavor_destinos_seo_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_destinos_seo_image', array(
        'label' => 'Imagen del bloque SEO',
        'section' => 'flavor_destinos_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_destinos_seo_layout', array('default' => 'text-only', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_seo_layout', array(
        'label' => 'Diseño del bloque',
        'section' => 'flavor_destinos_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
            'image-top' => 'Imagen arriba',
            'image-bottom' => 'Imagen abajo',
        ),
    ));
    
    // Posición contenido
    $wp_customize->add_setting('flavor_destinos_position', array('default' => 'center', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_position', array('label' => 'Posición del contenido', 'section' => 'flavor_destinos_page', 'type' => 'select', 'choices' => array('top' => 'Arriba', 'center' => 'Centro', 'bottom' => 'Abajo')));
    
    // Badge
    $wp_customize->add_setting('flavor_destinos_show_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_destinos_show_badge', array('label' => 'Mostrar badge contador', 'section' => 'flavor_destinos_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_destinos_hide_empty_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_destinos_hide_empty_badge', array('label' => 'Ocultar badge si hay 0 items', 'section' => 'flavor_destinos_page', 'type' => 'checkbox'));
    
    // CTA Button
    $wp_customize->add_setting('flavor_destinos_show_cta', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_destinos_show_cta', array('label' => 'Mostrar botón CTA', 'section' => 'flavor_destinos_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_destinos_cta_text', array('default' => 'Consultar por WhatsApp', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_cta_text', array('label' => 'Texto del botón', 'section' => 'flavor_destinos_page'));
    $wp_customize->add_setting('flavor_destinos_cta_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_destinos_cta_url', array('label' => 'URL del botón (vacío = WhatsApp)', 'section' => 'flavor_destinos_page', 'type' => 'url'));
    
    // Scroll indicator
    $wp_customize->add_setting('flavor_destinos_show_scroll', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_destinos_show_scroll', array('label' => 'Mostrar indicador de scroll', 'section' => 'flavor_destinos_page', 'type' => 'checkbox'));
    
    // Search bar (solo destinos)
    $wp_customize->add_setting('flavor_destinos_show_search', array('default' => false, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_destinos_show_search', array('label' => 'Mostrar barra de búsqueda', 'section' => 'flavor_destinos_page', 'type' => 'checkbox'));

    // ========== PÁGINA VIAJES ==========
    $wp_customize->add_section('flavor_viajes_page', array('title' => '✈️ Página Viajes', 'priority' => 40));

    // Hero
    $wp_customize->add_setting('flavor_viajes_title', array('default' => 'Nuestros Viajes', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_viajes_title', array('label' => 'Título', 'section' => 'flavor_viajes_page'));
    $wp_customize->add_setting('flavor_viajes_desc', array('default' => 'Descubre nuestras salidas confirmadas y eventos deportivos. Experiencias únicas con fechas garantizadas.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_viajes_desc', array('label' => 'Descripción', 'section' => 'flavor_viajes_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_viajes_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_viajes_image', array('label' => 'Imagen de fondo Hero (URL)', 'section' => 'flavor_viajes_page', 'type' => 'url'));

    // Imágenes de las tarjetas
    $wp_customize->add_setting('flavor_salidas_image', array('default' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_salidas_image', array('label' => 'Imagen tarjeta Salidas Confirmadas (URL)', 'section' => 'flavor_viajes_page', 'type' => 'url'));
    $wp_customize->add_setting('flavor_eventos_image', array('default' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&q=80', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_eventos_image', array('label' => 'Imagen tarjeta Eventos Deportivos (URL)', 'section' => 'flavor_viajes_page', 'type' => 'url'));

    // Texto introductorio (arriba de las tarjetas)
    $wp_customize->add_setting('flavor_viajes_intro', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_viajes_intro', array('label' => 'Texto introductorio (acepta HTML)', 'section' => 'flavor_viajes_page', 'description' => 'Se muestra ARRIBA de las tarjetas. Acepta enlaces, negritas, etc.', 'type' => 'textarea'));

    // Bloque de contenido adicional (abajo de las tarjetas)
    $wp_customize->add_setting('flavor_viajes_content', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_viajes_content', array('label' => 'Texto adicional (acepta HTML)', 'section' => 'flavor_viajes_page', 'description' => 'Se muestra debajo de las tarjetas. Acepta enlaces, negritas, etc.', 'type' => 'textarea'));

    $wp_customize->add_setting('flavor_viajes_content_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_viajes_content_image', array(
        'label' => 'Imagen del bloque de texto',
        'section' => 'flavor_viajes_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_viajes_layout', array('default' => 'text-only', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_viajes_layout', array(
        'label' => 'Diseño del bloque',
        'section' => 'flavor_viajes_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
        ),
    ));

    // ========== PÁGINA TOURS ==========
    $wp_customize->add_section('flavor_tours_page', array('title' => '🌴 Página Tours', 'priority' => 41));
    
    $wp_customize->add_setting('flavor_tours_title', array('default' => 'Tours', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_title', array('label' => 'Título', 'section' => 'flavor_tours_page'));
    $wp_customize->add_setting('flavor_tours_desc', array('default' => 'Descubre experiencias únicas en cada rincón del mundo.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_desc', array('label' => 'Descripción', 'section' => 'flavor_tours_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_tours_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_tours_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_tours_page', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_tours_seo_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_tours_seo_text', array('label' => 'Texto adicional (acepta HTML)', 'section' => 'flavor_tours_page', 'description' => 'Acepta enlaces, negritas, imágenes, etc.', 'type' => 'textarea'));
    
    // Imagen SEO
    $wp_customize->add_setting('flavor_tours_seo_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_tours_seo_image', array(
        'label' => 'Imagen del bloque SEO',
        'section' => 'flavor_tours_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_tours_seo_layout', array('default' => 'text-only', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_seo_layout', array(
        'label' => 'Diseño del bloque',
        'section' => 'flavor_tours_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
            'image-top' => 'Imagen arriba',
            'image-bottom' => 'Imagen abajo',
        ),
    ));
    
    $wp_customize->add_setting('flavor_tours_position', array('default' => 'center', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_position', array('label' => 'Posición del contenido', 'section' => 'flavor_tours_page', 'type' => 'select', 'choices' => array('top' => 'Arriba', 'center' => 'Centro', 'bottom' => 'Abajo')));
    
    $wp_customize->add_setting('flavor_tours_show_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_tours_show_badge', array('label' => 'Mostrar badge contador', 'section' => 'flavor_tours_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_tours_hide_empty_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_tours_hide_empty_badge', array('label' => 'Ocultar badge si hay 0 items', 'section' => 'flavor_tours_page', 'type' => 'checkbox'));
    
    $wp_customize->add_setting('flavor_tours_show_cta', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_tours_show_cta', array('label' => 'Mostrar botón CTA', 'section' => 'flavor_tours_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_tours_cta_text', array('default' => 'Consultar por WhatsApp', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_cta_text', array('label' => 'Texto del botón', 'section' => 'flavor_tours_page'));
    $wp_customize->add_setting('flavor_tours_cta_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_tours_cta_url', array('label' => 'URL del botón (vacío = WhatsApp)', 'section' => 'flavor_tours_page', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_tours_show_scroll', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_tours_show_scroll', array('label' => 'Mostrar indicador de scroll', 'section' => 'flavor_tours_page', 'type' => 'checkbox'));
    
    // ========== PÁGINA OFERTAS ==========
    $wp_customize->add_section('flavor_ofertas_page', array('title' => '🏷️ Página Ofertas', 'priority' => 42));
    
    $wp_customize->add_setting('flavor_ofertas_title', array('default' => 'Ofertas', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_title', array('label' => 'Título', 'section' => 'flavor_ofertas_page'));
    $wp_customize->add_setting('flavor_ofertas_desc', array('default' => 'Aprovecha nuestras ofertas especiales y descuentos exclusivos.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_desc', array('label' => 'Descripción', 'section' => 'flavor_ofertas_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_ofertas_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_ofertas_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_ofertas_page', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_ofertas_seo_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_ofertas_seo_text', array('label' => 'Texto adicional (acepta HTML)', 'section' => 'flavor_ofertas_page', 'description' => 'Acepta enlaces, negritas, imágenes, etc.', 'type' => 'textarea'));
    
    // Imagen SEO
    $wp_customize->add_setting('flavor_ofertas_seo_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_ofertas_seo_image', array(
        'label' => 'Imagen del bloque SEO',
        'section' => 'flavor_ofertas_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_ofertas_seo_layout', array('default' => 'text-only', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_seo_layout', array(
        'label' => 'Diseño del bloque',
        'section' => 'flavor_ofertas_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
            'image-top' => 'Imagen arriba',
            'image-bottom' => 'Imagen abajo',
        ),
    ));
    
    $wp_customize->add_setting('flavor_ofertas_position', array('default' => 'center', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_position', array('label' => 'Posición del contenido', 'section' => 'flavor_ofertas_page', 'type' => 'select', 'choices' => array('top' => 'Arriba', 'center' => 'Centro', 'bottom' => 'Abajo')));
    
    $wp_customize->add_setting('flavor_ofertas_show_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_ofertas_show_badge', array('label' => 'Mostrar badge contador', 'section' => 'flavor_ofertas_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_ofertas_hide_empty_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_ofertas_hide_empty_badge', array('label' => 'Ocultar badge si hay 0 items', 'section' => 'flavor_ofertas_page', 'type' => 'checkbox'));
    
    $wp_customize->add_setting('flavor_ofertas_show_cta', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_ofertas_show_cta', array('label' => 'Mostrar botón CTA', 'section' => 'flavor_ofertas_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_ofertas_cta_text', array('default' => 'Ver ofertas ahora', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_cta_text', array('label' => 'Texto del botón', 'section' => 'flavor_ofertas_page'));
    $wp_customize->add_setting('flavor_ofertas_cta_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_ofertas_cta_url', array('label' => 'URL del botón (vacío = WhatsApp)', 'section' => 'flavor_ofertas_page', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_ofertas_show_scroll', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_ofertas_show_scroll', array('label' => 'Mostrar indicador de scroll', 'section' => 'flavor_ofertas_page', 'type' => 'checkbox'));

    // PÁGINA NOSOTROS
    $wp_customize->add_section('flavor_nosotros_page', array('title' => '👥 Página Nosotros', 'priority' => 43));
    $wp_customize->add_setting('flavor_nosotros_title', array('default' => 'Nosotros', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_nosotros_title', array('label' => 'Título', 'section' => 'flavor_nosotros_page'));
    $wp_customize->add_setting('flavor_nosotros_desc', array('default' => 'Conoce nuestra historia y pasión por los viajes.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_nosotros_desc', array('label' => 'Descripción', 'section' => 'flavor_nosotros_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_nosotros_image', array('default' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&q=80', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_nosotros_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_nosotros_page', 'type' => 'url'));
    $wp_customize->add_setting('flavor_nosotros_content', array('default' => 'Somos una agencia de viajes con más de 10 años de experiencia.', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_nosotros_content', array('label' => 'Contenido principal (acepta HTML)', 'section' => 'flavor_nosotros_page', 'type' => 'textarea'));
    
    // Imagen del contenido
    $wp_customize->add_setting('flavor_nosotros_content_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_nosotros_content_image', array(
        'label' => 'Imagen del contenido',
        'section' => 'flavor_nosotros_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_nosotros_layout', array('default' => 'image-right', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_nosotros_layout', array(
        'label' => 'Diseño del contenido',
        'section' => 'flavor_nosotros_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
            'image-top' => 'Imagen arriba',
            'image-bottom' => 'Imagen abajo',
        ),
    ));
    
    // Bloque adicional
    $wp_customize->add_setting('flavor_nosotros_block2_title', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_nosotros_block2_title', array('label' => 'Bloque 2 - Título', 'section' => 'flavor_nosotros_page'));
    $wp_customize->add_setting('flavor_nosotros_block2_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_nosotros_block2_text', array('label' => 'Bloque 2 - Texto (acepta HTML)', 'section' => 'flavor_nosotros_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_nosotros_block2_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_nosotros_block2_image', array(
        'label' => 'Bloque 2 - Imagen',
        'section' => 'flavor_nosotros_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_nosotros_block2_layout', array('default' => 'image-left', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_nosotros_block2_layout', array(
        'label' => 'Bloque 2 - Diseño',
        'section' => 'flavor_nosotros_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
        ),
    ));

    // ========== PÁGINA SALIDAS CONFIRMADAS ==========
    $wp_customize->add_section('flavor_salidas_page', array('title' => '📅 Página Salidas Confirmadas', 'priority' => 42));

    $wp_customize->add_setting('flavor_salidas_title', array('default' => 'Salidas Confirmadas', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_salidas_title', array('label' => 'Título', 'section' => 'flavor_salidas_page'));
    $wp_customize->add_setting('flavor_salidas_desc', array('default' => 'Viajes con fechas y grupos confirmados. Reserva tu lugar ahora.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_salidas_desc', array('label' => 'Descripción', 'section' => 'flavor_salidas_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_salidas_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_salidas_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_salidas_page', 'type' => 'url'));

    $wp_customize->add_setting('flavor_salidas_position', array('default' => 'center', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_salidas_position', array('label' => 'Posición del contenido', 'section' => 'flavor_salidas_page', 'type' => 'select', 'choices' => array('top' => 'Arriba', 'center' => 'Centro', 'bottom' => 'Abajo')));

    $wp_customize->add_setting('flavor_salidas_show_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_salidas_show_badge', array('label' => 'Mostrar badge contador', 'section' => 'flavor_salidas_page', 'type' => 'checkbox'));

    $wp_customize->add_setting('flavor_salidas_show_cta', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_salidas_show_cta', array('label' => 'Mostrar botón CTA', 'section' => 'flavor_salidas_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_salidas_cta_text', array('default' => 'Consultar por WhatsApp', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_salidas_cta_text', array('label' => 'Texto del botón', 'section' => 'flavor_salidas_page'));
    $wp_customize->add_setting('flavor_salidas_cta_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_salidas_cta_url', array('label' => 'URL del botón (vacío = WhatsApp)', 'section' => 'flavor_salidas_page', 'type' => 'url'));

    $wp_customize->add_setting('flavor_salidas_show_scroll', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_salidas_show_scroll', array('label' => 'Mostrar indicador de scroll', 'section' => 'flavor_salidas_page', 'type' => 'checkbox'));

    // ========== PÁGINA EVENTOS DEPORTIVOS ==========
    $wp_customize->add_section('flavor_eventos_page', array('title' => '🏆 Página Eventos Deportivos', 'priority' => 43));

    $wp_customize->add_setting('flavor_eventos_title', array('default' => 'Eventos Deportivos', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_eventos_title', array('label' => 'Título', 'section' => 'flavor_eventos_page'));
    $wp_customize->add_setting('flavor_eventos_desc', array('default' => 'Vive la emoción de los mejores eventos deportivos del mundo.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_eventos_desc', array('label' => 'Descripción', 'section' => 'flavor_eventos_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_eventos_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_eventos_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_eventos_page', 'type' => 'url'));

    $wp_customize->add_setting('flavor_eventos_position', array('default' => 'center', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_eventos_position', array('label' => 'Posición del contenido', 'section' => 'flavor_eventos_page', 'type' => 'select', 'choices' => array('top' => 'Arriba', 'center' => 'Centro', 'bottom' => 'Abajo')));

    $wp_customize->add_setting('flavor_eventos_show_badge', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_eventos_show_badge', array('label' => 'Mostrar badge contador', 'section' => 'flavor_eventos_page', 'type' => 'checkbox'));

    $wp_customize->add_setting('flavor_eventos_show_cta', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_eventos_show_cta', array('label' => 'Mostrar botón CTA', 'section' => 'flavor_eventos_page', 'type' => 'checkbox'));
    $wp_customize->add_setting('flavor_eventos_cta_text', array('default' => 'Consultar por WhatsApp', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_eventos_cta_text', array('label' => 'Texto del botón', 'section' => 'flavor_eventos_page'));
    $wp_customize->add_setting('flavor_eventos_cta_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_eventos_cta_url', array('label' => 'URL del botón (vacío = WhatsApp)', 'section' => 'flavor_eventos_page', 'type' => 'url'));

    $wp_customize->add_setting('flavor_eventos_show_scroll', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_eventos_show_scroll', array('label' => 'Mostrar indicador de scroll', 'section' => 'flavor_eventos_page', 'type' => 'checkbox'));

    // PÁGINA CONTACTO
    $wp_customize->add_section('flavor_contacto_page', array('title' => '📧 Página Contacto', 'priority' => 44));
    $wp_customize->add_setting('flavor_contacto_title', array('default' => 'Contacto', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_contacto_title', array('label' => 'Título', 'section' => 'flavor_contacto_page'));
    $wp_customize->add_setting('flavor_contacto_desc', array('default' => 'Estamos aquí para ayudarte a planificar tu próximo viaje.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_contacto_desc', array('label' => 'Descripción', 'section' => 'flavor_contacto_page', 'type' => 'textarea'));
    $wp_customize->add_setting('flavor_contacto_image', array('default' => 'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1920&q=80', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_contacto_image', array('label' => 'Imagen de fondo (URL)', 'section' => 'flavor_contacto_page', 'type' => 'url'));
    $wp_customize->add_setting('flavor_contacto_address', array('default' => 'Av. Principal 123, Ciudad', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_contacto_address', array('label' => 'Dirección', 'section' => 'flavor_contacto_page'));
    
    // Teléfono de la página contacto
    $wp_customize->add_setting('flavor_contacto_phone', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_contacto_phone', array('label' => 'Teléfono', 'section' => 'flavor_contacto_page', 'description' => 'Ej: +00 123 456 789 (si está vacío usa el global de Contacto)'));
    
    // Email de la página contacto
    $wp_customize->add_setting('flavor_contacto_email', array('default' => '', 'sanitize_callback' => 'sanitize_email'));
    $wp_customize->add_control('flavor_contacto_email', array('label' => 'Email', 'section' => 'flavor_contacto_page', 'type' => 'email', 'description' => 'Se mostrará protegido contra spam'));
    
    // WhatsApp de la página contacto
    $wp_customize->add_setting('flavor_contacto_whatsapp', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_contacto_whatsapp', array('label' => 'WhatsApp (solo números)', 'section' => 'flavor_contacto_page', 'description' => 'Ej: 00123456789 (si está vacío usa el global de Contacto)'));
    
    // Texto adicional
    $wp_customize->add_setting('flavor_contacto_content', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_contacto_content', array('label' => 'Contenido adicional (acepta HTML)', 'section' => 'flavor_contacto_page', 'type' => 'textarea'));
    
    // Imagen
    $wp_customize->add_setting('flavor_contacto_content_image', array('default' => '', 'sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_contacto_content_image', array(
        'label' => 'Imagen del contenido',
        'section' => 'flavor_contacto_page',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('flavor_contacto_layout', array('default' => 'image-right', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_contacto_layout', array(
        'label' => 'Diseño del contenido',
        'section' => 'flavor_contacto_page',
        'type' => 'select',
        'choices' => array(
            'text-only' => 'Solo texto',
            'image-left' => 'Imagen a la izquierda',
            'image-right' => 'Imagen a la derecha',
        ),
    ));
    
    // Mapa (permite iframes)
    $wp_customize->add_setting('flavor_contacto_map', array('default' => '', 'sanitize_callback' => 'flavor_sanitize_iframe'));
    $wp_customize->add_control('flavor_contacto_map', array('label' => 'Código iframe de Google Maps', 'section' => 'flavor_contacto_page', 'type' => 'textarea', 'description' => 'Pega el código embed de Google Maps'));

    // ========== HOME - HERO PRINCIPAL ==========
    $wp_customize->add_section('flavor_hero', array('title' => '🏠 Hero Principal', 'priority' => 50));
    
    $wp_customize->add_setting('flavor_hero_badge', array('default' => 'Más de 50 destinos en 5 continentes', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_hero_badge', array('label' => 'Badge superior', 'section' => 'flavor_hero'));
    
    $wp_customize->add_setting('flavor_hero_title', array('default' => 'Explora el mundo sin límites', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_hero_title', array('label' => 'Título principal', 'section' => 'flavor_hero'));
    
    $wp_customize->add_setting('flavor_hero_subtitle', array('default' => 'Desde las playas del Caribe hasta los templos de Asia. Tu próxima aventura te espera.', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('flavor_hero_subtitle', array('label' => 'Subtítulo', 'section' => 'flavor_hero', 'type' => 'textarea'));
    
    $wp_customize->add_setting('flavor_hero_btn1_text', array('default' => 'Explorar Destinos', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_hero_btn1_text', array('label' => 'Botón 1 - Texto', 'section' => 'flavor_hero'));
    
    $wp_customize->add_setting('flavor_hero_btn1_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_hero_btn1_url', array('label' => 'Botón 1 - URL (vacío = Destinos)', 'section' => 'flavor_hero', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_hero_btn2_text', array('default' => 'Ver Ofertas', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_hero_btn2_text', array('label' => 'Botón 2 - Texto', 'section' => 'flavor_hero'));
    
    $wp_customize->add_setting('flavor_hero_btn2_url', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_hero_btn2_url', array('label' => 'Botón 2 - URL (vacío = Ofertas)', 'section' => 'flavor_hero', 'type' => 'url'));
    
    // ========== HOME - CONTINENTES ==========
    $wp_customize->add_section('flavor_home_continentes', array('title' => '🌍 Home - Continentes', 'priority' => 51));
    
    $wp_customize->add_setting('flavor_continentes_subtitle', array('default' => 'Destinos por continente', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_continentes_subtitle', array('label' => 'Subtítulo', 'section' => 'flavor_home_continentes'));
    
    $wp_customize->add_setting('flavor_continentes_title', array('default' => 'Explora el mundo', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_continentes_title', array('label' => 'Título', 'section' => 'flavor_home_continentes'));
    
    $wp_customize->add_setting('flavor_continentes_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_continentes_text', array('label' => 'Texto/Descripción (acepta HTML)', 'section' => 'flavor_home_continentes', 'type' => 'textarea', 'description' => 'Puedes usar &lt;a href=""&gt;, &lt;strong&gt;, &lt;em&gt;'));
    
    // ========== HOME - OFERTAS ==========
    $wp_customize->add_section('flavor_home_ofertas', array('title' => '🏷️ Home - Ofertas', 'priority' => 52));
    
    $wp_customize->add_setting('flavor_ofertas_home_subtitle', array('default' => 'Ofertas especiales', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_home_subtitle', array('label' => 'Subtítulo', 'section' => 'flavor_home_ofertas'));
    
    $wp_customize->add_setting('flavor_ofertas_home_title', array('default' => 'Descuentos exclusivos', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_ofertas_home_title', array('label' => 'Título', 'section' => 'flavor_home_ofertas'));
    
    $wp_customize->add_setting('flavor_ofertas_home_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_ofertas_home_text', array('label' => 'Texto/Descripción (acepta HTML)', 'section' => 'flavor_home_ofertas', 'type' => 'textarea'));
    
    // ========== HOME - TOURS ==========
    $wp_customize->add_section('flavor_home_tours', array('title' => '🌴 Home - Tours', 'priority' => 53));
    
    $wp_customize->add_setting('flavor_tours_home_subtitle', array('default' => 'Experiencias únicas', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_home_subtitle', array('label' => 'Subtítulo', 'section' => 'flavor_home_tours'));
    
    $wp_customize->add_setting('flavor_tours_home_title', array('default' => 'Tours destacados', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_tours_home_title', array('label' => 'Título', 'section' => 'flavor_home_tours'));
    
    $wp_customize->add_setting('flavor_tours_home_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_tours_home_text', array('label' => 'Texto/Descripción (acepta HTML)', 'section' => 'flavor_home_tours', 'type' => 'textarea'));
    
    // ========== HOME - DESTINOS ==========
    $wp_customize->add_section('flavor_home_destinos', array('title' => '🗺️ Home - Destinos', 'priority' => 54));
    
    $wp_customize->add_setting('flavor_destinos_home_subtitle', array('default' => 'Los más populares', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_home_subtitle', array('label' => 'Subtítulo', 'section' => 'flavor_home_destinos'));
    
    $wp_customize->add_setting('flavor_destinos_home_title', array('default' => 'Destinos destacados', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_destinos_home_title', array('label' => 'Título', 'section' => 'flavor_home_destinos'));
    
    $wp_customize->add_setting('flavor_destinos_home_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_destinos_home_text', array('label' => 'Texto/Descripción (acepta HTML)', 'section' => 'flavor_home_destinos', 'type' => 'textarea'));
    
    // ========== HOME - CTA ==========
    $wp_customize->add_section('flavor_cta', array('title' => '📢 Home - CTA Final', 'priority' => 55));
    
    $wp_customize->add_setting('flavor_cta_title', array('default' => 'Llevamos la satisfacción de tus pasajeros al siguiente nivel', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_cta_title', array('label' => 'Título', 'section' => 'flavor_cta'));
    
    $wp_customize->add_setting('flavor_cta_subtitle', array('default' => 'Contáctanos y diseñaremos el viaje perfecto para ti.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_cta_subtitle', array('label' => 'Subtítulo', 'section' => 'flavor_cta'));
    
    $wp_customize->add_setting('flavor_cta_button', array('default' => 'Consultar por WhatsApp', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_cta_button', array('label' => 'Texto del botón', 'section' => 'flavor_cta'));
    
    $wp_customize->add_setting('flavor_cta_text', array('default' => '', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('flavor_cta_text', array('label' => 'Texto adicional (acepta HTML)', 'section' => 'flavor_cta', 'type' => 'textarea'));
    
    // ========== HOME - NEWSLETTER ==========
    $wp_customize->add_section('flavor_newsletter', array('title' => '📧 Newsletter', 'priority' => 56));
    
    $wp_customize->add_setting('flavor_newsletter_title', array('default' => 'Recibe ofertas exclusivas', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_newsletter_title', array('label' => 'Título', 'section' => 'flavor_newsletter'));
    
    $wp_customize->add_setting('flavor_newsletter_desc', array('default' => 'Suscríbete y recibe las mejores ofertas en tu correo.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_newsletter_desc', array('label' => 'Descripción', 'section' => 'flavor_newsletter'));

}
add_action('customize_register', 'flavor_customizer');

// ========== META BOXES CON FECHAS DE VIGENCIA ==========
function flavor_meta_boxes() {
    $all_cpts = array('paquete', 'oferta', 'destino', 'salida_confirmada', 'evento_deportivo');
    add_meta_box('flavor_precio', '💰 Precios y Detalles', 'flavor_precio_cb', $all_cpts, 'side', 'high');
    add_meta_box('flavor_vigencia', '📅 Vigencia (Visibilidad)', 'flavor_vigencia_cb', $all_cpts, 'side', 'high');
}
add_action('add_meta_boxes', 'flavor_meta_boxes');

function flavor_precio_cb($post) {
    wp_nonce_field('flavor_save', 'flavor_nonce');
    $p = get_post_meta($post->ID, '_flavor_precio', true);
    $po = get_post_meta($post->ID, '_flavor_precio_oferta', true);
    $d = get_post_meta($post->ID, '_flavor_duracion', true);
    ?>
    <p>
        <label><strong>Precio Regular ($)</strong></label><br>
        <input type="number" name="flavor_precio" value="<?php echo esc_attr($p); ?>" style="width:100%">
    </p>
    <p>
        <label><strong>Precio Oferta ($)</strong></label><br>
        <input type="number" name="flavor_precio_oferta" value="<?php echo esc_attr($po); ?>" style="width:100%">
        <br><small>Dejar vacío si no hay descuento</small>
    </p>
    <p>
        <label><strong>Duración</strong></label><br>
        <input type="text" name="flavor_duracion" id="flavor_duracion" value="<?php echo esc_attr($d); ?>" style="width:100%" placeholder="5 días / 4 noches">
        <br><small>Escribe solo el número de días (ej: 5) y se autocompletará</small>
    </p>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var duracionInput = document.getElementById('flavor_duracion');
        if (!duracionInput) return;

        duracionInput.addEventListener('blur', function() {
            var value = this.value.trim();
            // Si es solo un número, autocompletar
            if (/^\d+$/.test(value)) {
                var dias = parseInt(value);
                var noches = dias - 1;
                if (noches < 0) noches = 0;
                var diaText = dias === 1 ? 'día' : 'días';
                var nocheText = noches === 1 ? 'noche' : 'noches';
                this.value = dias + ' ' + diaText + ' / ' + noches + ' ' + nocheText;
            }
        });
    });
    </script>
    <?php
}

function flavor_vigencia_cb($post) {
    $fecha_inicio = get_post_meta($post->ID, '_flavor_fecha_inicio', true);
    $fecha_fin = get_post_meta($post->ID, '_flavor_fecha_fin', true);
    $siempre_visible = get_post_meta($post->ID, '_flavor_siempre_visible', true);
    ?>
    <p>
        <label>
            <input type="checkbox" name="flavor_siempre_visible" value="1" <?php checked($siempre_visible, '1'); ?>>
            <strong>Siempre visible</strong>
        </label>
        <br><small>Si está marcado, ignora las fechas</small>
    </p>
    <hr style="margin: 15px 0;">
    <p>
        <label><strong>📆 Visible desde:</strong></label><br>
        <input type="date" name="flavor_fecha_inicio" value="<?php echo esc_attr($fecha_inicio); ?>" style="width:100%">
        <br><small>Dejar vacío = visible desde hoy</small>
    </p>
    <p>
        <label><strong>📆 Visible hasta:</strong></label><br>
        <input type="date" name="flavor_fecha_fin" value="<?php echo esc_attr($fecha_fin); ?>" style="width:100%">
        <br><small>Dejar vacío = sin fecha límite</small>
    </p>
    <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 15px; border-left: 4px solid #ffc107;">
        <strong>💡 Ejemplo:</strong><br>
        Si pones "Visible hasta: 31/01/2026", el contenido desaparecerá automáticamente el 1 de febrero.
    </div>
    <?php
}

function flavor_save_meta($post_id) {
    if (!isset($_POST['flavor_nonce']) || !wp_verify_nonce($_POST['flavor_nonce'], 'flavor_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    // Precios
    $fields = array('flavor_precio', 'flavor_precio_oferta', 'flavor_duracion', 'flavor_fecha_inicio', 'flavor_fecha_fin');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
    }
    
    // Checkbox siempre visible
    update_post_meta($post_id, '_flavor_siempre_visible', isset($_POST['flavor_siempre_visible']) ? '1' : '0');
}
add_action('save_post', 'flavor_save_meta');

// ========== META BOX PARA ARCHIVOS DESCARGABLES ==========
function flavor_archivos_meta_box() {
    $post_types = array('paquete', 'oferta', 'salida_confirmada', 'evento_deportivo');
    add_meta_box(
        'flavor_archivos',
        '📎 Archivos Descargables',
        'flavor_archivos_cb',
        $post_types,
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'flavor_archivos_meta_box');

function flavor_archivos_cb($post) {
    wp_nonce_field('flavor_archivos_save', 'flavor_archivos_nonce');

    $archivo_id = get_post_meta($post->ID, '_flavor_archivo_id', true);
    $flyer_id = get_post_meta($post->ID, '_flavor_flyer_id', true);

    $archivo_url = $archivo_id ? wp_get_attachment_url($archivo_id) : '';
    $flyer_url = $flyer_id ? wp_get_attachment_image_url($flyer_id, 'thumbnail') : '';
    ?>

    <p>
        <label><strong>📄 Archivo (PDF/Word)</strong></label><br>
        <input type="hidden" name="flavor_archivo_id" id="flavor_archivo_id" value="<?php echo esc_attr($archivo_id); ?>">
        <button type="button" class="button" id="flavor_archivo_btn">Seleccionar Archivo</button>
        <button type="button" class="button" id="flavor_archivo_remove" style="<?php echo $archivo_id ? '' : 'display:none;'; ?>">Eliminar</button>
        <div id="flavor_archivo_preview" style="margin-top: 10px;">
            <?php if ($archivo_url): ?>
                <a href="<?php echo esc_url($archivo_url); ?>" target="_blank"><?php echo esc_html(basename($archivo_url)); ?></a>
            <?php endif; ?>
        </div>
    </p>

    <hr style="margin: 15px 0;">

    <p>
        <label><strong>🖼️ Flyer (Imagen)</strong></label><br>
        <input type="hidden" name="flavor_flyer_id" id="flavor_flyer_id" value="<?php echo esc_attr($flyer_id); ?>">
        <button type="button" class="button" id="flavor_flyer_btn">Seleccionar Flyer</button>
        <button type="button" class="button" id="flavor_flyer_remove" style="<?php echo $flyer_id ? '' : 'display:none;'; ?>">Eliminar</button>
        <div id="flavor_flyer_preview" style="margin-top: 10px;">
            <?php if ($flyer_url): ?>
                <img src="<?php echo esc_url($flyer_url); ?>" style="max-width: 150px; border-radius: 4px;">
            <?php endif; ?>
        </div>
    </p>

    <script>
    jQuery(document).ready(function($) {
        $('#flavor_archivo_btn').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Seleccionar Archivo',
                button: { text: 'Usar este archivo' },
                library: { type: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'] },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#flavor_archivo_id').val(attachment.id);
                $('#flavor_archivo_preview').html('<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>');
                $('#flavor_archivo_remove').show();
            });
            frame.open();
        });

        $('#flavor_archivo_remove').on('click', function() {
            $('#flavor_archivo_id').val('');
            $('#flavor_archivo_preview').html('');
            $(this).hide();
        });

        $('#flavor_flyer_btn').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Seleccionar Flyer',
                button: { text: 'Usar esta imagen' },
                library: { type: 'image' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#flavor_flyer_id').val(attachment.id);
                var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $('#flavor_flyer_preview').html('<img src="' + imgUrl + '" style="max-width: 150px; border-radius: 4px;">');
                $('#flavor_flyer_remove').show();
            });
            frame.open();
        });

        $('#flavor_flyer_remove').on('click', function() {
            $('#flavor_flyer_id').val('');
            $('#flavor_flyer_preview').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

function flavor_save_archivos_meta($post_id) {
    if (!isset($_POST['flavor_archivos_nonce']) || !wp_verify_nonce($_POST['flavor_archivos_nonce'], 'flavor_archivos_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['flavor_archivo_id'])) {
        update_post_meta($post_id, '_flavor_archivo_id', absint($_POST['flavor_archivo_id']));
    }
    if (isset($_POST['flavor_flyer_id'])) {
        update_post_meta($post_id, '_flavor_flyer_id', absint($_POST['flavor_flyer_id']));
    }
}
add_action('save_post', 'flavor_save_archivos_meta');

// ========== FILTRAR CONTENIDO POR VIGENCIA ==========
function flavor_filter_by_vigencia($query) {
    // Solo en frontend, no en admin
    if (is_admin()) return;
    
    // Solo para nuestros CPT
    if (!$query->is_main_query()) return;
    
    $post_types = array('destino', 'paquete', 'oferta', 'salida_confirmada', 'evento_deportivo');

    // Verificar si es archive o taxonomy de nuestros CPT
    $is_our_archive = false;
    if ($query->is_post_type_archive($post_types)) $is_our_archive = true;
    if ($query->is_tax('continente')) $is_our_archive = true;
    
    if (!$is_our_archive) return;
    
    $today = date('Y-m-d');
    
    // Meta query para filtrar por vigencia
    $meta_query = array(
        'relation' => 'OR',
        // Siempre visible
        array(
            'key' => '_flavor_siempre_visible',
            'value' => '1',
            'compare' => '='
        ),
        // Sin fechas configuradas (vacías)
        array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_inicio', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_inicio', 'value' => '', 'compare' => '='),
            ),
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_fin', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_fin', 'value' => '', 'compare' => '='),
            ),
        ),
        // Dentro del rango de fechas
        array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_inicio', 'value' => '', 'compare' => '='),
                array('key' => '_flavor_fecha_inicio', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_inicio', 'value' => $today, 'compare' => '<=', 'type' => 'DATE'),
            ),
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_fin', 'value' => '', 'compare' => '='),
                array('key' => '_flavor_fecha_fin', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_fin', 'value' => $today, 'compare' => '>=', 'type' => 'DATE'),
            ),
        ),
    );
    
    $query->set('meta_query', $meta_query);
}
add_action('pre_get_posts', 'flavor_filter_by_vigencia');

// ========== HELPER: VERIFICAR SI UN POST ESTÁ VIGENTE ==========
function flavor_is_vigente($post_id) {
    $siempre = get_post_meta($post_id, '_flavor_siempre_visible', true);
    if ($siempre == '1') return true;
    
    $fecha_inicio = get_post_meta($post_id, '_flavor_fecha_inicio', true);
    $fecha_fin = get_post_meta($post_id, '_flavor_fecha_fin', true);
    $today = date('Y-m-d');
    
    // Si no hay fechas, está vigente
    if (empty($fecha_inicio) && empty($fecha_fin)) return true;
    
    // Verificar inicio
    if (!empty($fecha_inicio) && $fecha_inicio > $today) return false;
    
    // Verificar fin
    if (!empty($fecha_fin) && $fecha_fin < $today) return false;
    
    return true;
}

// ========== COLUMNA EN ADMIN PARA VER VIGENCIA ==========
function flavor_admin_columns($columns) {
    $columns['vigencia'] = '📅 Vigencia';
    return $columns;
}
add_filter('manage_destino_posts_columns', 'flavor_admin_columns');
add_filter('manage_paquete_posts_columns', 'flavor_admin_columns');
add_filter('manage_oferta_posts_columns', 'flavor_admin_columns');
add_filter('manage_salida_confirmada_posts_columns', 'flavor_admin_columns');
add_filter('manage_evento_deportivo_posts_columns', 'flavor_admin_columns');

function flavor_admin_column_content($column, $post_id) {
    if ($column !== 'vigencia') return;
    
    $siempre = get_post_meta($post_id, '_flavor_siempre_visible', true);
    $fecha_inicio = get_post_meta($post_id, '_flavor_fecha_inicio', true);
    $fecha_fin = get_post_meta($post_id, '_flavor_fecha_fin', true);
    
    if ($siempre == '1') {
        echo '<span style="color: green;">✅ Siempre visible</span>';
        return;
    }
    
    if (empty($fecha_inicio) && empty($fecha_fin)) {
        echo '<span style="color: green;">✅ Sin límite</span>';
        return;
    }
    
    $vigente = flavor_is_vigente($post_id);
    
    if ($vigente) {
        echo '<span style="color: green;">✅ Visible</span><br>';
    } else {
        echo '<span style="color: red;">❌ No visible</span><br>';
    }
    
    if ($fecha_inicio) echo '<small>Desde: ' . date('d/m/Y', strtotime($fecha_inicio)) . '</small><br>';
    if ($fecha_fin) echo '<small>Hasta: ' . date('d/m/Y', strtotime($fecha_fin)) . '</small>';
}
add_action('manage_destino_posts_custom_column', 'flavor_admin_column_content', 10, 2);
add_action('manage_paquete_posts_custom_column', 'flavor_admin_column_content', 10, 2);
add_action('manage_oferta_posts_custom_column', 'flavor_admin_column_content', 10, 2);
add_action('manage_salida_confirmada_posts_custom_column', 'flavor_admin_column_content', 10, 2);
add_action('manage_evento_deportivo_posts_custom_column', 'flavor_admin_column_content', 10, 2);

// FLUSH REWRITE
function flavor_activate() {
    flavor_cpt();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flavor_activate');

// Flush rewrite rules cuando se guarda un tour con continente
function flavor_flush_on_tour_save($post_id, $post, $update) {
    if ($post->post_type === 'paquete') {
        // Marcar para flush en el próximo init
        set_transient('flavor_flush_rewrite', 1, 60);
    }
}
add_action('save_post', 'flavor_flush_on_tour_save', 10, 3);

// Ejecutar flush si está marcado
function flavor_maybe_flush_rewrite() {
    if (get_transient('flavor_flush_rewrite')) {
        delete_transient('flavor_flush_rewrite');
        flush_rewrite_rules();
    }
}
add_action('init', 'flavor_maybe_flush_rewrite', 99);

// ========== CUSTOMIZER - HOMEPAGE ==========
// REMOVED: flavor_customizer_home (duplicated)

// ========== CUSTOMIZER - HOME COMPLETO ==========
// REMOVED: flavor_customizer_home_complete (duplicated)

// ========== VISIBILIDAD DE SECCIONES ==========
function flavor_customizer_visibility($wp_customize) {
    
    $wp_customize->add_section('flavor_visibility', array(
        'title' => '👁️ Mostrar/Ocultar Secciones',
        'priority' => 25,
        'description' => 'Activa o desactiva las secciones de la página de inicio. Las secciones ocultas no se eliminan, solo se esconden temporalmente.',
    ));
    
    // Hero
    $wp_customize->add_setting('flavor_show_hero', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_hero', array('label' => '👁️ Mostrar Hero (Banner principal)', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // Continentes
    $wp_customize->add_setting('flavor_show_continentes', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_continentes', array('label' => '👁️ Mostrar Continentes', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // Ofertas
    $wp_customize->add_setting('flavor_show_ofertas', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_ofertas', array('label' => '👁️ Mostrar Ofertas', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // Badges
    $wp_customize->add_setting('flavor_show_badges', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_badges', array('label' => '👁️ Mostrar Badges de confianza', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // Tours
    $wp_customize->add_setting('flavor_show_tours', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_tours', array('label' => '👁️ Mostrar Tours', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // Destinos
    $wp_customize->add_setting('flavor_show_destinos', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_destinos', array('label' => '👁️ Mostrar Destinos', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // CTA
    $wp_customize->add_setting('flavor_show_cta', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_cta', array('label' => '👁️ Mostrar CTA (Llamada a acción)', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
    
    // Newsletter
    $wp_customize->add_setting('flavor_show_newsletter', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('flavor_show_newsletter', array('label' => '👁️ Mostrar Newsletter', 'section' => 'flavor_visibility', 'type' => 'checkbox'));
}
add_action('customize_register', 'flavor_customizer_visibility');


// ========== HERO CAROUSEL ==========
function flavor_customizer_hero_carousel($wp_customize) {
    
    // Quitar el campo anterior de imagen única si existe
    $wp_customize->remove_setting('flavor_hero_image');
    $wp_customize->remove_control('flavor_hero_image');
    
    // Imagen 1
    $wp_customize->add_setting('flavor_hero_image_1', array(
        'default' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('flavor_hero_image_1', array(
        'label' => '🖼️ Imagen 1 (Principal)',
        'description' => 'Siempre visible. Pega la URL de la imagen.',
        'section' => 'flavor_hero',
        'type' => 'url',
        'priority' => 20
    ));
    
    // Imagen 2
    $wp_customize->add_setting('flavor_hero_image_2', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('flavor_hero_image_2', array(
        'label' => '🖼️ Imagen 2 (Opcional)',
        'description' => 'Dejar vacío si no quieres carrusel.',
        'section' => 'flavor_hero',
        'type' => 'url',
        'priority' => 21
    ));
    
    // Imagen 3
    $wp_customize->add_setting('flavor_hero_image_3', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('flavor_hero_image_3', array(
        'label' => '🖼️ Imagen 3 (Opcional)',
        'description' => 'Dejar vacío si solo quieres 2 imágenes.',
        'section' => 'flavor_hero',
        'type' => 'url',
        'priority' => 22
    ));
    
    // Velocidad del carrusel
    $wp_customize->add_setting('flavor_hero_speed', array(
        'default' => '5',
        'sanitize_callback' => 'absint'
    ));
    $wp_customize->add_control('flavor_hero_speed', array(
        'label' => '⏱️ Velocidad del carrusel (segundos)',
        'description' => 'Tiempo entre cada imagen.',
        'section' => 'flavor_hero',
        'type' => 'number',
        'input_attrs' => array('min' => 3, 'max' => 15),
        'priority' => 23
    ));
    
    // URLs de botones
    $wp_customize->add_setting('flavor_hero_btn1_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('flavor_hero_btn1_url', array(
        'label' => '🔗 URL Botón 1',
        'description' => 'Dejar vacío para ir a Destinos.',
        'section' => 'flavor_hero',
        'type' => 'url',
        'priority' => 30
    ));
    
    $wp_customize->add_setting('flavor_hero_btn2_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('flavor_hero_btn2_url', array(
        'label' => '🔗 URL Botón 2',
        'description' => 'Dejar vacío para ir a Ofertas.',
        'section' => 'flavor_hero',
        'type' => 'url',
        'priority' => 31
    ));
}
add_action('customize_register', 'flavor_customizer_hero_carousel');

// ========== NO CACHE HEADERS ==========
function flavor_no_cache_headers() {
    if (!is_admin()) {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}
add_action('send_headers', 'flavor_no_cache_headers');

// Desactivar caché de recursos
function flavor_disable_resource_cache($src) {
    if (strpos($src, get_template_directory_uri()) !== false) {
        $src = add_query_arg('ver', time(), $src);
    }
    return $src;
}
add_filter('style_loader_src', 'flavor_disable_resource_cache', 10, 1);
add_filter('script_loader_src', 'flavor_disable_resource_cache', 10, 1);



// Agregar página de opciones del tema
function flavor_add_theme_options_page() {
    add_theme_page(
        'Opciones Flavor Travel',
        '⚙️ Opciones del Tema',
        'manage_options',
        'flavor-options',
        'flavor_options_page_html'
    );
}
add_action('admin_menu', 'flavor_add_theme_options_page');

function flavor_options_page_html() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>⚙️ Opciones Flavor Travel</h1>
        
        <div style="background: white; padding: 30px; border-radius: 8px; margin-top: 20px; max-width: 600px;">
            <h2 style="margin-top: 0;">🎨 Personalización</h2>
            <p>Configura los banners, colores, textos y contenido del sitio:</p>
            <p>
                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary" style="margin-right: 10px;">
                    🎨 Abrir Personalizador
                </a>
            </p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 8px; margin-top: 20px; max-width: 600px;">
            <h2 style="margin-top: 0;">📝 Crear Contenido</h2>
            <p>Agrega destinos, tours y ofertas:</p>
            <p style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="<?php echo admin_url('post-new.php?post_type=destino'); ?>" class="button">➕ Nuevo Destino</a>
                <a href="<?php echo admin_url('post-new.php?post_type=paquete'); ?>" class="button">➕ Nuevo Tour</a>
                <a href="<?php echo admin_url('post-new.php?post_type=oferta'); ?>" class="button">➕ Nueva Oferta</a>
            </p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 8px; margin-top: 20px; max-width: 600px;">
            <h2 style="margin-top: 0;">🗺️ Continentes</h2>
            <p>Administra los continentes para organizar tus destinos:</p>
            <p>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=continente&post_type=destino'); ?>" class="button">
                    🌍 Gestionar Continentes
                </a>
            </p>
        </div>
    </div>
    <?php
}

// ========== SEO PROFESIONAL ==========
function flavor_seo_customizer($wp_customize) {
    
    // SECCIÓN SEO
    $wp_customize->add_section('flavor_seo', array(
        'title' => '🔍 SEO',
        'priority' => 25,
    ));
    
    // Meta título
    $wp_customize->add_setting('flavor_seo_title', array(
        'default' => 'Agencia de Viajes | Tours y Paquetes Turísticos',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('flavor_seo_title', array(
        'label' => 'Título SEO (máx 60 caracteres)',
        'section' => 'flavor_seo',
        'type' => 'text'
    ));
    
    // Meta descripción
    $wp_customize->add_setting('flavor_seo_description', array(
        'default' => 'Descubre los mejores destinos del mundo con nuestra agencia de viajes. Tours a Machu Picchu, Europa, Asia y más. ¡Reserva ahora y vive la aventura!',
        'sanitize_callback' => 'sanitize_textarea_field'
    ));
    $wp_customize->add_control('flavor_seo_description', array(
        'label' => 'Meta Descripción (máx 160 caracteres)',
        'section' => 'flavor_seo',
        'type' => 'textarea'
    ));
    
    // Palabras clave
    $wp_customize->add_setting('flavor_seo_keywords', array(
        'default' => 'agencia de viajes, tours, paquetes turísticos, viajes a Machu Picchu, viajes a Europa, turismo',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('flavor_seo_keywords', array(
        'label' => 'Palabras clave (separadas por coma)',
        'section' => 'flavor_seo',
        'type' => 'textarea'
    ));
    
    // Nombre del negocio
    $wp_customize->add_setting('flavor_seo_business_name', array(
        'default' => 'Tu Agencia de Viajes',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('flavor_seo_business_name', array(
        'label' => 'Nombre del Negocio',
        'section' => 'flavor_seo'
    ));
    
    // Tipo de negocio
    $wp_customize->add_setting('flavor_seo_business_type', array(
        'default' => 'TravelAgency',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('flavor_seo_business_type', array(
        'label' => 'Tipo de Negocio (Schema.org)',
        'section' => 'flavor_seo',
        'type' => 'select',
        'choices' => array(
            'TravelAgency' => 'Agencia de Viajes',
            'TouristInformationCenter' => 'Centro de Información Turística',
            'LocalBusiness' => 'Negocio Local',
        )
    ));
    
    // Dirección
    $wp_customize->add_setting('flavor_seo_address', array(
        'default' => 'Lima, Perú',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('flavor_seo_address', array(
        'label' => 'Dirección',
        'section' => 'flavor_seo'
    ));
    
    // Email
    $wp_customize->add_setting('flavor_seo_email', array(
        'default' => 'info@tusitio.com',
        'sanitize_callback' => 'sanitize_email'
    ));
    $wp_customize->add_control('flavor_seo_email', array(
        'label' => 'Email de contacto',
        'section' => 'flavor_seo',
        'type' => 'email'
    ));
    
    // Redes sociales
    $wp_customize->add_setting('flavor_seo_facebook', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_seo_facebook', array('label' => 'Facebook URL', 'section' => 'flavor_seo', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_seo_instagram', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_seo_instagram', array('label' => 'Instagram URL', 'section' => 'flavor_seo', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_seo_youtube', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_seo_youtube', array('label' => 'YouTube URL', 'section' => 'flavor_seo', 'type' => 'url'));
    
    $wp_customize->add_setting('flavor_seo_tiktok', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_seo_tiktok', array('label' => 'TikTok URL', 'section' => 'flavor_seo', 'type' => 'url'));
    
    // Imagen OG
    $wp_customize->add_setting('flavor_seo_og_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('flavor_seo_og_image', array(
        'label' => 'Imagen para redes sociales (1200x630px)',
        'section' => 'flavor_seo',
        'type' => 'url',
        'description' => 'Se muestra al compartir en Facebook, WhatsApp, etc.'
    ));
    
    // Google verification
    $wp_customize->add_setting('flavor_seo_google_verification', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('flavor_seo_google_verification', array(
        'label' => 'Google Site Verification',
        'section' => 'flavor_seo',
        'description' => 'Código de verificación de Google Search Console'
    ));
}
add_action('customize_register', 'flavor_seo_customizer');

// Agregar meta tags al head
function flavor_seo_head() {
    $site_name = get_theme_mod('flavor_seo_business_name', get_bloginfo('name'));
    $title = get_theme_mod('flavor_seo_title', 'Agencia de Viajes');
    $description = get_theme_mod('flavor_seo_description', '');
    $keywords = get_theme_mod('flavor_seo_keywords', '');
    $og_image = get_theme_mod('flavor_seo_og_image', '');
    $google_verification = get_theme_mod('flavor_seo_google_verification', '');
    
    // Para páginas internas, usar título del post
    if (!is_front_page() && !is_home()) {
        if (is_singular()) {
            $title = get_the_title() . ' | ' . $site_name;
            $description = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25);
            if (has_post_thumbnail()) {
                $og_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
            }
        } elseif (is_tax('continente')) {
            $term = get_queried_object();
            $title = $term->name . ' - Destinos | ' . $site_name;
            $description = $term->description ?: 'Descubre los mejores destinos en ' . $term->name;
            $term_img = get_term_meta($term->term_id, 'continente_imagen', true);
            if ($term_img) $og_image = $term_img;
        } elseif (is_post_type_archive('destino')) {
            $title = 'Destinos | ' . $site_name;
            $description = get_theme_mod('flavor_destinos_desc', 'Explora nuestros destinos');
        } elseif (is_post_type_archive('paquete')) {
            $title = 'Tours y Paquetes | ' . $site_name;
            $description = get_theme_mod('flavor_tours_desc', 'Descubre nuestros tours');
        } elseif (is_post_type_archive('oferta')) {
            $title = 'Ofertas Especiales | ' . $site_name;
            $description = get_theme_mod('flavor_ofertas_desc', 'Aprovecha nuestras ofertas');
        }
    }
    
    $url = home_url($_SERVER['REQUEST_URI']);
    ?>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    <meta name="keywords" content="<?php echo esc_attr($keywords); ?>">
    <meta name="author" content="<?php echo esc_attr($site_name); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="<?php echo esc_url($url); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <?php if ($og_image): ?>
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <?php if ($og_image): ?>
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    <?php endif; ?>
    
    <!-- Google Verification -->
    <?php if ($google_verification): ?>
    <meta name="google-site-verification" content="<?php echo esc_attr($google_verification); ?>">
    <?php endif; ?>
    
    <!-- Geo Tags -->
    <meta name="geo.region" content="PE">
    <meta name="geo.placename" content="<?php echo esc_attr(get_theme_mod('flavor_seo_address', 'Lima, Perú')); ?>">
    
    <?php
}
add_action('wp_head', 'flavor_seo_head', 1);

// Schema.org JSON-LD
function flavor_schema_jsonld() {
    $site_name = get_theme_mod('flavor_seo_business_name', get_bloginfo('name'));
    $description = get_theme_mod('flavor_seo_description', '');
    $business_type = get_theme_mod('flavor_seo_business_type', 'TravelAgency');
    $address = get_theme_mod('flavor_seo_address', 'Lima, Perú');
    $phone = get_theme_mod('flavor_phone', '');
    $email = get_theme_mod('flavor_seo_email', '');
    $og_image = get_theme_mod('flavor_seo_og_image', '');
    
    // Redes sociales
    $social = array();
    if (get_theme_mod('flavor_seo_facebook')) $social[] = get_theme_mod('flavor_seo_facebook');
    if (get_theme_mod('flavor_seo_instagram')) $social[] = get_theme_mod('flavor_seo_instagram');
    if (get_theme_mod('flavor_seo_youtube')) $social[] = get_theme_mod('flavor_seo_youtube');
    if (get_theme_mod('flavor_seo_tiktok')) $social[] = get_theme_mod('flavor_seo_tiktok');
    
    // Schema Organization / TravelAgency
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => $business_type,
        'name' => $site_name,
        'description' => $description,
        'url' => home_url('/'),
        'telephone' => $phone,
        'email' => $email,
        'address' => array(
            '@type' => 'PostalAddress',
            'addressLocality' => $address,
            'addressCountry' => 'PE'
        ),
        'priceRange' => '$$',
    );
    
    if ($og_image) {
        $schema['image'] = $og_image;
        $schema['logo'] = $og_image;
    }
    
    if (!empty($social)) {
        $schema['sameAs'] = $social;
    }
    
    // Agregar servicios
    $schema['hasOfferCatalog'] = array(
        '@type' => 'OfferCatalog',
        'name' => 'Tours y Paquetes Turísticos',
        'itemListElement' => array(
            array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Tours Nacionales')),
            array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Tours Internacionales')),
            array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Paquetes Todo Incluido')),
        )
    );
    
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
    
    // Schema WebSite con SearchAction
    $website_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $site_name,
        'url' => home_url('/'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string'
        )
    );
    echo '<script type="application/ld+json">' . json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    
    // Schema BreadcrumbList para páginas internas
    if (!is_front_page()) {
        $breadcrumbs = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array()
        );
        
        $position = 1;
        $breadcrumbs['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Inicio',
            'item' => home_url('/')
        );
        
        if (is_post_type_archive('destino')) {
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => 'Destinos',
                'item' => get_post_type_archive_link('destino')
            );
        } elseif (is_post_type_archive('paquete')) {
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => 'Tours',
                'item' => get_post_type_archive_link('paquete')
            );
        } elseif (is_tax('continente')) {
            $term = get_queried_object();
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'Destinos',
                'item' => get_post_type_archive_link('destino')
            );
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $term->name,
                'item' => get_term_link($term)
            );
        } elseif (is_singular()) {
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title(),
                'item' => get_permalink()
            );
        }
        
        echo '<script type="application/ld+json">' . json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'flavor_schema_jsonld', 2);

// Modificar el título del documento
function flavor_document_title($title) {
    if (is_front_page() || is_home()) {
        return get_theme_mod('flavor_seo_title', get_bloginfo('name'));
    }
    return $title;
}
add_filter('pre_get_document_title', 'flavor_document_title');

// ========== HELPER: META QUERY PARA VIGENCIA ==========
function flavor_get_vigencia_meta_query() {
    $today = date('Y-m-d');
    
    return array(
        'relation' => 'OR',
        // Siempre visible
        array(
            'key' => '_flavor_siempre_visible',
            'value' => '1',
            'compare' => '='
        ),
        // Sin fechas configuradas
        array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_inicio', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_inicio', 'value' => '', 'compare' => '='),
            ),
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_fin', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_fin', 'value' => '', 'compare' => '='),
            ),
        ),
        // Dentro del rango de fechas
        array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_inicio', 'value' => '', 'compare' => '='),
                array('key' => '_flavor_fecha_inicio', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_inicio', 'value' => $today, 'compare' => '<=', 'type' => 'DATE'),
            ),
            array(
                'relation' => 'OR',
                array('key' => '_flavor_fecha_fin', 'value' => '', 'compare' => '='),
                array('key' => '_flavor_fecha_fin', 'compare' => 'NOT EXISTS'),
                array('key' => '_flavor_fecha_fin', 'value' => $today, 'compare' => '>=', 'type' => 'DATE'),
            ),
        ),
    );
}


// ========== BLOQUE ABOUT (OPCIONAL) ==========
// ========== OBTENER ITEMS CON DESCUENTO (OFERTAS AUTOMÁTICAS) ==========
function flavor_get_items_con_descuento($limit = 8) {
    global $wpdb;
    
    $today = current_time('Y-m-d');
    
    // Query para obtener posts de cualquier CPT que tengan precio_oferta
    $query = $wpdb->prepare("
        SELECT DISTINCT p.ID, p.post_type, pm_oferta.meta_value as precio_oferta, pm_precio.meta_value as precio
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_oferta ON p.ID = pm_oferta.post_id AND pm_oferta.meta_key = '_flavor_precio_oferta'
        INNER JOIN {$wpdb->postmeta} pm_precio ON p.ID = pm_precio.post_id AND pm_precio.meta_key = '_flavor_precio'
        LEFT JOIN {$wpdb->postmeta} pm_siempre ON p.ID = pm_siempre.post_id AND pm_siempre.meta_key = '_flavor_siempre_visible'
        LEFT JOIN {$wpdb->postmeta} pm_inicio ON p.ID = pm_inicio.post_id AND pm_inicio.meta_key = '_flavor_fecha_inicio'
        LEFT JOIN {$wpdb->postmeta} pm_fin ON p.ID = pm_fin.post_id AND pm_fin.meta_key = '_flavor_fecha_fin'
        WHERE p.post_type IN ('paquete', 'oferta', 'salida_confirmada', 'evento_deportivo')
        AND p.post_status = 'publish'
        AND pm_oferta.meta_value != ''
        AND pm_oferta.meta_value > 0
        AND CAST(pm_oferta.meta_value AS DECIMAL(10,2)) < CAST(pm_precio.meta_value AS DECIMAL(10,2))
        AND (
            pm_siempre.meta_value = '1'
            OR (
                (pm_inicio.meta_value IS NULL OR pm_inicio.meta_value = '' OR pm_inicio.meta_value <= %s)
                AND (pm_fin.meta_value IS NULL OR pm_fin.meta_value = '' OR pm_fin.meta_value >= %s)
            )
        )
        ORDER BY (1 - (CAST(pm_oferta.meta_value AS DECIMAL(10,2)) / CAST(pm_precio.meta_value AS DECIMAL(10,2)))) DESC
        LIMIT %d
    ", $today, $today, $limit);
    
    $results = $wpdb->get_results($query);
    
    if (empty($results)) {
        return array();
    }
    
    $post_ids = wp_list_pluck($results, 'ID');
    
    return get_posts(array(
        'post_type' => array('destino', 'paquete', 'oferta'),
        'post__in' => $post_ids,
        'orderby' => 'post__in',
        'posts_per_page' => $limit,
    ));
}

// Calcular porcentaje de descuento
function flavor_get_discount_percent($post_id) {
    $precio = floatval(get_post_meta($post_id, '_flavor_precio', true));
    $precio_oferta = floatval(get_post_meta($post_id, '_flavor_precio_oferta', true));
    
    if ($precio > 0 && $precio_oferta > 0 && $precio > $precio_oferta) {
        return round((($precio - $precio_oferta) / $precio) * 100);
    }
    return 0;
}

// ========== BUSCADOR AJAX ==========
function flavor_ajax_search() {
    $query = sanitize_text_field($_GET['q']);
    $continente_slug = isset($_GET['continente']) ? sanitize_text_field($_GET['continente']) : '';

    if (strlen($query) < 2) {
        wp_send_json(array());
        return;
    }

    $args = array(
        'post_type' => array('destino', 'paquete', 'oferta', 'salida_confirmada', 'evento_deportivo'),
        'posts_per_page' => 8,
        's' => $query,
        'post_status' => 'publish',
    );

    // Filtrar por continente si se especifica
    if (!empty($continente_slug)) {
        $args['post_type'] = array('paquete', 'oferta', 'salida_confirmada', 'evento_deportivo');

        // Obtener el término y sus hijos (países)
        $continente_term = get_term_by('slug', $continente_slug, 'continente');
        if ($continente_term) {
            $term_ids = array($continente_term->term_id);

            // Incluir subcategorías (países)
            $children = get_terms(array(
                'taxonomy' => 'continente',
                'parent' => $continente_term->term_id,
                'hide_empty' => false,
                'fields' => 'ids',
            ));
            if (!is_wp_error($children)) {
                $term_ids = array_merge($term_ids, $children);
            }

            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'continente',
                    'field' => 'term_id',
                    'terms' => $term_ids,
                ),
            );
        }
    }

    $posts = get_posts($args);
    $results = array();
    
    foreach ($posts as $post) {
        $img = get_the_post_thumbnail_url($post->ID, 'thumbnail');
        if (!$img) $img = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=150&q=60';
        
        $precio = get_post_meta($post->ID, '_flavor_precio', true);
        $precio_oferta = get_post_meta($post->ID, '_flavor_precio_oferta', true);
        
        $pais = get_the_terms($post->ID, 'pais');
        $continente = get_the_terms($post->ID, 'continente');
        
        $location = '';
        if ($pais && !is_wp_error($pais)) {
            $location = $pais[0]->name;
        } elseif ($continente && !is_wp_error($continente)) {
            $location = $continente[0]->name;
        }
        
        $type_labels = array(
            'destino' => 'Destino',
            'paquete' => 'Tour',
            'oferta' => 'Oferta',
            'salida_confirmada' => 'Salida',
            'evento_deportivo' => 'Evento',
        );
        
        $results[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'url' => get_permalink($post->ID),
            'image' => $img,
            'price' => $precio_oferta ?: $precio,
            'location' => $location,
            'type' => $type_labels[$post->post_type] ?? 'Destino',
        );
    }
    
    wp_send_json($results);
}
add_action('wp_ajax_flavor_search', 'flavor_ajax_search');
add_action('wp_ajax_nopriv_flavor_search', 'flavor_ajax_search');

// Pasar URL de AJAX al frontend
function flavor_search_scripts() {
    if (is_front_page()) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('hero-search-input');
            const results = document.getElementById('hero-search-results');
            let timeout;
            
            if (!input || !results) return;
            
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    results.classList.remove('active');
                    return;
                }
                
                timeout = setTimeout(function() {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=flavor_search&q=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                results.innerHTML = '<div class="search-no-results">No se encontraron resultados para "<strong>' + query + '</strong>"</div>';
                                results.classList.add('active');
                                return;
                            }
                            
                            let html = '';
                            data.forEach(item => {
                                html += '<a href="' + item.url + '" class="search-result-item">';
                                html += '<img src="' + item.image + '" class="search-result-img" alt="">';
                                html += '<div class="search-result-info">';
                                html += '<div class="search-result-title">' + item.title + '</div>';
                                html += '<div class="search-result-meta">' + item.type + (item.location ? ' • ' + item.location : '') + '</div>';
                                html += '</div>';
                                if (item.price) {
                                    html += '<div class="search-result-price">$' + Number(item.price).toLocaleString() + '</div>';
                                }
                                html += '</a>';
                            });
                            
                            results.innerHTML = html;
                            results.classList.add('active');
                        });
                }, 300);
            });
            
            // Cerrar al hacer click fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.hero-search')) {
                    results.classList.remove('active');
                }
            });
            
            // Cerrar al presionar Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    results.classList.remove('active');
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'flavor_search_scripts');

// Incluir CPTs en la búsqueda de WordPress
function flavor_search_filter($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        // Si viene del formulario de búsqueda con post_type específico
        if (isset($_GET['post_type'])) {
            $post_types = explode(',', sanitize_text_field($_GET['post_type']));
            $query->set('post_type', $post_types);
        } else {
            // Por defecto buscar en destinos, tours y ofertas
            $query->set('post_type', array('destino', 'paquete', 'oferta', 'post', 'page'));
        }
    }
    return $query;
}
add_action('pre_get_posts', 'flavor_search_filter');

// ========== REDIRECT DE URLs LEGACY ==========
// Redirigir URLs con query string ?destino=slug a su permalink limpio
function flavor_redirect_legacy_urls() {
    // Redirect ?destino=slug
    if (isset($_GET['destino']) && !empty($_GET['destino'])) {
        $slug = sanitize_title($_GET['destino']);
        $post = get_page_by_path($slug, OBJECT, 'destino');
        if ($post) {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }

    // Redirect ?paquete=slug
    if (isset($_GET['paquete']) && !empty($_GET['paquete'])) {
        $slug = sanitize_title($_GET['paquete']);
        $post = get_page_by_path($slug, OBJECT, 'paquete');
        if ($post) {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }

    // Redirect ?oferta=slug
    if (isset($_GET['oferta']) && !empty($_GET['oferta'])) {
        $slug = sanitize_title($_GET['oferta']);
        $post = get_page_by_path($slug, OBJECT, 'oferta');
        if ($post) {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }

    // Redirect ?salida_confirmada=slug
    if (isset($_GET['salida_confirmada']) && !empty($_GET['salida_confirmada'])) {
        $slug = sanitize_title($_GET['salida_confirmada']);
        $post = get_page_by_path($slug, OBJECT, 'salida_confirmada');
        if ($post) {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }

    // Redirect ?evento_deportivo=slug
    if (isset($_GET['evento_deportivo']) && !empty($_GET['evento_deportivo'])) {
        $slug = sanitize_title($_GET['evento_deportivo']);
        $post = get_page_by_path($slug, OBJECT, 'evento_deportivo');
        if ($post) {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }
}
add_action('template_redirect', 'flavor_redirect_legacy_urls', 1);

// ========== SISTEMA DE SUSCRIPTORES ==========

// Crear tabla de suscriptores al activar el tema
function flavor_create_subscribers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'flavor_subscribers';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        subscribed_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        ip_address varchar(45),
        status varchar(20) DEFAULT 'active',
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'flavor_create_subscribers_table');

// También verificar en admin_init por si la tabla no existe
function flavor_check_subscribers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'flavor_subscribers';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        flavor_create_subscribers_table();
    }
}
add_action('admin_init', 'flavor_check_subscribers_table');

// AJAX para guardar suscriptor
function flavor_subscribe_ajax() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'flavor_subscribers';
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Por favor ingresa un correo válido.'));
        return;
    }
    
    // Verificar si ya existe
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE email = %s",
        $email
    ));
    
    if ($exists) {
        wp_send_json_error(array('message' => 'Este correo ya está suscrito.'));
        return;
    }
    
    // Insertar nuevo suscriptor
    $result = $wpdb->insert(
        $table_name,
        array(
            'email' => $email,
            'subscribed_at' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'status' => 'active'
        ),
        array('%s', '%s', '%s', '%s')
    );
    
    if ($result) {
        wp_send_json_success(array('message' => '¡Gracias por suscribirte! 🎉'));
    } else {
        wp_send_json_error(array('message' => 'Ocurrió un error. Intenta de nuevo.'));
    }
}
add_action('wp_ajax_flavor_subscribe', 'flavor_subscribe_ajax');
add_action('wp_ajax_nopriv_flavor_subscribe', 'flavor_subscribe_ajax');

// Agregar menú en el admin
function flavor_subscribers_menu() {
    add_menu_page(
        'Suscriptores',
        '📧 Suscriptores',
        'manage_options',
        'flavor-subscribers',
        'flavor_subscribers_page',
        'dashicons-email-alt',
        30
    );
}
add_action('admin_menu', 'flavor_subscribers_menu');

// Página de suscriptores
function flavor_subscribers_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'flavor_subscribers';
    
    // Manejar acciones
    if (isset($_GET['action']) && isset($_GET['id']) && wp_verify_nonce($_GET['_wpnonce'], 'subscriber_action')) {
        $id = intval($_GET['id']);
        
        if ($_GET['action'] === 'delete') {
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            echo '<div class="notice notice-success"><p>Suscriptor eliminado.</p></div>';
        } elseif ($_GET['action'] === 'toggle') {
            $current = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table_name WHERE id = %d", $id));
            $new_status = ($current === 'active') ? 'inactive' : 'active';
            $wpdb->update($table_name, array('status' => $new_status), array('id' => $id));
            echo '<div class="notice notice-success"><p>Estado actualizado.</p></div>';
        }
    }
    
    // Exportar CSV
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        $subscribers = $wpdb->get_results("SELECT email, subscribed_at, status FROM $table_name ORDER BY subscribed_at DESC");
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=suscriptores-' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Email', 'Fecha de suscripción', 'Estado'));
        
        foreach ($subscribers as $sub) {
            fputcsv($output, array($sub->email, $sub->subscribed_at, $sub->status));
        }
        
        fclose($output);
        exit;
    }
    
    // Obtener suscriptores
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY subscribed_at DESC");
    $total = count($subscribers);
    $active = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'active'");
    ?>
    <div class="wrap">
        <h1>📧 Suscriptores del Newsletter</h1>
        
        <!-- Estadísticas -->
        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <div style="background: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; font-weight: 700; color: #2563eb;"><?php echo $total; ?></div>
                <div style="color: #64748b;">Total suscriptores</div>
            </div>
            <div style="background: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; font-weight: 700; color: #059669;"><?php echo $active; ?></div>
                <div style="color: #64748b;">Activos</div>
            </div>
            <div style="background: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; font-weight: 700; color: #dc2626;"><?php echo $total - $active; ?></div>
                <div style="color: #64748b;">Inactivos</div>
            </div>
        </div>
        
        <!-- Botón exportar -->
        <p>
            <a href="<?php echo admin_url('admin.php?page=flavor-subscribers&export=csv'); ?>" class="button button-primary">
                📥 Exportar CSV
            </a>
        </p>
        
        <!-- Tabla -->
        <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Email</th>
                    <th style="width: 180px;">Fecha de suscripción</th>
                    <th style="width: 100px;">Estado</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($subscribers): ?>
                    <?php foreach ($subscribers as $index => $sub): ?>
                    <tr>
                        <td><?php echo $sub->id; ?></td>
                        <td><strong><?php echo esc_html($sub->email); ?></strong></td>
                        <td>
                            <?php 
                            $date = new DateTime($sub->subscribed_at);
                            echo $date->format('d/m/Y H:i'); 
                            ?>
                        </td>
                        <td>
                            <?php if ($sub->status === 'active'): ?>
                                <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 12px;">Activo</span>
                            <?php else: ?>
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 12px;">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=flavor-subscribers&action=toggle&id=' . $sub->id), 'subscriber_action'); ?>" class="button button-small">
                                <?php echo $sub->status === 'active' ? 'Desactivar' : 'Activar'; ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=flavor-subscribers&action=delete&id=' . $sub->id), 'subscriber_action'); ?>" class="button button-small" style="color: #dc2626;" onclick="return confirm('¿Eliminar este suscriptor?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            <p style="color: #64748b;">Aún no hay suscriptores.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Script para el formulario del newsletter
function flavor_newsletter_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('newsletter-form');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = form.querySelector('input[type="email"]');
            const submitBtn = form.querySelector('button[type="submit"]');
            const email = emailInput.value.trim();
            const originalText = submitBtn.innerHTML;
            
            if (!email) return;
            
            submitBtn.innerHTML = 'Enviando...';
            submitBtn.disabled = true;
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=flavor_subscribe&email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    form.innerHTML = '<p style="color: #10b981; font-weight: 500;">' + data.data.message + '</p>';
                } else {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    alert(data.data.message);
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert('Error de conexión. Intenta de nuevo.');
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'flavor_newsletter_script');

// ========== REDES SOCIALES ==========
function flavor_social_customizer($wp_customize) {
    
    $wp_customize->add_section('flavor_social', array(
        'title' => '📱 Redes Sociales',
        'priority' => 35,
    ));
    
    // Redes disponibles
    $social_networks = array(
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
        'youtube' => 'YouTube',
        'twitter' => 'X (Twitter)',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'whatsapp_social' => 'WhatsApp (enlace directo)',
        'telegram' => 'Telegram',
        'tripadvisor' => 'TripAdvisor',
    );
    
    foreach ($social_networks as $key => $label) {
        $wp_customize->add_setting("flavor_social_$key", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("flavor_social_$key", array(
            'label' => $label,
            'description' => "URL completa de $label",
            'section' => 'flavor_social',
            'type' => 'url',
        ));
    }
    
    // Posición de los iconos
    $wp_customize->add_setting('flavor_social_position', array(
        'default' => 'footer',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('flavor_social_position', array(
        'label' => 'Posición de iconos',
        'section' => 'flavor_social',
        'type' => 'select',
        'choices' => array(
            'footer' => 'Solo en el footer',
            'floating' => 'Barra flotante lateral + footer',
            'both' => 'Header + footer',
        ),
    ));
}
add_action('customize_register', 'flavor_social_customizer');

// Obtener redes sociales activas
function flavor_get_social_links() {
    $networks = array(
        'facebook' => array('icon' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>', 'color' => '#1877F2'),
        'instagram' => array('icon' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>', 'color' => '#E4405F'),
        'tiktok' => array('icon' => '<path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>', 'color' => '#000000'),
        'youtube' => array('icon' => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>', 'color' => '#FF0000'),
        'twitter' => array('icon' => '<path d="M4 4l11.733 16h4.267l-11.733 -16z"/><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"/>', 'color' => '#000000'),
        'linkedin' => array('icon' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>', 'color' => '#0A66C2'),
        'pinterest' => array('icon' => '<circle cx="12" cy="12" r="10"/><path d="M8 12c0-2.5 2-4.5 4-4.5s4 2 4 4.5c0 2.5-2 4.5-4 4.5"/><line x1="12" y1="12" x2="12" y2="21"/>', 'color' => '#E60023'),
        'whatsapp_social' => array('icon' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>', 'color' => '#25D366'),
        'telegram' => array('icon' => '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>', 'color' => '#0088CC'),
        'tripadvisor' => array('icon' => '<circle cx="6.5" cy="13.5" r="2.5"/><circle cx="17.5" cy="13.5" r="2.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2z"/><circle cx="12" cy="8" r="2"/>', 'color' => '#34E0A1'),
    );
    
    $active = array();
    foreach ($networks as $key => $data) {
        $url = get_theme_mod("flavor_social_$key", '');
        if (!empty($url)) {
            $active[$key] = array(
                'url' => $url,
                'icon' => $data['icon'],
                'color' => $data['color'],
                'name' => ucfirst(str_replace('_social', '', $key)),
            );
        }
    }
    
    return $active;
}

// Renderizar iconos de redes sociales
function flavor_render_social_icons($style = 'default') {
    $links = flavor_get_social_links();
    if (empty($links)) return '';
    
    $html = '<div class="social-icons social-icons--' . $style . '">';
    foreach ($links as $key => $data) {
        $html .= '<a href="' . esc_url($data['url']) . '" target="_blank" rel="noopener noreferrer" class="social-icon social-icon--' . $key . '" title="' . $data['name'] . '">';
        $html .= '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $data['icon'] . '</svg>';
        $html .= '</a>';
    }
    $html .= '</div>';
    
    return $html;
}

// Barra flotante de redes sociales
function flavor_floating_social() {
    $position = get_theme_mod('flavor_social_position', 'footer');
    if ($position !== 'floating') return;
    
    $links = flavor_get_social_links();
    if (empty($links)) return;
    ?>
    <style>
    .floating-social {
        position: fixed;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 999;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .floating-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: #1f2937;
        color: white;
        transition: all 0.3s;
        text-decoration: none;
    }
    .floating-social a:hover {
        width: 54px;
        background: #2563eb;
    }
    .floating-social a:first-child {
        border-radius: 0 8px 0 0;
    }
    .floating-social a:last-child {
        border-radius: 0 0 8px 0;
    }
    @media (max-width: 768px) {
        .floating-social {
            display: none;
        }
    }
    </style>
    <div class="floating-social">
        <?php foreach ($links as $key => $data): ?>
        <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo $data['name']; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo ($key === 'whatsapp_social' || $key === 'youtube') ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $data['icon']; ?></svg>
        </a>
        <?php endforeach; ?>
    </div>
    <?php
}
add_action('wp_footer', 'flavor_floating_social');

// ========== RENDERIZAR BLOQUE SEO CON IMAGEN ==========
function flavor_render_seo_block($text, $image_id, $layout = 'text-only') {
    if (empty($text) && empty($image_id)) return;
    
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    
    ?>
    <section class="seo-block seo-block--<?php echo esc_attr($layout); ?>" style="padding: 60px 0; background: #f8fafc;">
        <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
            <?php if ($layout === 'text-only' || empty($image_url)): ?>
                <!-- Solo texto -->
                <div style="text-align: center;">
                    <div style="color: #4b5563; line-height: 1.9; font-size: 1rem;"><?php echo wp_kses_post($text); ?></div>
                </div>
            
            <?php elseif ($layout === 'image-left'): ?>
                <!-- Imagen izquierda, texto derecha -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
                    <div>
                        <img src="<?php echo esc_url($image_url); ?>" alt="" style="width: 100%; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    </div>
                    <div style="color: #4b5563; line-height: 1.9; font-size: 1rem;"><?php echo wp_kses_post($text); ?></div>
                </div>
            
            <?php elseif ($layout === 'image-right'): ?>
                <!-- Texto izquierda, imagen derecha -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
                    <div style="color: #4b5563; line-height: 1.9; font-size: 1rem;"><?php echo wp_kses_post($text); ?></div>
                    <div>
                        <img src="<?php echo esc_url($image_url); ?>" alt="" style="width: 100%; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    </div>
                </div>
            
            <?php elseif ($layout === 'image-top'): ?>
                <!-- Imagen arriba, texto abajo -->
                <div style="text-align: center;">
                    <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 800px; width: 100%; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); margin-bottom: 30px;">
                    <div style="color: #4b5563; line-height: 1.9; font-size: 1rem; max-width: 800px; margin: 0 auto;"><?php echo wp_kses_post($text); ?></div>
                </div>
            
            <?php elseif ($layout === 'image-bottom'): ?>
                <!-- Texto arriba, imagen abajo -->
                <div style="text-align: center;">
                    <div style="color: #4b5563; line-height: 1.9; font-size: 1rem; max-width: 800px; margin: 0 auto 30px;"><?php echo wp_kses_post($text); ?></div>
                    <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 800px; width: 100%; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <style>
    @media (max-width: 768px) {
        .seo-block--image-left > div > div,
        .seo-block--image-right > div > div {
            grid-template-columns: 1fr !important;
        }
    }
    </style>
    <?php
}
