# Architectural Standards & Guidelines

When writing or modifying code in this codebase, strictly follow these standards:

## 1. Single-Action Backend Controllers
- **Directory Grouping**: Organize controllers inside subfolders under `App\Http\Controllers\` based on major domain features (e.g., `App\Http\Controllers\Settings\`, `App\Http\Controllers\Auth\`, `App\Http\Controllers\Events\`).
- **Single-Action Rule**: Every controller must focus on a single operation using the `__invoke()` magic method. Do not group CRUD methods (`index`, `edit`, `update`, `destroy`) inside a single multi-method controller.
- **Naming Convention**: Name controllers after the specific action being performed (e.g., `EditProfileController`, `UpdateProfileController`, `DeleteProfileController`).
- **Inertia Responses**: Return typed Inertia responses (`Inertia::render()`) or redirect routes (`to_route()`) directly from the invokable action.

## 2. Frontend File Limits & Composition (Vue 3)
- **150-Line Hard Limit**: No single `.vue` file may exceed 150 lines of code. If a view approaches this limit, immediately extract logical chunks into sub-components.
- **Composables First**: Extract all non-UI logic (data fetching, state management, form handlings, validation setups) into separate composables (`resources/js/composables/useProfileSettings.ts`).
- **Modular Components**: Divide complex pages into smaller focused child components (e.g., `UpdatePasswordForm.vue`, `DeleteUserModal.vue`) stored under component subfolders.
- **Syntax**: Always use `<script setup lang="ts">` with TypeScript definitions for `defineProps` and `defineEmits`.

## 3. General Clean Code Standards
- **Form Request Classes**: Enforce Request Classes (`php artisan make:request`) for all incoming HTTP validations instead of validating inside controllers.
- **Data Masking**: Use Inertia resource rendering without exposing raw Eloquent models where possible.
