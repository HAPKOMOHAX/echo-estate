<script setup>
import { computed } from 'vue';

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    roomOptions: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update', 'reset']);

function updateFilter(key, value) {
    emit('update', {
        [key]: value,
    });
}

const title = computed({
    get: () => props.filters.title,
    set: (value) => updateFilter('title', value ?? ''),
});

const hasPhoto = computed({
    get: () => props.filters.has_photo,
    set: (value) => updateFilter('has_photo', value),
});

const rooms = computed({
    get: () => props.filters.rooms,
    set: (value) => updateFilter('rooms', value),
});

const area = computed({
    get: () => props.filters.area,
    set: (value) => updateFilter('area', value),
});

const sort = computed({
    get: () => props.filters.sort,
    set: (value) => updateFilter('sort', value),
});
</script>

<template>
    <aside class="h-fit rounded-lg bg-white p-5 shadow">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-900">
                Фильтр
            </h2>

            <el-button
                size="small"
                text
                @click="emit('reset')"
            >
                Сбросить
            </el-button>
        </div>

        <div class="mb-5">
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Название
            </label>

            <el-input
                v-model="title"
                placeholder="Например: квартира"
                clearable
            />
        </div>

        <div class="mb-5">
            <el-checkbox v-model="hasPhoto">
                Есть фото
            </el-checkbox>
        </div>

        <div class="mb-5">
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Количество комнат
            </label>

            <el-checkbox-group v-model="rooms">
                <div class="flex flex-wrap gap-x-4 gap-y-2">
                    <el-checkbox
                        v-for="room in roomOptions"
                        :key="room"
                        :label="room"
                    >
                        {{ room }}
                    </el-checkbox>
                </div>
            </el-checkbox-group>
        </div>

        <div class="mb-5">
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Площадь, м²
            </label>

            <el-slider
                v-model="area"
                range
                :min="0"
                :max="180"
                :step="1"
            />

            <div class="mt-2 text-sm text-gray-500">
                от {{ area[0] }} до {{ area[1] }} м²
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Сортировка
            </label>

            <el-select v-model="sort" class="w-full">
                <el-option label="По умолчанию" value="default" />
                <el-option label="Площадь по возрастанию" value="area_asc" />
                <el-option label="Площадь по убыванию" value="area_desc" />
                <el-option label="Комнаты по возрастанию" value="rooms_asc" />
                <el-option label="Комнаты по убыванию" value="rooms_desc" />
                <el-option label="Этаж по возрастанию" value="floor_asc" />
                <el-option label="Этаж по убыванию" value="floor_desc" />
                <el-option label="Сначала новые" value="created_desc" />
            </el-select>
        </div>
    </aside>
</template>