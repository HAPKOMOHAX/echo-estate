import { onMounted, reactive, ref, watch } from 'vue';

export function usePropertiesSearch() {
    const loading = ref(false);
    const errorMessage = ref('');
    const properties = ref([]);

    const meta = reactive({
        current_page: 1,
        per_page: 12,
        total: 0,
        last_page: 1,
    });

    const roomOptions = [1, 2, 3, 4, 5];

    const filters = reactive({
        title: '',
        has_photo: false,
        rooms: [],
        area: [0, 180],
        sort: 'default',
        page: 1,
        per_page: 12,
    });

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
    function updateFilters(changes) {
        Object.assign(filters, changes);
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
    

    return {
        loading,
        errorMessage,
        properties,
        meta,
        filters,
        roomOptions,
        resetFilters,
        updateFilters,
        onPageChange,
    };
}