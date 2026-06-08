<script setup>
defineProps({
    meta: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['change']);
</script>

<template>
    <div v-if="meta.total > meta.per_page" class="mt-8">
        <div class="flex max-w-full justify-start overflow-x-auto sm:hidden">
            <el-pagination
                background
                layout="prev, pager, next"
                :pager-count="5"
                :current-page="meta.current_page"
                :page-size="meta.per_page"
                :total="meta.total"
                @current-change="emit('change', $event)"
            />
        </div>

        <div class="hidden justify-center sm:flex 2xl:hidden">
            <el-pagination
                background
                layout="prev, pager, next"
                :pager-count="7"
                :current-page="meta.current_page"
                :page-size="meta.per_page"
                :total="meta.total"
                @current-change="emit('change', $event)"
            />
        </div>

        <div class="hidden justify-center 2xl:flex">
            <el-pagination
                background
                layout="prev, pager, next"
                :pager-count="9"
                :current-page="meta.current_page"
                :page-size="meta.per_page"
                :total="meta.total"
                @current-change="emit('change', $event)"
            />
        </div>
    </div>
</template>