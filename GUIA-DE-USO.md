# 📚 Guía de Uso - Flavor Travel Theme

## 🚀 Cómo Importar el Contenido de Demostración

### Paso 1: Ir al importador
1. Ve a **WordPress Admin → Herramientas → Importar**
2. Busca **"WordPress"** y haz clic en **"Instalar ahora"**
3. Una vez instalado, haz clic en **"Ejecutar importador"**

### Paso 2: Subir el archivo
1. Selecciona el archivo `demo-content.xml`
2. Haz clic en **"Subir archivo e importar"**
3. Asigna los posts al usuario admin
4. Marca **"Descargar e importar archivos adjuntos"**
5. Clic en **"Enviar"**

---

## ✏️ Cómo Crear Contenido Manualmente

### 📍 CREAR UN DESTINO

1. Ve a **Destinos → Añadir nuevo**

2. **Título:** Nombre del destino
   ```
   Ejemplo: Cusco - La Ciudad Imperial
   ```

3. **Contenido:** Descripción completa con formato
   ```
   ## Descubre la magia del Cusco
   
   Cusco, antigua capital del Imperio Inca...
   
   ### ¿Qué ver en Cusco?
   
   **Plaza de Armas:** El corazón de la ciudad...
   
   **Sacsayhuamán:** Impresionante fortaleza inca...
   ```

4. **Extracto:** Resumen corto (aparece en las tarjetas)
   ```
   Antigua capital del Imperio Inca, Cusco te cautivará 
   con su arquitectura colonial y ruinas milenarias.
   ```

5. **Imagen destacada:** Clic en "Establecer imagen destacada"
   - Sube una imagen de mínimo 1200x800px
   - Formato JPG optimizado para web

6. **Continente:** Selecciona en el panel derecho
   - América / Europa / Asia / etc.

7. **Publicar**

---

### 🌴 CREAR UN TOUR/PAQUETE

1. Ve a **Tours → Añadir nuevo**

2. **Título:** Nombre del paquete
   ```
   Ejemplo: Perú Mágico - 7 Días
   ```

3. **Contenido:** Itinerario detallado
   ```
   ## El viaje perfecto por Perú
   
   ### Itinerario
   
   **Día 1 - Lima:** Llegada y city tour...
   **Día 2 - Cusco:** Vuelo y aclimatación...
   
   ### Incluye
   - Vuelos internos
   - Hotel 4 estrellas
   - Desayunos diarios
   ```

4. **Extracto:** Resumen para tarjetas
   ```
   Lima, Cusco y Machu Picchu. 7 días perfectos 
   para descubrir la magia del Perú.
   ```

5. **Datos del Paquete (panel inferior):**
   - **Precio:** `1899` (solo número)
   - **Duración:** `7 días / 6 noches`

6. **Imagen destacada:** Foto atractiva del destino

7. **Continente:** Selecciona el correspondiente

8. **Publicar**

---

### 🏷️ CREAR UNA OFERTA

1. Ve a **Ofertas → Añadir nuevo**

2. **Título:** Con emoji para destacar
   ```
   Ejemplo: 🔥 Cusco + Machu Picchu - Oferta Especial
   ```

3. **Contenido:** Detalles de la oferta
   ```
   ## ¡Precio especial por tiempo limitado!
   
   ### ¿Qué incluye?
   
   ✅ 3 noches en Cusco - Hotel 4 estrellas
   ✅ Entrada a Machu Picchu
   ✅ Tren Vistadome
   ✅ Guía profesional
   
   ### Condiciones
   - Válido hasta marzo 2025
   - Sujeto a disponibilidad
   ```

4. **Extracto:** Gancho corto y atractivo
   ```
   4 días y 3 noches con TODO incluido. 
   ¡Ahorra $400 reservando ahora!
   ```

5. **Datos del Paquete (panel inferior):**
   - **Precio original:** `899`
   - **Precio oferta:** `499`
   - **Duración:** `4 días / 3 noches`
   - **Vigencia:** `2025-03-31` (formato YYYY-MM-DD)

6. **Imagen destacada:** Imagen llamativa

7. **Publicar**

---

## 🗺️ CREAR UN CONTINENTE

1. Ve a **Destinos → Continentes**

2. **Nombre:** 
   ```
   América
   ```

3. **Slug:** (se genera automático)
   ```
   america
   ```

4. **Descripción:**
   ```
   Desde la Patagonia hasta Alaska, descubre paisajes 
   únicos y culturas ancestrales.
   ```

5. **Imagen del continente:**
   - Agrega la URL de una imagen representativa
   - Ejemplo: `https://images.unsplash.com/photo-xxx`

6. **Añadir nuevo continente**

---

## ⚙️ PERSONALIZAR PÁGINAS

### Ir al Customizer
**Apariencia → Personalizar**

### Secciones disponibles:

| Sección | Qué controla |
|---------|--------------|
| 🗺️ Página Destinos | Banner de /destinos/ |
| 🌴 Página Tours | Banner de /tours/ |
| 🏷️ Página Ofertas | Banner de /ofertas/ |
| 👥 Página Nosotros | Banner de /nosotros/ |
| 📞 Página Contacto | Banner de /contacto/ |
| 🏠 Hero Principal | Slider del home |
| 🌍 Home - Continentes | Sección continentes |
| 🔥 Home - Ofertas | Sección ofertas |

### Opciones de cada página:

- **Título:** Texto principal
- **Descripción:** Subtítulo
- **Imagen de fondo:** URL de imagen
- **Posición contenido:** Arriba/Centro/Abajo
- **Mostrar badge:** Contador de items
- **Ocultar si vacío:** No mostrar "0 items"
- **Mostrar CTA:** Botón de acción
- **Texto del botón:** Personalizable
- **URL del botón:** Vacío = WhatsApp
- **Indicador scroll:** Flecha animada

---

## 📸 RECOMENDACIONES DE IMÁGENES

### Tamaños recomendados:

| Uso | Tamaño mínimo | Formato |
|-----|---------------|---------|
| Hero/Banner | 1920 x 1080 px | JPG |
| Imagen destacada | 1200 x 800 px | JPG |
| Continente | 800 x 1000 px | JPG |
| Logo | 200 x 200 px | PNG |

### Dónde conseguir imágenes gratis:
- [Unsplash](https://unsplash.com) - Fotos de alta calidad
- [Pexels](https://pexels.com) - Fotos y videos
- [Pixabay](https://pixabay.com) - Imágenes variadas

### Tips:
- Usa imágenes horizontales para banners
- Optimiza el peso (máximo 500KB)
- Usa herramientas como TinyPNG para comprimir

---

## 💬 CONFIGURAR WHATSAPP

1. Ve a **Personalizar → Contacto**
2. Ingresa el número sin espacios ni símbolos:
   ```
   00123456789
   ```
   (Código de país + número)

El botón de WhatsApp aparecerá automáticamente en:
- Header (desktop)
- Botón flotante (móvil)
- Botones CTA de los banners
- Página de contacto

---

## ❓ SOPORTE

¿Necesitas ayuda? Contacta al desarrollador:
- Email: soporte@ejemplo.com
- WhatsApp: +00 123 456 789
