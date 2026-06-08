<script setup>
import { Head } from '@inertiajs/vue3';
import { usePropertiesSearch } from './Composables/usePropertiesSearch';
import AppLayout from '@/Layouts/AppLayout.vue';
import PropertyFilters from './Partials/PropertyFilters.vue';
import PropertyResults from './Partials/PropertyResults.vue';

const {
    loading,
    errorMessage,
    properties,
    meta,
    filters,
    roomOptions,
    resetFilters,
    updateFilters,
    onPageChange,
} = usePropertiesSearch();
</script>

<template>
    <Head title="Поиск недвижимости" />

    <AppLayout title="Поиск недвижимости">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Поиск недвижимости
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Фильтрация объектов без перезагрузки страницы
                </p>
            </div>
        </template>

        <main class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 2xl:max-w-[1800px]">
                <div class="block space-y-6 lg:grid lg:grid-cols-[320px_1fr] lg:gap-6 lg:space-y-0 2xl:grid-cols-[380px_1fr]">
                    <PropertyFilters
                        :filters="filters"
                        :room-options="roomOptions"
                        @update="updateFilters"
                        @reset="resetFilters"
                    />

                    <PropertyResults
                        :properties="properties"
                        :meta="meta"
                        :loading="loading"
                        :error-message="errorMessage"
                        @page-change="onPageChange"
                    />
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped>
.property-title {
    display: -webkit-box;
    overflow: hidden;
    overflow-wrap: anywhere;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.property-description {
    display: -webkit-box;
    overflow: hidden;
    overflow-wrap: anywhere;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}
</style>