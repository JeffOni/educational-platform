# PublicNavbar Component

Componente de navegación global moderno para vistas públicas de la plataforma educativa.

## Características

- ✨ Diseño moderno y responsivo
- 🎨 Soporte para tema claro/oscuro
- 🛒 Contador de carrito integrado
- 📱 Menú móvil con Sheet (drawer lateral)
- 🔐 Manejo automático de autenticación
- 🎯 Navegación principal (Inicio, Cursos)
- ⚡ Transiciones suaves y animaciones

## Uso Básico

```vue
<script setup lang="ts">
import PublicNavbar from '@/components/PublicNavbar.vue';
</script>

<template>
    <PublicNavbar />
</template>
```

## Props

### `cartCount`
- **Tipo:** `number`
- **Default:** `0`
- **Descripción:** Número de items en el carrito de compras

### `showRegister`
- **Tipo:** `boolean`
- **Default:** `true`
- **Descripción:** Muestra u oculta el botón de registro

### `transparent`
- **Tipo:** `boolean`
- **Default:** `false`
- **Descripción:** Hace el navbar transparente (útil para hero sections)

## Ejemplos

### Con contador de carrito

```vue
<PublicNavbar :cart-count="5" />
```

### Sin botón de registro

```vue
<PublicNavbar :show-register="false" />
```

### Navbar transparente

```vue
<PublicNavbar :transparent="true" />
```

### Ejemplo completo

```vue
<script setup lang="ts">
import PublicNavbar from '@/components/PublicNavbar.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const cartCount = computed(() => page.props.cartCount || 0);
</script>

<template>
    <PublicNavbar
        :cart-count="cartCount"
        :show-register="true"
        :transparent="false"
    />
</template>
```

## PublicLayout

También puedes usar el layout completo que incluye el navbar:

```vue
<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <!-- Tu contenido aquí -->
    </PublicLayout>
</template>
```

## Navegación Incluida

El navbar incluye los siguientes enlaces:

- **Inicio:** Redirige a `/`
- **Cursos:** Redirige a `/courses`
- **Carrito:** Redirige a `/cart` con badge del contador
- **Dashboard:** Aparece si el usuario está autenticado
- **Iniciar Sesión:** Aparece si el usuario NO está autenticado
- **Empezar Gratis:** Botón de registro (si `showRegister` es true)

## Responsive

- **Desktop (md+):** Muestra navegación completa con menú horizontal
- **Mobile:** Menú hamburguesa que abre un Sheet lateral con todas las opciones

## Estilos

El componente utiliza:
- Tailwind CSS para estilos
- shadcn/ui components (Button, Sheet, NavigationMenu)
- lucide-vue-next para iconos
- Soporte para modo oscuro nativo

## Dependencias

```json
{
  "@inertiajs/vue3": "^2.0.0",
  "lucide-vue-next": "^0.400.0",
  "reka-ui": "^2.0.0"
}
```

## Personalización

Para personalizar los enlaces de navegación, edita la constante `navLinks` en el componente:

```typescript
const navLinks = [
    { href: '/', label: 'Inicio' },
    { href: '/courses', label: 'Cursos' },
    // Agrega más enlaces aquí
];
```

## Notas

- El navbar es **fijo** (`position: fixed`) en la parte superior
- Utiliza `backdrop-blur` para efecto glassmorphism
- Se adapta automáticamente al estado de autenticación del usuario
- Compatible con Inertia.js routing
