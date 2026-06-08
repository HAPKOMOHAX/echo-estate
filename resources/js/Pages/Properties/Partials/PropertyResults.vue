<script setup>
import PropertyCard from './PropertyCard.vue';
import PropertyPagination from './PropertyPagination.vue';

defineProps({
    properties: {
        type: Array,
        required: true,
    },
    meta: {
        type: Object,
        required: true,
    },
    loading: {
        type: Boolean,
        required: true,
    },
    errorMessage: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['page-change']);
</script>

<template>
    <section class="min-w-0">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Объекты
                </h2>

                <p class="text-sm text-gray-500">
                    Найдено: {{ meta.total }}
                </p>
            </div>
        </div>

        <el-alert
            v-if="errorMessage"
            :title="errorMessage"
            type="error"
            show-icon
            class="mb-4"
        />

        <el-skeleton v-if="loading" :rows="8" animated />

        <div v-else>
            <div
                v-if="properties.length"
                class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"
            >
                <PropertyCard
                    v-for="property in properties"
                    :key="property.id"
                    :property="property"
                />
            </div>

            <el-empty
                v-else
                description="Ничего не найдено"
            />

            <PropertyPagination
                :meta="meta"
                @change="emit('page-change', $event)"
            />
        </div>
    </section>
</template>