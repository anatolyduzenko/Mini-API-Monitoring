export function useChartColors() {
    function getRandomHSLColor(hueRange = [0, 360], saturation = 70, lightness = 50) {
        const hue = Math.floor(Math.random() * (hueRange[1] - hueRange[0])) + hueRange[0];
        return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
    }

    function getRandomHSLColors(count, hueRange = [0, 360], saturation = 70, lightness = 50) {
        return Array.from({ length: count }, () => getRandomHSLColor(hueRange, saturation, lightness));
    }

    return { getRandomHSLColor, getRandomHSLColors };
}
