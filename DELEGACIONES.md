# 🎓 Sistema de Delegaciones de Cursos

## 📋 Descripción

Sistema que permite a los profesores titulares delegar permisos temporales o permanentes a otros profesores (reemplazos) para gestionar sus cursos cuando no están disponibles.

---

## 🔑 Casos de Uso

### 1. **Profesor Enfermo** 🤒

- **Situación**: Profesor con licencia médica por 7 días
- **Necesidad**: Otro profesor debe calificar las tareas de los estudiantes
- **Solución**: Delegación temporal con permiso solo de calificar

### 2. **Vacaciones Programadas** 🏖️

- **Situación**: Profesor en vacaciones por 2 semanas
- **Necesidad**: Reemplazo que responda preguntas y califique
- **Solución**: Delegación programada con múltiples permisos

### 3. **Asistente de Enseñanza** 👨‍🏫

- **Situación**: Curso grande que necesita ayuda permanente
- **Necesidad**: Asistente que responda preguntas de estudiantes
- **Solución**: Delegación permanente (sin fecha de expiración)

---

## 🏗️ Estructura de la Base de Datos

### Tabla `course_delegations`

```sql
id                  - ID único
course_id           - Curso al que aplica
delegated_by        - ID del profesor titular (quien delega)
delegated_to        - ID del profesor reemplazo (quien recibe)
permissions         - JSON array de permisos
starts_at           - Fecha/hora de inicio (opcional)
expires_at          - Fecha/hora de expiración (opcional)
reason              - Razón de la delegación (opcional)
is_active           - Si está activa o no
created_at/updated_at
```

---

## 🔐 Permisos Disponibles

| Permiso             | Código              | Descripción                     |
| ------------------- | ------------------- | ------------------------------- |
| Ver Curso           | `view_course`       | Ver contenido del curso         |
| Calificar           | `grade_assignments` | Calificar tareas de estudiantes |
| Responder Preguntas | `answer_questions`  | Responder dudas de estudiantes  |
| Editar Contenido    | `edit_content`      | Modificar lecciones y recursos  |
| Ver Estadísticas    | `view_analytics`    | Acceder a reportes del curso    |

---

## 🚀 Uso del API

### **1. Listar delegaciones de un curso**

```http
GET /admin/courses/{course}/delegations
Authorization: Bearer {token}
```

**Respuesta:**

```json
{
  "delegations": [
    {
      "id": 1,
      "course_id": 5,
      "delegated_by": {...},
      "delegated_to": {...},
      "permissions": ["view_course", "grade_assignments"],
      "starts_at": "2026-01-26 10:00:00",
      "expires_at": "2026-02-02 10:00:00",
      "reason": "Licencia médica",
      "is_active": true
    }
  ]
}
```

---

### **2. Crear una delegación**

```http
POST /admin/courses/{course}/delegations
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**

```json
{
    "delegated_to": 5,
    "permissions": ["view_course", "grade_assignments"],
    "starts_at": "2026-01-26 10:00:00",
    "expires_at": "2026-02-02 10:00:00",
    "reason": "Enfermedad temporal"
}
```

**Validaciones:**

- ✅ `delegated_to` debe existir y ser profesor
- ✅ No puedes delegarte a ti mismo
- ✅ Permisos deben ser válidos
- ✅ `expires_at` debe ser después de `starts_at`
- ✅ Solo el profesor titular o admin pueden delegar

---

### **3. Actualizar una delegación**

```http
PUT /admin/courses/{course}/delegations/{delegation}
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**

```json
{
    "permissions": ["view_course", "grade_assignments", "answer_questions"],
    "expires_at": "2026-02-09 10:00:00",
    "is_active": true
}
```

---

### **4. Revocar una delegación**

```http
POST /admin/courses/{course}/delegations/{delegation}/revoke
Authorization: Bearer {token}
```

**Efecto:**

- Marca `is_active = false`
- Establece `expires_at = now()`

---

### **5. Eliminar permanentemente**

```http
DELETE /admin/courses/{course}/delegations/{delegation}
Authorization: Bearer {token}
```

⚠️ **Solo admins pueden eliminar permanentemente**

---

### **6. Profesores disponibles para delegar**

```http
GET /admin/courses/{course}/delegations/available-teachers
Authorization: Bearer {token}
```

**Respuesta:**

```json
{
    "teachers": [
        {
            "id": 5,
            "name": "María González",
            "email": "maria@example.com"
        }
    ]
}
```

---

## 💻 Uso Programático

### **Verificar permisos en el código**

