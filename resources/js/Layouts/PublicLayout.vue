<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="border-b border-gray-100 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 2xl:max-w-[1800px]">
                <div class="flex h-16 justify-between">
                    <div class="flex items-center">
                        <Link
                            :href="route('properties.index')"
                            class="text-lg font-semibold text-gray-900"
                        >
                            Поиск недвижимости
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <header
            v-if="$slots.header"
            class="bg-white shadow"
        >
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 2xl:max-w-[1800px]">
                <slot name="header" />
            </div>
        </header>

        <slot />
    </div>
</template>