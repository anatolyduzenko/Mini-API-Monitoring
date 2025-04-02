import { onMounted, ref } from 'vue';

type ChartColors = 'random' | 'system';

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getStoredChartColors = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return localStorage.getItem('chartColors') as ChartColors | null;
};

export function useChartColors() {
    const chartColors = ref<ChartColors>('system');

    onMounted(() => {
        const savedChartColors = localStorage.getItem('chartColors') as ChartColors | null;

        if (savedChartColors) {
            chartColors.value = savedChartColors;
        }
    });

    function updateChartColors(value: ChartColors) {
        chartColors.value = value;

        localStorage.setItem('chartColors', value);

        setCookie('chartColors', value);
    }

    function getRandomHSLColor(hueRange = [0, 360], saturation = 70, lightness = 50) {
        const hue = Math.floor(Math.random() * (hueRange[1] - hueRange[0])) + hueRange[0];
        return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
    }

    function getRandomHSLColors(count: number, hueRange = [0, 360], saturation = 70, lightness = 50) {
        return Array.from({ length: count }, () => getRandomHSLColor(hueRange, saturation, lightness));
    }

    return {
        chartColors,
        updateChartColors,
        getRandomHSLColor,
        getRandomHSLColors,
    };
}
