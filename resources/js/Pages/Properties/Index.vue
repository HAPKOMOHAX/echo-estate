<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive, ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
const loading = ref(false);
const errorMessage = ref('');
const properties = ref([]);

const meta = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 12,
    total: 0,
});

const filters = reactive({
    title: '',
    has_photo: false,
    rooms: [],
    area: [0, 180],
    sort: 'default',
    per_page: 12,
    page: 1,
});

const roomOptions = [1, 2, 3, 4, 5];

let debounceTimer = null;

function debounce(callback, delay = 400) {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        callback();
    }, delay);
}

function readFiltersFromUrl() {
    const params = new URLSearchParams(window.location.search);

    filters.title = params.get('title') ?? '';
    filters.has_photo = params.get('has_photo') === '1';

    filters.rooms = params
        .getAll('rooms[]')
        .map((room) => Number(room))
        .filter(Boolean);

    filters.area = [
        Number(params.get('area_from') ?? 0),
        Number(params.get('area_to') ?? 180),
    ];

    filters.sort = params.get('sort') ?? 'default';
    filters.per_page = Number(params.get('per_page') ?? filters.per_page);
    filters.page = Number(params.get('page') ?? 1);
}

function resetFilters() {
    filters.title = '';
    filters.has_photo = false;
    filters.rooms = [];
    filters.area = [0, 180];
    filters.sort = 'default';
    filters.page = 1;
}

function buildParams() {
    const params = new URLSearchParams();

    if (filters.title) {
        params.set('title', filters.title);
    }

    if (filters.has_photo) {
        params.set('has_photo', '1');
    }

    filters.rooms.forEach((room) => {
        params.append('rooms[]', room);
    });

    params.set('area_from', filters.area[0]);
    params.set('area_to', filters.area[1]);
    params.set('sort', filters.sort);
    params.set('per_page', filters.per_page);
    params.set('page', filters.page);

    return params;
}

function syncUrl(params) {
    const query = params.toString();
    const url = query ? `${window.location.pathname}?${query}` : window.location.pathname;

    window.history.replaceState({}, '', url);
}

async function loadProperties() {
    loading.value = true;
    errorMessage.value = '';

    const params = buildParams();
    syncUrl(params);

    try {
        const response = await fetch(`/properties/search?${params.toString()}`, {
            headers: {
                Accept: 'application/json',
            },
        });

        if (! response.ok) {
            throw new Error('Не удалось загрузить объекты недвижимости');
        }

        const data = await response.json();

        properties.value = data.data;

        meta.current_page = data.current_page;
        meta.last_page = data.last_page;
        meta.per_page = Number(data.per_page);
        meta.total = data.total;
    } catch (error) {
        errorMessage.value = error.message || 'Не удалось загрузить объекты недвижимости';
        properties.value = [];
        meta.current_page = 1;
        meta.last_page = 1;
        meta.total = 0;
    } finally {
        loading.value = false;
    }
}

function onPageChange(page) {
    filters.page = page;
    loadProperties();
}

watch(
    () => [
        filters.title,
        filters.has_photo,
        filters.rooms.join(','),
        filters.area[0],
        filters.area[1],
        filters.sort,
    ],
    () => {
        debounce(() => {
            filters.page = 1;
            loadProperties();
        });
    }
);

onMounted(() => {
    readFiltersFromUrl();
    if (window.innerWidth >= 1920) {
        filters.per_page = 20;
    }
    loadProperties();
});
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
                    <aside class="h-fit rounded-lg bg-white p-5 shadow">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Фильтр
                            </h2>

                            <el-button
                                size="small"
                                text
                                @click="resetFilters"
                            >
                                Сбросить
                            </el-button>
                        </div>

                        <div class="mb-5">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Название
                            </label>

                            <el-input
                                v-model="filters.title"
                                placeholder="Например: квартира"
                                clearable
                            />
                        </div>

                        <div class="mb-5">
                            <el-checkbox v-model="filters.has_photo">
                                Есть фото
                            </el-checkbox>
                        </div>

                        <div class="mb-5">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Количество комнат
                            </label>

                            <el-checkbox-group v-model="filters.rooms">
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
                                v-model="filters.area"
                                range
                                :min="0"
                                :max="180"
                                :step="1"
                            />

                            <div class="mt-2 text-sm text-gray-500">
                                от {{ filters.area[0] }} до {{ filters.area[1] }} м²
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Сортировка
                            </label>

                            <el-select v-model="filters.sort" class="w-full">
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
                                <article
                                    v-for="property in properties"
                                    :key="property.id"
                                    class="overflow-hidden rounded-lg bg-white shadow"
                                >
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
                            </div>

                              <el-empty
                                v-else
                                description="Ничего не найдено"
                                />

                            <div v-if="meta.total > meta.per_page" class="mt-8">
                                <!-- Mobile -->
                                <div class="flex max-w-full justify-start overflow-x-auto sm:hidden">
                                    <el-pagination
                                        background
                                        layout="prev, pager, next"
                                        :pager-count="5"
                                        :current-page="meta.current_page"
                                        :page-size="meta.per_page"
                                        :total="meta.total"
                                        @current-change="onPageChange"
                                    />
                                </div>

                                <!-- Small / normal desktop -->
                                <div class="hidden justify-center sm:flex 2xl:hidden">
                                    <el-pagination
                                        background
                                        layout="prev, pager, next"
                                        :pager-count="7"
                                        :current-page="meta.current_page"
                                        :page-size="meta.per_page"
                                        :total="meta.total"
                                        @current-change="onPageChange"
                                    />
                                </div>

                                <!-- 2XL / 4K -->
                                <div class="hidden justify-center 2xl:flex">
                                    <el-pagination
                                        background
                                        layout="prev, pager, next"
                                        :pager-count="9"
                                        :current-page="meta.current_page"
                                        :page-size="meta.per_page"
                                        :total="meta.total"
                                        @current-change="onPageChange"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>
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