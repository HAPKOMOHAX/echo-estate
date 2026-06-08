<script setup>
defineProps({
    property: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <article class="overflow-hidden rounded-lg bg-white shadow">
        <div class="h-48 bg-gray-200 2xl:h-56">
            <img
                v-if="property.thumbnail_path || property.image_path"
                :src="property.thumbnail_path || property.image_path"
                :alt="property.title"
                class="h-full w-full object-cover"
                loading="lazy"
            >

            <div
                v-else
                class="flex h-full items-center justify-center text-sm text-gray-400"
            >
                Нет фото
            </div>
        </div>

        <div class="p-4">
            <h3 class="property-title mb-2 text-lg font-semibold text-gray-900">
                {{ property.title }}
            </h3>

            <div class="mb-3 text-sm text-gray-600">
                {{ property.rooms }} комн. ·
                {{ property.floor }} этаж ·
                {{ property.area }} м²
            </div>

            <p class="property-description text-sm text-gray-500">
                {{ property.description }}
            </p>
        </div>
    </article>
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