```php
use App\Models\CourseDelegation;

// Verificar si un usuario puede editar el curso
if ($course->userCanEdit($user)) {
    // Permitir edición
}

// Verificar si puede calificar
if ($course->userCanGrade($user)) {
    // Permitir calificar
}

// Verificar permiso específico
if ($course->userHasPermission($user, CourseDelegation::PERMISSION_EDIT_CONTENT)) {
    // Permitir acción
}
```

### **Obtener delegaciones activas**

```php
// Delegaciones activas del curso
$activeDelegations = $course->activeDelegations;

// Verificar si una delegación está activa
if ($delegation->isActive()) {
    // Está activa
}

// Verificar si tiene un permiso específico
if ($delegation->hasPermission('grade_assignments')) {
    // Tiene el permiso
}
```

---

## 🎯 Lógica de Permisos

### **Jerarquía de acceso:**

1. **Admin** ⭐⭐⭐
    - Acceso total a todos los cursos
    - Puede crear, modificar y eliminar delegaciones
    - Puede eliminar delegaciones permanentemente

2. **Profesor Titular** ⭐⭐
    - Acceso total a sus cursos
    - Puede crear y revocar delegaciones
    - Puede publicar el curso (delegados NO)

3. **Profesor Delegado** ⭐
    - Acceso según permisos asignados
    - NO puede publicar el curso
    - NO puede delegar a otros

4. **Estudiantes**
    - Sin acceso a delegaciones

---

## 📊 Ejemplos de Delegaciones

### **Ejemplo 1: Licencia Médica (7 días)**

```php
CourseDelegation::create([
    'course_id' => 1,
    'delegated_by' => 2, // Profesor Juan
    'delegated_to' => 5, // Profesor María
    'permissions' => ['view_course', 'grade_assignments'],
    'starts_at' => now(),
    'expires_at' => now()->addDays(7),
    'reason' => 'Licencia médica',
    'is_active' => true,
]);
```

### **Ejemplo 2: Vacaciones Programadas**

```php
CourseDelegation::create([
    'course_id' => 1,
    'delegated_by' => 2,
    'delegated_to' => 5,
    'permissions' => [
        'view_course',
        'grade_assignments',
        'answer_questions',
        'view_analytics'
    ],
    'starts_at' => now()->addDays(30),
    'expires_at' => now()->addDays(44),
    'reason' => 'Vacaciones programadas',
    'is_active' => true,
]);
```

### **Ejemplo 3: Asistente Permanente**

```php
CourseDelegation::create([
    'course_id' => 1,
    'delegated_by' => 2,
    'delegated_to' => 5,
    'permissions' => ['view_course', 'answer_questions'],
    'starts_at' => now(),
    'expires_at' => null, // Sin expiración
    'reason' => 'Asistente de enseñanza',
    'is_active' => true,
]);
```

---

## 🔒 Seguridad

### **Validaciones Implementadas:**

✅ Solo profesores pueden ser delegados  
✅ No puedes delegarte a ti mismo  
✅ Verificación de permisos en cada acción  
✅ Middleware de roles en rutas  
✅ Expiración automática de delegaciones  
✅ Historial completo de delegaciones

### **Prevención de Ataques:**

✅ Validación de IDs de usuarios  
✅ Verificación de roles en servidor (no frontend)  
✅ Uso de Spatie Permission (probado y seguro)  
✅ No se exponen datos sensibles

---

## 📝 Notas Importantes

1. **Delegaciones expiradas**: Se marcan como inactivas automáticamente por el scope `active()`
2. **Publicación de cursos**: Solo el profesor titular puede publicar (delegados NO)
3. **Múltiples delegaciones**: Un curso puede tener múltiples delegaciones activas simultáneas
4. **Historial**: Todas las delegaciones se conservan (incluso las revocadas) para auditoría

---

## 🧪 Testing

Usuarios de prueba creados:

```
Profesor Titular:
- Email: teacher@example.com
- Password: 12345678

Profesor Reemplazo:
- Email: maria.reemplazo@example.com
- Password: password

Admin:
- Email: admin@example.com
- Password: 12345678
```

**Delegaciones de ejemplo creadas:**

1. ✅ Temporal 7 días (calificar)
2. ✅ Programada (vacaciones en 30 días)
3. ✅ Permanente (asistente de enseñanza)

---

## 🎨 Próximos Pasos (UI)

Para implementar la interfaz visual, necesitarás crear:

1. **Modal de delegación** en la página de edición de curso
2. **Lista de delegaciones activas** con badges de estado
3. **Formulario para crear delegación** con:
    - Select de profesores disponibles
    - Checkboxes de permisos
    - Date pickers para fechas
    - Input para razón
4. **Indicador visual** cuando un profesor ve un curso delegado

---

**¿Necesitas ayuda con la UI o tienes alguna pregunta sobre el sistema?** 🚀
